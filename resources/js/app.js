
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
//
Vue.component('users', require('./users/IndexComponent.vue').default);
//
Vue.component('school_years', require('./school_years/IndexComponent.vue').default);
//
Vue.component('teaching_periods', require('./teaching_periods/IndexComponent.vue').default);
//
Vue.component('tasks', require('./tasks/IndexComponent.vue').default);
//
Vue.component('coordinators', require('./coordinators/IndexComponent.vue').default);
//
Vue.component('teachers', require('./teachers/IndexComponent.vue').default);
//
Vue.component('admins', require('./admins/IndexComponent.vue').default);
//
Vue.component('dashboards', require('./dashboards/IndexComponent.vue').default);

Vue.component('dashboards_report', require('./dashboards/ReportComponent.vue').default);
//
Vue.component('dashboards_manage', require('./dashboards/ManageComponent.vue').default);
//
Vue.component('configs', require('./configs/IndexComponent.vue').default);
//
Vue.component('reports', require('./reports/IndexComponent.vue').default);
//
Vue.component('messages', require('./messages/IndexComponent.vue').default);
//
Vue.component('reports-tasks', require('./reports/TasksComponent.vue').default);
//
Vue.component('categories', require('./categories/IndexComponent.vue').default);
//
Vue.component('hystoric', require('./reports/HystoricComponent.vue').default);
//
Vue.component('task', require('./task/IndexComponent.vue').default);
//
Vue.component('activities', require('./activities/IndexComponent.vue').default);
//
Vue.component('level_educations', require('./level_educations/IndexComponent.vue').default);
//
Vue.component('subjects', require('./subjects/IndexComponent.vue').default);
//
Vue.component('validations', require('./validations/IndexComponent.vue').default);
//
Vue.component('reports-task', require('./reports/TaskComponent.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    /*created () {
		setInterval(() => {
			var url = 'tasks/getstatus';
	        axios.get(url).then(response => {
	          console.log("Refresh Tasks");
	        });
		}, 4000)
	}*/
});

