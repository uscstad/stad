<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::guest()){
		return view('auth.login');
	}else{
		session(['rol' => Auth::user()->type]);
		if(Auth::user()->type == 'support') {
			$users  = App\User::where('deleted',0)->count();
			$school_years  = App\School_years::where('deleted',0)->count();
			$teaching_periods  = App\Teaching_periods::where('deleted',0)->count();
			$tasks  = App\Tasks::where('deleted',0)->count();
	        return view('type.support', compact('users','school_years','teaching_periods','tasks'));
	    } else {
	    	//
	     	if(Auth::user()->type == 'admins') { 		    	
				$school_years  = App\School_years::where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->get();
				$school_years_id = [];
				foreach ($school_years as $value) {
					$school_years_id[] = (int) $value->id;
				}
				$school_years = count($school_years_id);
				$teaching_periods  = App\Teaching_periods::whereIn('school_years_id', $school_years_id)->where('deleted',0)->count();
				unset($school_years_id);
				$coordinators  = App\Coordinators::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->count();
				$teachers = App\Teachers::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->count();
				$level_educations = App\Level_educations::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->count();
            	$subjects = App\Subjects::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->count();
		    	return view('type.admins', compact('school_years','teaching_periods','coordinators','teachers','level_educations','subjects'));
		    } else if(Auth::user()->type == 'coordinators') {
				$tasks  = App\Tasks::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->get();
				$tasks_id = [];
				foreach ($tasks as $value) {
					$tasks_id[] = (int) $value->id;
				}
				unset($tasks);
				$tasks = count($tasks_id);
				$teachers = App\Teachers::where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted',0)->count();
				$tasks_contents = App\Tasks_contents::whereIn('tasks_id', $tasks_id)->where('deleted',0)->count();
				unset($tasks_id);
				$categories = App\Categories::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->count();
				//
		    	$messages_received = App\Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_received')->where('users_id', \Auth::user()->id)->where('deleted',0)->orderBy('id', 'desc');
        		//
        		$messages = App\Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_sent')->where('senders','like','%"id":'.\Auth::user()->id.'%')->where('deleted',0)->union($messages_received)->orderBy('id', 'desc')->count();
		    	return view('type.coordinators', compact('teachers','tasks','categories','tasks_contents','messages'));
		    } else if (Auth::user()->type == 'teachers') {
		    	$tasks_contents  = App\Tasks_contents::where('teachers_id',\Auth::user()->Teachers->id)->where('deleted',0)->count();
		    	$tcxts = App\Tcxts::where('teachers_id',\Auth::user()->Teachers->id)->where('deleted',0)->count();
		    	$tasks = $tasks_contents + $tcxts;
		    	//
		    	$messages_received = App\Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_received')->where('users_id', \Auth::user()->id)->where('deleted',0)->orderBy('id', 'desc');
        		//
        		$messages = App\Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_sent')->where('senders','like','%"id":'.\Auth::user()->id.'%')->where('deleted',0)->union($messages_received)->orderBy('id', 'desc')->count();
		    	//$messages = App\Messages_contents::where('users_id',\Auth::user()->id)->where('deleted',0)->count();
		    	return view('type.teachers', compact('tasks','messages'));
		    }
		}
	}
});

Route::group(['middleware' => 'web'], function () {

	Route::get('/change/{name}', 'HomeController@change')->name('change');

	Route::get('users/list', 'UsersController@list');
	Route::put('users/getstatus/{id}', 'UsersController@getstatus');
	Route::resource('users', 'UsersController');

	Route::get('tasks/getstatus', 'TasksController@getStatus');

	Route::middleware(['prefix'=>'admins', 'middleware' => 'admins'])->group(function () {

		Route::get('school_years/list', 'SchoolYearsController@list');
		Route::put('school_years/getstatus/{id}', 'SchoolYearsController@getstatus');
		Route::resource('school_years', 'SchoolYearsController');

		Route::get('teaching_periods/list', 'TeachingPeriodsController@list');
		Route::put('teaching_periods/getstatus/{id}', 'TeachingPeriodsController@getstatus');
		Route::get('teaching_periods/school_years', 'TeachingPeriodsController@getAllSchool_years');
		Route::resource('teaching_periods', 'TeachingPeriodsController');

		Route::get('coordinators/list', 'CoordinatorsController@list');
		Route::put('coordinators/getstatus/{id}', 'CoordinatorsController@getstatus');
		Route::resource('coordinators', 'CoordinatorsController');

		Route::get('teachers/list', 'TeachersController@list');
		Route::put('teachers/getstatus/{id}', 'TeachersController@getstatus');
		Route::resource('teachers', 'TeachersController');

		Route::get('settings/list', 'ConfigsController@list');
		Route::get('settings/school_years', 'ConfigsController@school_years');
		Route::get('settings/teaching_periods/{id}', 'ConfigsController@teaching_periods');
		Route::resource('settings', 'ConfigsController');

	});

	Route::middleware(['prefix'=>'coordinators', 'middleware' => 'coordinators'])->group(function () {

		Route::get('teachers/list', 'TeachersController@list');
		Route::put('teachers/getstatus/{id}', 'TeachersController@getstatus');
		Route::resource('teachers', 'TeachersController');

		Route::get('activities/list', 'ActivitiesController@list');
		Route::resource('activities', 'ActivitiesController');

		Route::get('level_educations/list', 'LevelEducationsController@list');
		Route::resource('level_educations', 'LevelEducationsController');

		Route::get('subjects/list', 'SubjectsController@list');
		Route::resource('subjects', 'SubjectsController');

		Route::get('tasks/list', 'TasksController@list');
		Route::put('tasks/getstatus/{id}', 'TasksController@getstatus');
		Route::get('tasks/teachers', 'TasksController@teachers');
		Route::resource('tasks', 'TasksController');

		Route::get('categories/list', 'CategoriesController@list');
		Route::resource('categories', 'CategoriesController');

		Route::get('messages/list', 'MessagesContentsController@list');
		Route::get('messages/tasks', 'MessagesContentsController@getTasks');
		Route::get('messages/senders', 'MessagesContentsController@getAllSenders');
		Route::get('messages/senders/{id}', 'MessagesContentsController@getSenders');
		Route::resource('messages', 'MessagesContentsController');

		Route::get('validations_tasks/list', 'ValidationsController@list');
		Route::resource('validations_tasks', 'ValidationsController');

	});

	Route::middleware(['prefix'=>'coordinators', 'middleware' => 'teachers'])->group(function () {

		Route::get('task/list', 'TaskController@list');
		Route::get('task/teachers', 'TaskController@teachers');
		Route::get('task/boards/{id}/{name}', 'TaskController@boards');
		Route::resource('task', 'TaskController');

		Route::get('messages/list', 'MessagesContentsController@list');
		Route::get('messages/tasks', 'MessagesContentsController@getTasks');
		Route::get('messages/senders', 'MessagesContentsController@getAllSenders');
		Route::get('messages/senders/{id}', 'MessagesContentsController@getSenders');
		Route::resource('messages', 'MessagesContentsController');

		Route::get('reports/task', 'ReportsController@task');
		Route::get('reports/task/list', 'ReportsController@task_list');

	});





	Route::get('admins/list', 'AdminsController@list');
	Route::put('admins/getstatus/{id}', 'AdminsController@getstatus');
	Route::resource('admins', 'AdminsController');

	Route::get('reports/hystoric', 'ReportsController@hystoric');
	Route::get('reports/hystoric/list', 'ReportsController@list_hystoric');
	Route::get('reports/hystoric/period/{id}', 'ReportsController@tasksByPeriod');
	Route::get('reports/list', 'ReportsController@list');
	Route::get('reports/teachers/{teacher_id}/{teaching_p_id}', 'ReportsController@reportByTeacher');
	Route::get('reports/periods', 'ReportsController@reportByPeriod');
	Route::get('reports/tasks', 'ReportsController@tasks');

	Route::get('reports/statitics', 'ReportsController@statitics');
	Route::resource('reports', 'ReportsController');

	Route::get('dashboards/list', 'DashboardsController@list');
	Route::get('dashboards/report', 'DashboardsController@report');
	Route::get('dashboards/report/list', 'DashboardsController@list_search');
	Route::get('dashboards/report/teacher/{id}', 'DashboardsController@tasksByTeacher');
	Route::get('dashboards/report/task/{name}', 'DashboardsController@tasksByName');
	Route::get('dashboards/manage', 'DashboardsController@manage');
	Route::get('dashboards/manage/indivial/{name}/{status}', 'DashboardsController@searchIndividualTasks');
	Route::get('dashboards/manage/colective/{name}/{status}', 'DashboardsController@searchColectiveTasks');
	Route::get('dashboards/school_years', 'DashboardsController@school_years');
	Route::get('dashboards/teaching_periods/{id}', 'DashboardsController@teaching_periods');
	Route::get('dashboards/teachers', 'DashboardsController@teachers');
	Route::get('dashboards/messages/{id}', 'DashboardsController@messages');
	Route::put('dashboards/getstatus/{id}', 'DashboardsController@getstatus');
	Route::get('dashboards/senders/{id}', 'DashboardsController@senders');
	Route::resource('dashboards', 'DashboardsController');

	Auth::routes();

	Route::get('tasks-contents/list', 'TasksContentsController@list');
	Route::resource('tasks-contents', 'TasksContentsController');

	Route::resource('responses', 'ResponsesController');
	Route::resource('prologues', 'ProloguesController');
	Route::resource('accepts', 'TasksIndividualsController');
	Route::resource('calification', 'TasksGroupsController');

});