<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <i class="fas fa-chart-bar"></i>
          Reporte Historico por Periodo Academico
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Año lectivo*</label>
              <select class="form-control" v-model="school_years_id" @change="getTeaching(school_years_id)">
                <option :value="school_year.id" v-for="school_year in school_years">{{school_year.name}}</option>
              </select>
              <span v-for="error in errors.school_years_id" class="text-danger">{{ error }}</span> 
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label> Periodo *</label>
              <select class="form-control" v-model="teaching_periods_id">
                <option :value="teaching_periods.id" v-for="teaching_periods in teaching_periods">{{teaching_periods.name}}</option>
              </select>
              <span v-for="error in errors.teaching_periods_id" class="text-danger">{{ error }}</span>

            </div>
          </div>
        </div>
        <br>
        <div class="text-center">
          <button type="button" class="btn btn-primary" v-on:click.prevent="search_by_school_year(school_years_id, teaching_periods_id)">Buscar</button>
          <button type="button" class="btn btn-secondary" v-on:click.prevent="cancelYears()">Cancelar</button>
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
                  <th scope="col">Responsables</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Fecha Inicio</th>
                  <th scope="col">Fecha Fin</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in tasks_contents">
                  <td>
                    <a href="#" v-on:click.prevent="edit(value)"><i class="far fa-edit fa-2x"></i></a>
                    <a href="#" v-on:click.prevent="deleteModal(value)" v-if="view"><i class="far fa-trash-alt fa-2x"></i></a>
                  </td>
                  <td><h5>{{ value.tasks.name }}</h5></td>
                  <td>{{ value.tasks.description != '' ? value.tasks.description : 'Sin descripcion' }}</td>
                  <td>
                    <ul style="margin: 9px;padding: 0;">
                      <li type="disc">{{ value.teachers.users.name + ' ' + value.teachers.users.lastname }}</li>
                      <li v-for="t in value.tcxts" type="circle">{{  t.teachers.users.name + ' ' + t.teachers.users.lastname }}</li>
                    </ul>
                  </td>
                  <td>{{ value.type != "groups" ? "Individuales" : "Colectivas" }}</td>
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
                          <label>Actividad *</label>
                          <multiselect 
                            v-model="find.activity" 
                            :options="activities" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione Actividad" 
                            :disabled="view == 0"
                          ></multiselect>
                          <span v-for="error in errors.activity" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Tipo *</label>
                          <multiselect 
                            v-model="find.type" 
                            :options="types" 
                            label="name" 
                            track-by="id"
                            @input="getTasks(type.id)"
                            placeholder="Seleccione Tipo" 
                            :disabled="view == 0"
                          ></multiselect>
                          <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Solicitar evidencia *</label>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" value="1" v-model="find.attachments" :disabled="view == 0">
                            <label class="form-check-label">Si</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" value="0" v-model="find.attachments" :disabled="view == 0">
                            <label class="form-check-label">No</label>
                          </div>
                          <span v-for="error in errors.attachments" class="text-danger">{{ error }}</span>
                        </div> 
                        <div class="form-group">
                          <label>Profesor Encargado *</label>
                          <multiselect 
                            v-model="find.teacher" 
                            :options="teachers" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione un Profesor" 
                            :disabled="view == 0"
                          ></multiselect>
                          <span v-for="error in errors.teacher" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Etiqueta</label>
                          <multiselect v-model="find.tags" :options="tags" :multiple="true" placeholder="Seleccione una fecha" track-by="name" label="name" :disabled="view == 0"></multiselect>
                          <span v-for="error in errors.tag" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">                        
                        <div class="form-group">
                          <label>Fecha Inicial *</label>
                          <datetime type="datetime" v-model="find.start_date"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="view == 0"></datetime>
                          <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Fecha Final *</label>
                          <datetime type="datetime" v-model="find.final_date"  input-class="form-control" placeholder="Seleccione una etiqueta" :disabled="view == 0"></datetime>
                          <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>¿Cual?</label>
                          <textarea name="comments" class="form-control" v-model="find.comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.attachments == 0 || view == 0"></textarea>
                          <span v-for="error in errors.comments" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Periodo *</label>
                          <input type="text" name="name" class="form-control" v-model="find.teaching_periods" disabled="">
                          <span v-for="error in errors.teaching_periods" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group" v-if="showGroups">
                          <label>Profesores Colaborativos *</label>
                          <multiselect v-model="find.teachers_group" :options="teachers_groups" :multiple="true" placeholder="Seleccione Profesores" track-by="name" label="name" :disabled="view == 0"></multiselect>
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
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
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
        types:[{'id': 'individuals' , name:'Individuales'},{'id': 'groups' , name:'Colaborativos'}],
        teachers_id: {},
        teacher_id: '',
        task_name: '',
        teaching_periods_name: '',
        teaching_periods_id: {},
        teaching_period_id: '',
        search: '',
        searchTeacher: '',
        school_years_filter: '',
        teaching_periods_filter: '',
        body: '',
        tasks_contents_id: '',
        users_id: '',
        teaching_p_id_bysc: '',
        school_years_id: '',
        statuses: [{enum:'processing', name:'Activa'}, {enum:'finish', name:'Finalizada'}],
        task_name_idv: '',
        task_name_col: '',
        task_status_idv: '',
        task_status_col: '',
        find: {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''},
        searchData: [],
        auth:'',
        view: false,
        school_years: [],
        teaching_periods: [],
        tags: [],
        teaching_periods_id: '',
        teaching_periods_bysc: [],
        teachers: [],
        teachers_list: [],
        tasks_contents: [],
        teachers_groups: [],
        teachers_group: [],
        errors: [],
        messages: [],
        activities: [],
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
        var url = 'hystoric/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.tags = response.data.tags;
          vm.school_years = response.data.school_years;
          vm.school_years_id = response.data.school_years_id;
          vm.teaching_periods = response.data.teaching_periods;
          vm.teaching_periods_name = response.data.teaching_periods[0].name;
          vm.teaching_periods_id = response.data.teaching_periods_id;
          vm.activities           = response.data.activities;
          vm.search_by_school_year(vm.school_years_id,vm.teaching_periods_id);
        });
      },
      search_by_school_year(school_year_id, teaching_p_id) {
        this.tasks_contents = [];
        let vm = this;
        axios.get('hystoric/period/' + teaching_p_id).then(function (response) {
          console.log(response.data);
          vm.view = response.data.auth;
          vm.tasks_contents =  response.data.tasks;
          vm.pagination = response.data.pagination;
        }); 
      },
      getTeaching(school_year_id){
        var url = '../dashboards/teaching_periods/' + school_year_id;
        axios.get(url).then(response => {
          this.teaching_periods = response.data;
        });
      },
      onSelect(){
        this.teaching_periods_bysc = [];
        for (var i = this.teaching_periods.length - 1; i >= 0; i--) {
          if(this.teaching_periods[i].school_years_id == this.school_years_id.id){
              this.teaching_periods_bysc.push(this.teaching_periods[i]);
              this.teaching_p_id_bysc = null;
          }
        }
        

      },
      edit(value){
        var array = [];
        if(value.tcxts != ""){
          $.each(value.tcxts, function(key, value){
            array.push({'id': value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname});
          })
        }
        console.log(this.teaching_periods_name);
        console.log(this.teaching_periods);
        this.find = {
          'id': value.id,
          'activity': {'id': value.tasks.id, 'name': value.tasks.name},
          'tasks_id': value.tasks.id,
          'type': {'id': value.type, 'name': value.type != 'individuals' ? 'Colaborativos' : 'Individuales'},
          'types': value.type,
          'teacher':{'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname},
          'teachers_id': value.teachers.id,
          'tags': value.tags != "" ? JSON.parse(value.tags) : "",
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_group': value.tcxts != "" ? array : "No Data",
          'teaching_periods': this.teaching_periods_name,
          'teaching_periods_id': this.teaching_periods_id,
          'attachments': value.attachments != 0 ? 1 : 0,
          'comments': value.comments,
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
      getTeachers(){
        var url = '../dashboards/teachers';
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