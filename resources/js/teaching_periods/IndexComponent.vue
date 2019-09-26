<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-calendar"></i> <b>Lista de Periodos</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-12 col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar Periodos" />
              </div>
            </div>
            <div class="col-12 col-sm-6 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
              <a href="#" class="btn btn-primary" @click="showCreate=true"><i class="fas fa-plus-circle fa-lg"></i> Periodo</a>
            </div>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Accion</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Información</th>
                  <th scope="col">Creación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in searchAll">
                  <td>
                    <a href="#" v-on:click.prevent="edit(value)"><i class="far fa-edit fa-2x"></i></a>
                    <a href="#" v-on:click.prevent="deleteModal(value)"><i class="far fa-trash-alt fa-2x"></i></a>
                  </td>
                  <td>
                    <h5>{{ value.name }}</h5>
                    <p>{{ value.description != '' ? value.description : 'Sin descripcion' }}</p>
                  </td>
                  <td>
                    <p style="margin: 3px 0 3px 0;">Año lectivo: <b>{{ value.names_y }}</b></p>
                    <p style="margin: 3px 0 3px 0;">Fecha Inicio: <b>{{ value.start_date | getdate  }}</b></p>
                    <p style="margin: 3px 0 3px 0;">Fecha Final: <b>{{ value.final_date | getdate  }}</b></p>
                  </td>  
                  <td>{{ value.created_at }}</td>
                  <!--<td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de Periodos.</h4>
        </div>
      </div>
      <div class="card-footer">
        <div class="text-center" v-if="searchAll.length > 0">
          <button class="btn btn-secondary" v-on:click="fetchStories(pagination.prev_page_url)"
            :disabled="!pagination.prev_page_url">Anterior
          </button>
          <span>Total {{pagination.total}} Pagina {{pagination.current_page}} de {{pagination.last_page}}</span>
          <button class="btn btn-secondary" v-on:click="fetchStories(pagination.next_page_url)"
            :disabled="!pagination.next_page_url">Siguiente
          </button>
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
                  <h4 class="modal-title"><i class="fas fa-calendar"></i> <b>Creación de Periodos</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="store">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="form-group">
                      <label>Nombre *</label>
                      <input type="text" name="name" class="form-control" v-model="name">
                      <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Descripcion</label>
                      <textarea name="description" class="form-control" v-model="description"></textarea>
                      <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Fecha Inicio *</label>
                      <datetime type="datetime" v-model="start_date" :min-datetime="new Date(current_date).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha"></datetime>
                      <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Fecha Final *</label>
                      <datetime type="datetime" v-model="final_date" :min-datetime="new Date(current_date).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha"></datetime>
                      <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Año lectivo *</label>
                      <select class="form-control" v-model="school_years_id">
                        <option value="">Seleciona una Opción</option>
                        <option :value="school_year.id" v-for="school_year in school_years">{{ school_year.name }}</option>
                      </select>
                      <span v-for="error in errors.school_years_id" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="fas fa-calendar"></i> <b>Editar Periodo</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="form-group">
                      <label>Nombre *</label>
                      <input type="text" name="name" class="form-control" v-model="find.name">
                      <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Descripcion</label>
                      <textarea name="description" class="form-control" v-model="find.description"></textarea>
                      <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
                    </div> 
                    <div class="form-group">
                      <label>Fecha Inicio *</label>
                      <datetime type="datetime" v-model="find.start_date" :min-datetime="new Date(current_date).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha"></datetime>
                    
                      <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Fecha Final *</label>
                      <datetime type="datetime" v-model="find.final_date" :min-datetime="new Date(current_date).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha"></datetime>
                      <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Año lectivo *</label>
                      <select class="form-control" v-model="find.school_years_id">
                        <option value="">Seleciona una Opción</option>
                        <option :value="school_year.id" v-for="school_year in school_years">{{ school_year.name }}</option>
                      </select>
                      <span v-for="error in errors.school_years_id" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="fas fa-calendar"></i> <b>Eliminar Periodo</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong>{{find.name}}</strong> de los Periodos?</h4>
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
  import moment from 'moment'
  import ToggleButton from 'vue-js-toggle-button'
 
  Vue.use(ToggleButton);

  export default { 
    data(){ 
      return { 
        name: '',
        description: '',
        start_date: '',
        final_date: '',
        status: 1,
        school_years_id: '',
        school_years: [],
        current_date: '',
        find: {'id': '', 'name': '', 'description': '', 'start_date': '', 'final_date': '', 'status': 1, 'school_years_id': ''},
        errors: [],
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
        searchData: [],
        showCreate: false,
        showUpdate: false,
        showDelete: false,
      }
    },
    components: {
      VPaginator: VuePaginator
    },
    methods: {
      since(time){
        return moment(time).fromNow(true);
      },
      getAll(){
        var url = 'teaching_periods/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData = response.data.teaching_periods.data;
          vm.pagination = response.data.pagination;
          vm.current_date = response.data.current_date;
        });
      },
      getStatus(value){
        var url = 'teaching_periods/getstatus/' + value.id;
        axios.put(url, value).then(response => {
          this.getAll();
          this.cancel();
          toastr.info('Se ha cambiado el estado del periodo Correctamente');
        }).catch(error => {
          toastr.error('Algo ha salido mal contactate con el Administrador');
        });
      },
      store(){
        var url = 'teaching_periods';
        axios.post(url,{
          name: this.name,
          description: this.description,
          start_date: this.start_date ? this.start_date.substring(0, 19).replace('T', ' ') : '',
          final_date: this.final_date ? this.final_date.substring(0, 19).replace('T', ' ') : '',
          status: this.status,
          school_years_id: this.school_years_id,
        }).then(response => {
          this.getAll();
          this.name = '';
          this.description = '';
          this.start_date = '';
          this.final_date = '';
          this.status = 1;
          this.school_years_id = '';
          this.errors = [];
          this.showCreate = false;
          toastr.success('Nuevo periodo creado correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
        this.find.id = value.id;
        this.find.name = value.name;
        this.find.description = value.description;
        this.find.start_date = value.start_date;
        this.find.final_date = value.final_date;
        this.find.status = value.status;
        this.find.school_years_id = value.ids_y;
        this.showUpdate = true;
      },
      update(id){
        var url = 'teaching_periods/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.find = {'id': '', 'name': '', 'description': '', 'start_date': '', 'final_date': '', 'status': 1, 'school_years_id': ''};
          this.errors = [];
          this.showUpdate = false;
          toastr.warning('Periodo actualizado correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      deleteModal(value){
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.find = {'id': '', 'name': '', 'description': '', 'start_date': '', 'final_date': '', 'status': 1, 'school_years_id': ''};
        this.showDelete = false;
        var url = 'teaching_periods/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.info('Periodo eliminado correctamente');
        });
      },
      cancel(){
        this.name = '';
        this.description = '';
        this.start_date = '';
        this.final_date = '';
        this.status = 1;
        this.school_years_id = '';
        this.find = {'id': '', 'name': '', 'description': '', 'start_date': '', 'final_date': '', 'status': 1, 'school_years_id': ''};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
      },
      getSchool_years(){
        var url = 'teaching_periods/school_years';
        let vm = this;
        axios.get(url).then(function (response) {
          vm.school_years = response.data;
        });
      },
    },
    created(){ 
      this.getAll();
      this.getSchool_years();
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