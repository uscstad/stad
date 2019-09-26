<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-table"></i> <b>Tablero</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-md-3"> 
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar Tarea" />
              </div>
            </div>
            <div class="col-md-3"> 
              <div class="form-group">
                <select name="searchTeacher" class="form-control" v-model="searchTeacher">
                  <option value="">Buscar Profesor</option>
                  <option :value="teacher.doc" v-for="teacher in teachers">{{teacher.name}}</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 text-right"> 
              <a href="#" class="btn btn-primary" @click="showCreate=true" v-if="auth == 'coordinators'">
                <i class="fas fa-plus-circle fa-lg"></i> Tareas
              </a>
            </div><br>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <!--<tr>
                  <th colspan="4" class="text-center" v-for="value in columns"> {{ value.name }} </th>
                </tr>-->
                <tr>
                  <th></th>
                </tr>
                <tr>
                  <td>
                    <div v-for="value in searchAll">
                      <div :class="'card border-'+ value.styles +' mb-3'" style="max-width: 18rem;">
                        <div class="card-header">
                          <div class="row">
                            <div class="col-md-6 text-right" v-if="auth == 'coordinators' || auth == 'teachers'">
                              <a @click="getMessages(value)"><i class="fas fa-envelope"></i></a>
                              <a @click="getMessages(value)"><i class="fas fa-envelope-open"></i></a>
                              <a @click="edit(value)"><i class="far fa-edit"></i></a>
                              <a v-on:click.prevent="deleteModal(value.tasks)"><i class="far fa-trash-alt"></i></a>
                              <a ><i class="fas fa-cog"></i></a>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <p class="card-title">{{ value.tasks.name }}</p>
                          <!--<p class="card-text">{{ key.tasks.description }}</p>-->
                        </div>
                        <div class="card-footer">
                          <p>Responsables:
                            <!-- Demas profesores -->
                            <img src="https://image.ibb.co/jw55Ex/def_face.jpg" class="img img-rounded img-fluid" width="25px" height="25px" :title="value.teachers.users.name +' '+ value.teachers.users.lastname" />

                          </p>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de las Tareas.</h4>
        </div>
      </div>
      <div class="card-footer">
        <div class="text-center">
        </div>
      </div>
    </div>
    <div v-if="showCreate">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> <b>Creación de Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="store">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label :class="'text-'+styles">Nombre *</label>
                          <input type="text" name="name" class="form-control" v-model="name">
                          <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Descripcion</label>
                          <textarea name="description" class="form-control" v-model="description" style="margin-top: 0px; margin-bottom: 0px; height: 124px; resize: none;"></textarea>
                          <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Tipo *</label>
                          <select class="form-control" name="type" v-model="type" @change="getTasks(type)">
                            <option value="individuals">Individuales</option>
                            <option value="groups">Colaborativas</option>
                          </select>
                          <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Profesor Encargado *</label>
                          <multiselect v-model="teachers_id" :options="teachers"  placeholder="Seleccione un Profesor" label="name" track-by="name"></multiselect>
                          <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label :class="'text-'+styles">Estilo *</label>
                          <select class="form-control" v-model="styles">
                            <option value="secondary">Secondary</p></option>
                            <option value="warning">Warning</option>
                            <option value="primary">Primary</option>
                            <option value="success">Success</option>
                            <option value="danger">Danger</option>
                          </select>
                          <span v-for="error in errors.styles" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Fecha Inicial *</label>
                          <datetime type="datetime" v-model="start_date"  input-class="form-control"></datetime>
                          <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Fecha Final *</label>
                          <datetime type="datetime" v-model="final_date"  input-class="form-control"></datetime>
                          <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label :class="'text-'+styles">Periodo *</label>
                          <multiselect v-model="teaching_periods_id" :options="teaching_periods" placeholder="Seleccione un Periodo" label="name" track-by="name"></multiselect>
                          <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group" v-if="showGroups">
                          <label :class="'text-'+styles">Profesores Colaborativos *</label>
                          <multiselect v-model="teachers_group" :options="teachers_groups" :multiple="true" placeholder="Seleccione Profesores" track-by="name" label="name"></multiselect>
                          <span v-for="error in errors.teachers_group" class="text-danger">{{ error }}</span>
                        </div> 
                      </div>
                    </div>
                      
                  </div>
                  <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Guardar">
                    <button type="button" class="btn btn-secondary" @click="cancel()">Cancelar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <div v-if="showUpdate">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> <b>Edición de Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Nombre *</label>
                          <input type="text" name="name" class="form-control" v-model="find.name">
                          <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Descripcion</label>
                          <textarea name="description" class="form-control" v-model="find.description" style="margin-top: 0px; margin-bottom: 0px; height: 124px; resize: none;"></textarea>
                          <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Tipo *</label>
                          <select class="form-control" name="type" v-model="find.type" @change="getTasks(type)">
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
                            placeholder="Seleccione un Profesor" 
                          ></multiselect>
                          <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label :class="'text-'+styles">Estilo *</label>
                          <select class="form-control" v-model="find.styles">
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
                          <datetime type="datetime" v-model="find.start_date"  input-class="form-control"></datetime>
                          <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Fecha Final *</label>
                          <datetime type="datetime" v-model="find.final_date"  input-class="form-control"></datetime>
                          <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Periodo *</label>
                          <multiselect v-model="find.teaching_periods_id" :options="teaching_periods" placeholder="Seleccione un Periodo" label="name" track-by="name"></multiselect>
                          <span v-for="error in errors.teaching_periods_id" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group" v-if="showGroups">
                          <label>Profesores Colaborativos *</label>
                          <multiselect v-model="find.teachers_group" :options="teachers_groups" :multiple="true" placeholder="Seleccione Profesores" track-by="name" label="name"></multiselect>
                          <span v-for="error in errors.teachers_group" class="text-danger">{{ error }}</span>
                        </div> 
                      </div>
                    </div>
                      
                  </div>
                  <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Guardar">
                    <button type="button" class="btn btn-secondary" @click="cancel()">Cancelar</button>
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
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong>{{find.name}} </strong> de las Tareas?</h4>
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
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong> </strong> de las Tareas?</h4>
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
        id_school_years: '',
        users_id: '',
        find: {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''},
        searchData: [],
        configs: {},
        auth:'',
        school_years: [],
        teaching_periods: [],
        teachers: [],
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
        var url = 'dashboards/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.id_school_years = response.data.school_years_id;  
          vm.searchData = response.data.tasks_contents;
          vm.auth = response.data.sesion;
          vm.users_id = response.data.users_id;
          //vm.getTeaching_period(response.data.config.school_years_id)
        });
      },
      getStatus(value){
        var url = 'tasks/getstatus/' + value.id;
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
      edit(value){
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
          'teachers_id': {'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname},
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
        var url = 'tasks/' + id;
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
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.find = {'id': '', 'name': '', 'description': '', 'status': 1, 'type': ''};
        this.showDelete = false;
        var url = 'tasks/' + value.id;
        axios.delete(url).then(response => {
          this.getAll(this.school_years_filter);
          toastr.success('Tarea Eliminado Correctamente');
        });
      },
      getMessages(value){
        this.messages = value.messages_contents;
        this.tasks_contents_id = value.id;
        this.showMessag = true;
      },
      storeMessages(){
        var url = 'messages';
        axios.post(url,{
          body: this.body,
          tasks_contents_id: this.tasks_contents_id,
        }).then(response => {
          this.body = '';
          this.getAll();
          this.getMessages(response.data);
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
        var url = 'dashboards/school_years';
        axios.get(url).then(response => {
          this.school_years = response.data;
        });
      },
      getTeaching_period(id){
        var url = 'dashboards/teaching_periods/' + id;
        axios.get(url).then(response => {
          this.teaching_periods = response.data;
        });
      },
      getTeachers(){
        var url = 'dashboards/teachers';
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
      },
      getPeriods(filter){

      }, 
      refreshMessage(){

      }
    },
    created(){ 
      this.getAll();
      this.getTeachers();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          let name = value.tasks.name.toLowerCase().includes(this.search.toLowerCase())
          let teacher = value.teachers.users.doc.includes(this.searchTeacher)
          return name && teacher;
        })
      }
    }
  }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
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