<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-table"></i> <b>Busqueda</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-md-2"> 
              <div class="form-group">
                <h5>Nombre Tarea</h5>
              </div>
            </div>
            <div class="col-md-3"> 
              <div class="form-group">
                <input type="text" class="form-control" v-model="task_name" placeholder="Nombre Tarea" />
              </div>
            </div>
            <div class="col-md-2"> 
              <button type="button" style="width: 100%" class="btn btn-primary" v-on:click.prevent="searchByTask(task_name)">Buscar</button>
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="row">
            <div class="col-md-2"> 
              <div class="form-group">
                <h5>Docente</h5>
              </div>
            </div>
            <div class="col-md-3"> 
              <div class="form-group">
                <multiselect v-model="teacher_id" :options="teachers_list"  placeholder="Seleccione el Docente" label="name" track-by="name"></multiselect>
              </div>
            </div>
            <div class="col-md-2"> 
              <button type="button" style="width: 100%" class="btn btn-primary" v-on:click.prevent="searchByTeacher(teacher_id)">Buscar</button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="text-center">
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-calendar-alt"></i> <b>Lista de Tareas</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-md-12" v-if="tasks_contents.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Accion</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Descripcion</th>
                  <th scope="col">Responsables2</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Fecha Inicio</th>
                  <th scope="col">Fecha Fin</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in tasks_contents">
                  <td>
                    <a href="#" v-on:click.prevent="show(value)"><i class="far fa-eye fa-2x"></i></a>
                    <a href="#" v-on:click.prevent="edit(value)"><i class="far fa-edit fa-2x"></i></a>
                    <a href="#" v-on:click.prevent="deleteModal(value)"><i class="far fa-trash-alt fa-2x"></i></a>
                  </td>
                  <td><h5>{{ value.tasks.name }}</h5></td>
                  <td>{{ value.tasks.description != '' ? value.tasks.description : 'Sin descripcion' }}</td>
                  <td>
                    <ul style="margin: 9px;padding: 0;">
                      <li type="disc">{{ value.teachers.users.name + ' ' + value.teachers.users.lastname }}</li>
                      <li v-for="t in value.tcxts" type="circle">{{  t.teachers.users.name + ' ' + t.teachers.users.lastname }}</li>
                    </ul>
                  </td>
                  <td>{{ value.tasks.type }}</td>
                  <td>{{ value.start_date | getdate }}</td>
                  <td>{{ value.final_date | getdate }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado Tareas con los parametros de bsusqueda.</h4>
        </div>
      </div>
      <div class="card-footer">
        <div class="text-center">
          <button class="btn btn-secondary" v-on:click="fetchStories(pagination.prev_page_url)"
            :disabled="!pagination.prev_page_url">Anterior
          </button>
          <span>Pagina {{pagination.current_page}} de {{pagination.last_page}}</span>
          <button class="btn btn-secondary" v-on:click="fetchStories(pagination.next_page_url)"
            :disabled="!pagination.next_page_url">Siguiente
          </button>
        </div>
      </div>
    </div>
    <div v-if="showUpdate">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> <b>{{ view ? 'Visialización' : 'Edición' }} de Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" id="edit-form" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Nombre *</label>
                          <input type="text" name="name" class="form-control" v-model="find.name" v-bind:disabled="view">
                          <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Descripcion</label>
                          <textarea name="description" class="form-control" v-model="find.description" style="margin-top: 0px; margin-bottom: 0px; height: 124px; resize: none;" v-bind:disabled="view"></textarea>
                          <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Tipo *</label>
                          <select class="form-control" name="type" v-model="find.type" @change="getTasks(type)" v-bind:disabled="view">
                            <option value="individuals">Individuales</option>
                            <option value="groups">Colaborativas</option>
                          </select>
                          <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Profesor Encargado *</label>
                          <multiselect 
                            v-model="find.teachers_id" 
                            :options="teachers" 
                            label="name" 
                            track-by="id"
                            :disabled="view"
                            placeholder="Seleccione un Profesor" 
                          ></multiselect>
                          <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label :class="'text-'+styles">Estilo *</label>
                          <select class="form-control" v-model="find.styles" v-bind:disabled="view">
                            <option value="secondary">Secondary</p></option>
                            <option value="warning">Warning</option>
                            <option value="primary">Primary</option>
                            <option value="success">Success</option>
                            <option value="danger">Danger</option>
                          </select>
                          <span v-for="error in errors.styles" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Fecha Inicial *</label>
                          <datetime type="datetime" v-model="find.start_date"  input-class="form-control" :disabled="view"></datetime>
                          <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Fecha Final *</label>
                          <datetime type="datetime" v-model="find.final_date"  input-class="form-control" :disabled="view"></datetime>
                          <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Periodo *</label>
                          <multiselect v-model="find.teaching_periods_id" :options="teaching_periods" placeholder="Seleccione un Periodo" label="name" track-by="name" :disabled="view"></multiselect>
                          <span v-for="error in errors.teaching_periods_id" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group" v-if="showGroups">
                          <label>Profesores Colaborativos *</label>
                          <multiselect v-model="find.teachers_group" :options="teachers_groups" :multiple="true" placeholder="Seleccione Profesores" track-by="name" label="name" :disabled="view"></multiselect>
                          <span v-for="error in errors.teachers_group" class="text-danger">{{ error }}</span>
                        </div> 
                      </div>
                    </div>
                      
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cancel()">Cerrar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <div v-if="showDelete">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> <b>Eliminar Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h4 class="text-center">¿Està seguro de que desea eliminar a <strong>{{find.tasks.name}} </strong> de las Tareas Asignadas?</h4>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" v-on:click.prevent="deleteOne(find)">Eliminar</button>
                  <button type="button" class="btn btn-secondary" @click="cancel">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <div v-if="showConfig">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> <b>Eliminar Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h4 class="text-center">¿Està seguro de que desea eliminar a <strong> </strong> de las Tareas?</h4>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" @click="cancel">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <div v-if="showMessag">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-comments"></i> <b>Mensajeria</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="storeMessages">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                    <div class="msg_card_body" v-for="message in messages">
                      <div class="d-flex justify-content-start mb-4" v-if="users_id != message.users_id">
                        <div class="img_cont_msg">
                          <img src="https://image.ibb.co/jw55Ex/def_face.jpg" class="img img-rounded img-fluid" width="45px" height="45px" :title="message.users.name +' '+ message.users.lastname" />
                        </div>
                        <div class="msg_cotainer">
                          {{ message.body }}
                          <span class="msg_time">8:40 AM, Today</span>
                        </div>
                      </div>
                      <div class="d-flex justify-content-end mb-4" v-else="">
                        <div class="msg_cotainer_send">
                          {{ message.body }}
                          <span class="msg_time_send">8:55 AM, Today</span>
                        </div>
                        <div class="img_cont_msg">
                          <img src="https://image.ibb.co/jw55Ex/def_face.jpg" class="img img-rounded img-fluid" width="45px" height="45px" :title="message.users.name +' '+ message.users.lastname" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="input-group">
                      <input id="btn-input" type="text" name="body" v-model="body" class="form-control input-sm" placeholder="Enviar Mensaje..." />
                      <span class="input-group-btn">
                        <input type="submit" @change="refreshMessage" class="btn btn-primary" value="Enviar">
                      </span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div> 
  </div>
</template>
<script>
  import axios  from 'axios'
  import Multiselect from 'vue-multiselect'
  import moment from 'moment'
  import ToggleButton from 'vue-js-toggle-button'
  import Datetime from 'vue-datetime'
  
  import 'vue-datetime/dist/vue-datetime.css'

  Vue.use(ToggleButton);
  Vue.use(Datetime);

  export default { 
    data(){ 
      return { 
        name: '',
        description: '',
        type: 'individuals',
        teachers_id: {},
        teacher_id: '',
        task_name: '',
        styles: '',
        start_date: '',
        final_date: '',
        teaching_periods_id: {},
        search: '',
        searchTeacher: '',
        school_years_filter: '',
        teaching_periods_filter: '',
        body: '',
        tasks_contents_id: '',
        users_id: '',
        find: {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''},
        searchData: [],
        auth:'',
        view: false,
        school_years: [],
        teaching_periods: [],
        teachers: [],
        teachers_list: [],
        tasks_contents: [],
        teachers_groups: [],
        teachers_group: [],
        errors: [],
        messages: [],
        showCreate: false,
        showUpdate: false,
        showDelete: false,
        showConfig: false,
        showGroups: false,
        showMessag: false,
        pagination: {
          'prev_page_url':'',
          'next_page_url':'',
          'total': 0,
          'current_page': 0,
          'per_page': 0,
          'last_page': 0,
          'from': 0,
          'to': 0
        },
      }
    },
    components: {
      VPaginator: VuePaginator,
      Multiselect
    },
    methods: {
      since(time){
        return moment(time).fromNow(true);
      },
      getAll(){
        console.log('todo');
        this.task_name = 'Nuevo';
        this.searchByTask('Nuevo');
        var url = 'report/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          console.log("hello", response.data)
          vm.teachers_list = response.data.teachers;
          //vm.searchData = response.data.school_years.teaching_periods;
        });
      },
      searchByTask(task_name){
        let vm = this;
        if(task_name){
          axios.get('report/task/' + task_name).then(function (response) {
            console.log("hello2", response.data);
            vm.tasks_contents =  response.data.tasks.data;
            vm.pagination = response.data.pagination;
          });
        }
      },
      searchByTeacher(teacher_id){
        let vm = this;
        if(teacher_id){
          axios.get('report/teacher/' + teacher_id.id).then(function (response) {
            console.log("hello2", response.data);
            vm.tasks_contents =  response.data.tasks.data;
            vm.pagination = response.data.pagination;
          });
        }

      },
      getStatus(value){
        var url = '/tasks/getstatus/' + value.id;
        axios.put(url, value).then(response => {
          this.getAll();
          this.cancel();
          toastr.info('Se ha cambiado el estado de la TaeaCorrectamente');
        }).catch(error => {
          toastr.error('Algo ha salido mal contactate con el Administrador');
        });
      },
      store(){
        var url = 'tasks';
        axios.post(url,{
          name: this.name,
          description: this.description,
          type: this.type,
          teachers_id: this.teachers_id,
          styles: this.styles,
          start_date: this.start_date,
          final_date: this.final_date,
          teachers_group: this.showGroups ? this.teachers_group : "No Data",
          teaching_periods_id: this.teaching_periods_id,
        }).then(response => {
          this.getAll(this.school_years_filter);
          this.name = '';
          this.description = '';
          this.type = 'individuals';
          this.teachers_id = {};
          this.styles = '';
          this.start_date = '';
          this.final_date = '';
          this.teachers_group = [];
          this.teaching_periods_id = {};
          this.errors = [];
          this.showCreate = false;
          toastr.success('Nueva Tarea Creada Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      show(value){
        this.view = true;
        var array = [];
        if(value.tcxts != ""){
          $.each(value.tcxts, function(key, value){
            array.push({'id': value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname});
          })
        }
        this.find = {
          'id': value.tasks.id,
          'name': value.tasks.name,
          'description': value.tasks.description,
          'type': value.tasks.type,
          'teachers_id': value.teachers.users,
          'styles': value.styles,
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_group': value.tcxts != "" ? array : "No Data",
          'teaching_periods_id': value.teaching_periods,
        };
        if(value.tasks.type == 'groups'){
          this.showGroups = true;
        }
        this.showUpdate = true;
      },
      edit(value){
        this.view = false;
        var array = [];
        if(value.tcxts != ""){
          $.each(value.tcxts, function(key, value){
            array.push({'id': value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname});
          })
        }
        this.find = {
          'id': value.tasks.id,
          'name': value.tasks.name,
          'description': value.tasks.description,
          'type': value.tasks.type,
          'teachers_id': value.teachers.users,
          'styles': value.styles,
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_group': value.tcxts != "" ? array : "No Data",
          'teaching_periods_id': value.teaching_periods,
        };
        if(value.tasks.type == 'groups'){
          this.showGroups = true;
        }
        this.showUpdate = true;
      },
      update(id){
        var url = '/tasks/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll(this.school_years_filter);
          this.find = {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''};
          this.errors = [];
          this.showUpdate = false;
          toastr.warning('Tarea Actualizado Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      deleteModal(value){
        console.log(value);
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.find = {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''};
        this.showDelete = false;
        var url = '/tasks/' + value.id;
        axios.delete(url).then(response => {
          this.getAll(this.school_years_filter);
          toastr.success('Tarea Eliminado Correctamente');
        });
      },
      getMessages(id){
        var url = '/dashboards/messages/' + id;
        axios.get(url).then(response => {
          this.messages = response.data.data;
          this.users_id = response.data.users_id;
        });
        this.tasks_contents_id = id;
        this.showMessag = true;
      },
      storeMessages(){
        var url = 'messages';
        axios.post(url,{
          body: this.body,
          tasks_contents_id: this.tasks_contents_id,
        }).then(response => {
          this.body = '';
          console.log(response.data.tasks_contents_id);
          console.log(response.data);
          this.getMessages(response.data.tasks_contents_id);
          //this.tasks_contents_id = '';
          //this.errors = [];
          //this.showMessag = false;
          toastr.success('Nueva Tarea Creada Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      cancel(){
        this.name = '';
        this.description = '';
        this.status = 1;
        this.type = '';
        this.find = {'id': '', 'name': '', 'description': '', 'status': 1, 'type':''};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
        this.showConfig = false;
        this.showMessag = false;
      },
      getSchool_years(){
        var url = 'school_years';
        axios.get(url).then(response => {
          this.school_years = response.data;
        });
      },
      getTeaching_period(name){
        var url = 'teaching_periods/' + name;
        axios.get(url).then(response => {
          this.teaching_periods = response.data;
        });
      },
      getTeachers(){
        var url = 'teachers';
        axios.get(url).then(response => {
          this.teachers = response.data;
          this.teachers_groups = response.data;
        });
      },
      getTasks(type){
        if(type == 'groups'){
          this.showGroups = true;
        } else {
          this.showGroups = false;
        } 
      },
      showConfigs(id){
        this.showConfig = true;
      },
      getYears(filter){
        this.searchData = [];
        this.getAll(filter);
        console.log(filter);
      },
      getPeriods(filter){

      }
    },
    created(){ 
      var fecha = new Date();
      this.school_years_filter = fecha.getFullYear();
      var year = fecha.getFullYear();
      this.getAll(year);
      this.getSchool_years();
      this.getTeaching_period(year);
      this.getTeachers();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          return value.name.toLowerCase().includes(this.search.toLowerCase())
        })
      }
    },
    filters: {
        getdate: function (str) {
          return str.split('T')[0];
        }
      }
  }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
  input {
   background-color: red;
  }
  .msg_cotainer{
    margin-top: auto;
    margin-bottom: auto;
    margin-left: 10px;
    border-radius: 25px;
    background-color: #82ccdd;
    padding: 10px;
    position: relative;
  }
  .msg_cotainer_send{
    margin-top: auto;
    margin-bottom: auto;
    margin-right: 10px;
    border-radius: 25px;
    background-color: #78e08f;
    padding: 10px;
    position: relative;
  }
  .msg_time{
    position: absolute;
    left: 0;
    bottom: -15px;
    font-size: 10px;
  }
  .msg_time_send{
    position: absolute;
    right:0;
    bottom: -15px;
    font-size: 10px;
  }
  .msg_head{
    position: relative;
  }
  .containerx{
    padding-bottom: 70px;
  }
  .modal-body {
    max-height: calc(80% - 100px);
    overflow-y: scroll;
  }
  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
  }
  .modal-wrapper {
    display: table-cell;
    vertical-align: middle;
  }
  
</style>