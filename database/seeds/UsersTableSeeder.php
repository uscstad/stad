<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//
        factory(App\User::class, 100)->create()->each(function ($user) {
	        $user->admins()->save(factory(App\Admins::class)->make());
	        $user->coordinators()->save(factory(App\Coordinators::class)->make());
	        $user->teachers()->save(factory(App\Teachers::class)->make());
	        $user->auxiliaries()->save(factory(App\Auxiliaries::class)->make());
	    });

	    /*$data = [
    		[
    			'name'			=> '2018',
    			'description'	=> 'Sin descripcion',
    			'created_at'    => new DateTime,
                'updated_at'    => new DateTime,
    		],
    		[
    			'name'			=> '2019',
    			'description'	=> 'Sin descripcion',
    			'created_at'    => new DateTime,
                'updated_at'    => new DateTime,
    		],
    		[
    			'name'			=> '2020',
    			'description'	=> 'Sin descripcion',
    			'created_at'    => new DateTime,
                'updated_at'    => new DateTime,
    		],
    	];
    	$schools = App\Schools_years::insert($data);
    	foreach ($schools as $value) {
    		for ($i=0; $i < 4; $i++) { 
    			App\Teaching_periods::create([
    				'name'			    => 'Periodo '.($i+1),
    				'description'	    => 'Sin descripcion',
    				'start_date' 	    => date('Y-m-d'),
    				'final_date' 	    => date('Y-m-d'),
    				'admins_id'		    => ($i+1),
    				'schools_years_id'	=> $value->id,
    			]);
    		}
    	}*/
	    //
	    factory(App\Tasks::class, 50)->create()->each(function ($tasks) {
	        $tasks->tasks_contents()->save(factory(App\Tasks_contents::class)->make());
	    });
	    //
	    //
	    /*factory(App\User::class, 100)->create()->each(function ($user) {
	        
	    });
	    //
	    factory(App\User::class, 100)->create()->each(function ($user) {
	        
	    });
	    */
    }
}
