<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-clipboard-check"></i> <b>Validar tareas</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-12 col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar Tareas" />
              </div>
            </div>
            <div class="col-12 col-sm-6 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
            </div>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Accion</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Descripcion</th>
                  <th style="width: 30%;">Información</th>
                  <th scope="col">Creación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in searchAll">
                  <td class="group">
                    <a href="#" v-on:click.prevent="edit(value)" title="Actualizar" data-toggle="tooltip"><i class="far fa-edit fa-2x"></i></a>
                  </td>
                  <td>{{ value.tasks.name }}</td>
                  <td>{{ value.tasks.description != '' ? value.tasks.description : 'Sin descripcion' }}</td>  
                  <td>
                    <div v-if="value.type">
                      <p style="font-size:80%;">Tipo: <b>{{ value.type != "groups" ? "Individuales" : "Colectivas" }}</b></p>
                      <p style="font-size:80%;">Fecha Inicio: <b>{{ value.start_date | getdate }}</b></p>
                      <p style="font-size:80%;">Fecha Final: <b>{{ value.final_date | getdate }}</b></p>
                      <p style="font-size:80%;">Docentes: <b>{{ value.teachers.users.name }} {{ value.teachers.users.lastname }}</b></p>
                      <p style="font-size:80%;">Periodo: <b>{{ value.teaching_periods.name }}</b></p>
                      <p style="font-size:80%;">Estado: <b>{{ value.status | getStatus }}</b></p>
                      <p style="font-size:80%;" v-if="value.type == 'groups'">Colaboradores: <b v-for="value in value.tcxts"> {{ value.teachers.users.name }} {{ value.teachers.users.lastname }} </b></p>
                      <p style="font-size:80%;" v-else="">Colaboradores: <b>No</b></p>
                    </div>
                  </td>
                  <td>{{ value.created_at }}</td>
                  <!--<td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de las Tareas.</h4>
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
                  <h4 class="modal-title"><i class="fas fa-clipboard-check"></i> <b>Administrar Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"> General</a>
                        <a v-if="showAssign" class="nav-item nav-link" id="nav-individual-tab" data-toggle="tab" href="#nav-individual" role="tab" aria-controls="nav-individual" aria-selected="false"> Asignación Individual</a>
                        <a v-if="showGroups" class="nav-item nav-link" id="nav-groups-tab" data-toggle="tab" href="#nav-groups" role="tab" aria-controls="nav-groups" aria-selected="false"> Asignación Grupales</a>
                        <a class="nav-item nav-link" id="nav-task-tab" data-toggle="tab" href="#nav-task" role="tab" aria-controls="nav-task" aria-selected="false"> Ejecución de Tarea</a>
                        <a class="nav-item nav-link" id="nav-reprocessing-tab" data-toggle="tab" href="#nav-reprocessing" role="tab" aria-controls="nav-reprocessing" aria-selected="false">Reprogramación</a>
                      </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                      <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <br>
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
                                :disabled="lock"
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
                                :disabled="lock"
                              ></multiselect>
                              <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>Solicitar evidencia *</label>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="1" v-model="find.attachments" :disabled="lock">
                                <label class="form-check-label">Si</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="0" v-model="find.attachments" :disabled="lock">
                                <label class="form-check-label">No</label>
                              </div>
                              <span v-for="error in errors.attachments" class="text-danger">{{ error }}</span>
                            </div> 
                            <div class="form-group">
                              <label>Categorias</label>
                              <multiselect v-model="find.categories" :options="tags" :multiple="true" placeholder="Seleccione una fecha" track-by="name" label="name" :disabled="lock"></multiselect>
                              <span v-for="error in errors.categories" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">                        
                            <div class="form-group">
                              <label>Fecha Inicial *</label>
                              <datetime type="datetime" v-model="find.start_date"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="lock"></datetime>
                              <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>Fecha Final *</label>
                              <datetime type="datetime" v-model="find.final_date"  input-class="form-control" placeholder="Seleccione una etiqueta" :disabled="lock"></datetime>
                              <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>¿Cual?</label>
                              <textarea name="comments" class="form-control" v-model="find.comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.attachments == 0"></textarea>
                              <span v-for="error in errors.comments" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>Periodo *</label>
                              <input type="text" name="name" class="form-control" v-model="find.teaching_periods" :disabled="lock">
                              <span v-for="error in errors.teaching_periods" class="text-danger">{{ error }}</span>
                            </div>
                          </div><hr>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-individual" role="tabpanel" aria-labelledby="nav-individual-tab">
                        <br>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Profesor Encargado *</label>
                              <multiselect 
                                v-model="find.teacher" 
                                :options="teachers" 
                                label="name" 
                                track-by="id"
                                placeholder="Seleccione un Profesor" 
                                :disabled="lock"
                              ></multiselect>
                              <span v-for="error in errors.teacher" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group" >
                              <label>Nivel de educación </label>
                              <multiselect 
                                v-model="find.selectLevel" 
                                :options="level_educations" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                placeholder="No tiene un nivel de Educación" 
                                :disabled="lock"
                              ></multiselect>
                            </div>
                            <div class="form-group" >
                              <label>Asignación por Areas </label>
                              <multiselect 
                                v-model="find.selectSubje" 
                                :options="subjects" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                placeholder="No tiene una Asignatura" 
                                :disabled="lock"
                              ></multiselect>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-groups" role="tabpanel" aria-labelledby="nav-groups-tab">
                        <br>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Docente Lider *</label>
                              <multiselect 
                                v-model="find.teacher" 
                                :options="teachers_groups" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                placeholder="Seleccione un Docente" 
                                :disabled="lock"
                              ></multiselect>
                              <span v-for="error in errors.teacher" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>Profesores Asignados *</label>
                              <multiselect 
                                v-model="find.teachers_fulls" 
                                :options="teachers_groups" 
                                :multiple="true" 
                                placeholder="Seleccione Profesores" 
                                :disabled="lock"
                                track-by="name" 
                                label="name"
                              ></multiselect>
                              <span v-for="error in errors.teachers_fulls" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group" >
                              <label>Nivel de educación </label>
                              <multiselect 
                                v-model="find.selectLevel" 
                                :options="level_educations" 
                                label="name" 
                                track-by="id"
                                :disabled="lock"
                                placeholder="No tiene un nivel de Educación" 
                                disabled
                              ></multiselect>
                            </div>
                            <div class="form-group" >
                              <label>Asignación por Areas </label>
                              <multiselect 
                                v-model="find.selectSubje" 
                                :options="subjects" 
                                label="name" 
                                track-by="id"
                                :disabled="lock"
                                placeholder="No tiene una Asignatura" 
                                disabled
                              ></multiselect>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-reprocessing" role="tabpanel" aria-labelledby="nav-reprocessing-tab">
                        <br>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Nueva Fecha Final *</label>
                              <datetime type="datetime" v-model="find.new_final_date"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="find.prologue"></datetime>
                              <span v-for="error in errors.new_final_date" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Justificación</label>
                              <textarea name="find.justifications" class="form-control" v-model="find.justifications" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.prologue"></textarea>
                              <span v-for="error in errors.justifications" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6" v-if="!find.evidence && !find.prologue">
                            <div class="form-group">
                              <label>Evidencia</label>
                              <input type="file" name="evidence" class="form-control" v-on:change="onImageChange">
                              <span v-for="error in errors.image" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-2" v-else>
                            <div class="form-group">
                              <label>Evidencia</label>
                              <a :href="find.evidence" target="_blank" class="btn btn-primary">Ver</a>
                            </div>
                          </div>
                          
                        </div>  
                      </div>
                      <div class="tab-pane fade" id="nav-task" role="tabpanel" aria-labelledby="nav-task-tab">
                        <br>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>respuesta</label>
                              <textarea name="answer" class="form-control" v-model="find.answer" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.answered"></textarea>
                              <span v-for="error in errors.answer" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6" v-if="!find.answered">
                            <div class="form-group" >
                              <label>Anexo</label>
                              <input type="file" name="annexed" class="form-control" v-on:change="onImageChange">
                              <span v-for="error in errors.annexed" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-2" v-else>
                            <div class="form-group" v-if="find.annexed">
                              <label>Anexo</label>
                              <a :href="find.annexed" target="_blank" class="btn btn-primary">Ver</a>
                            </div>
                          </div>
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
                  <h4 class="modal-title"><i class="fas fa-clipboard-check"></i> <b>Eliminar Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Està seguro de que desea eliminar a <strong>{{find.name}} </strong> de las Tareas?</h4>
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
 
  Vue.use(ToggleButton);

  export default { 
    data(){ 
      return { 
        statitics: [],
        activity: null,
        activities_id: null,
        tasks_id: null,
        type: null,
        type_select: null,
        types:[
          {id: 'individuals', name:'Individuales'},
          {id: 'groups',      name:'Colaborativos'}
        ],
        categories: null,
        attachments: 0,
        teacher: null,
        tag: '',
        tags: [],
        start_date: '',
        new_final_date: '',
        final_date: '',
        teaching_periods: {},
        comments: '',
        justifications: '',
        evidence: '',
        teachers_group: '',
        lock: false,
        teachers_fulls: '',
        allLevel: 0,
        allSubje: 0,
        selectLevel: '',
        selectSubje: '',
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
        search: '',
        find: {},
        searchData: [],
        teaching_periods: '',
        teaching_periods_id: '',
        teachers: [],
        teachers_groups: [],
        errors: [],
        activities: [],
        level_educations: [],
        subjects: [],
        showCreate: false,
        showUpdate: false,
        showDelete: false,
        showAssign: false,
        showGroups: false,
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
        var url = 'validations_tasks/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData  = response.data.tasks;
          console.log(response.data.tasks);
          vm.pagination  = response.data.pagination;
        });
      },
      singleActivity(value){
        this.tasks_id = value.id;
      },
      singleType(value){
        this.type = value.id;
      },
      edit(value){
        var array = [];
        if(value.tcxts != null){
          $.each(value.tcxts, function(key, value){
            array.push({'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname});
          })
        }
        if(value.status  != 'assigned'){
          this.lock = true;
        }
        this.getTasks(value.type);
        this.find = {
          'id': value.id,
          'activity': {'id': value.tasks.id, 'name': value.tasks.name},
          'tasks_id': value.tasks.id,
          'type': {'id': value.type, 'name': value.type != 'individuals' ? 'Colaborativos' : 'Individuales'},
          'types': value.type,
          'teacher':{'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname},
          'teachers_id': value.teachers.id,
          'categories': value.categories != "" ? JSON.parse(value.categories) : "",
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_group': value.tcxts != "" ? array : "No Data",
          'teaching_periods': value.teaching_periods.name,
          'attachments': value.attachments != 0 ? 1 : 0,
          'comments': value.comments,
          'positive': value.positive,
          'accepted': value.prologues.length ? value.prologues[0].accepted : '',
          'prologue_id': value.prologues.length ? value.prologues[0].id : false,
          'justifications': value.prologues.length ? value.prologues[0].justifications : '',
          'evidence': value.prologues.length ? value.prologues[0].archiveName : '',
          'new_final_date': value.prologues.length ? value.prologues[0].final_date : '',
          'answered': value.answer ? true : false,
          'answer': value.answer,
          'annexed': value.archiveName,
        };
        if(value.type == 'groups'){
          this.showGroups = true;
        }
        this.showUpdate = true;
      },
      update(id){
        console.log(this.find);
        var url = 'tasks/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.cancel();
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
        this.find = {'id': '', 'name': '', 'description': ''};
        this.showDelete = false;
        var url = 'tasks/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.success('Tarea Eliminado Correctamente');
        });
      },
      getTasks(type){
        if(type.id == 'groups'){
          this.showGroups = true;
          this.showAssign = false;
        }
        if(type.id == 'individuals'){
          this.showAssign = true;
          this.showGroups = false;
        } 
        this.type = type.id;
      },
      cancel(){
        this.activity = null;
        this.type = null;
        this.attachments = 0;
        this.teacher = null;
        this.tag = [];
        this.start_date = '';
        this.final_date = '';
        this.comments = '';
        this.teachers_group = '';
        this.find = {};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
        this.showGroups = false;
      },
      getTeachers(){
        var url = 'dashboards/teachers';
        axios.get(url).then(response => {
          this.teachers = response.data;
          this.teachers_groups = response.data;
        });
      },
      onImageChange(e) {
          let files = e.target.files || e.dataTransfer.files;
          if (!files.length)
              return;
          this.createImage(files[0], e.target.name, files[0].name);
      },
      createImage(file, name, ext) {
        let reader = new FileReader();
        let vm = this;
        reader.onload = (e) => {
            if (name == 'evidence') {
              this.find.evidence =  ext + '/' + e.target.result;
            }
            else if (name == 'annexed') {
              this.find.annexed =  ext + '/' + e.target.result;
            }
        };
        reader.readAsDataURL(file);
      },
    },
    created(){ 
      this.getAll();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          return value.tasks.name.toLowerCase().includes(this.search.toLowerCase())
        })
      }
    },
    filters: {
      getdate: function (str) {
        return str.split('T')[0];
      },
      getStatus(status){
        if(status == 'assigned'){
          return 'Asignada';
        }
        if(status == 'processing'){
          return 'En proceso';
        }
        if(status == 'reprogrammed'){
          return 'Re programada';
        }
        if(status == 'canceled'){
          return 'Cancelada';
        }
        if(status == 'unfulfilled'){
          return 'Incumplida';
        }
        if(status == 'finishedtime'){
          return 'Cumplida Fuera de Tiempo';
        }
        if(status == 'finish'){
          return 'Cumplida';
        }
      }
    }
  }
</script>
<style>
  .containerx{
    padding-bottom: 70px;
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