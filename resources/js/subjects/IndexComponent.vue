<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="far fa-id-card"></i> <b>Asignaturas</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-12 col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar asignatura..." />
              </div>
            </div>
            <div class="col-12 col-sm-6 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
              <a href="#" class="btn btn-primary" @click="showCreate=true"><i class="fas fa-plus-circle fa-lg"></i> Asignatura</a>
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
                  <th scope="col">Creación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in searchAll">
                  <td class="group">
                    <a href="#" v-on:click.prevent="edit(value)" title="Actualizar" data-toggle="tooltip"><i class="far fa-edit fa-2x"></i></a>
                    <a href="#" v-on:click.prevent="deleteModal(value)" title="Eliminar" data-toggle="tooltip"><i class="far fa-trash-alt fa-2x"></i></a>
                  </td>
                  <td>{{ value.name }}</td>
                  <td>{{ value.description != '' ? value.description : 'Sin descripcion' }}</td>  
                  <td>{{ value.created_at }}</td>
                  <!--<td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de las Asignaturas.</h4>
        </div>
      </div>
      <div class="card-footer">
        <div class="text-center" v-if="searchAll.length > 0">
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
    <div v-if="showCreate">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="far fa-id-card"></i> <b>Creación de Asignatura</b></h4>
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
                      <textarea name="description" class="form-control" v-model="description" style="margin-top: 0px; margin-bottom: 0px; height: 124px; resize: none;"></textarea>
                      <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="far fa-id-card"></i> <b>Edición de Asignatura</b></h4>
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
                      <textarea name="description" class="form-control" v-model="find.description" style="margin-top: 0px; margin-bottom: 0px; height: 124px; resize: none;"></textarea>
                      <span v-for="error in errors.description" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="far fa-id-card"></i> <b>Eliminar Asignatura</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong>{{find.name}} </strong> de las Asignaturas?</h4>
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
        name: '',
        description: '',
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
        find: {'id': '', 'name': '', 'description': ''},
        searchData: [],
        errors: [],
        showCreate: false,
        showUpdate: false,
        showDelete: false,
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
        var url = 'subjects/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData = response.data.subjects;
          vm.pagination = response.data.pagination;
        });
      },
      store(){
        var url = 'subjects';
        axios.post(url,{
          name: this.name,
          description: this.description,
        }).then(response => {
          this.getAll();
          this.name = '';
          this.description = '';
          this.errors = [];
          this.showCreate = false;
          toastr.success('Nueva asignatura creada correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
        this.find = {
          'id': value.id,
          'name': value.name,
          'description': value.description,
        };
        this.showUpdate = true;
      },
      update(id){
        var url = 'subjects/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.find = {'id': '', 'name': '', 'description': ''};
          this.errors = [];
          this.showUpdate = false;
          toastr.warning('Asignatura actualizada correctamente');
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
        var url = 'subjects/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.info('Asignatura eliminada correctamente');
        });
      },
      cancel(){
        this.name = '';
        this.description = '';
        this.find = {'id': '', 'name': '', 'description': ''};
        this.errors = [];
        this.disabled = false;
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
        this.showGroups = false;
        this.showAssign = false;
      },
    },
    created(){ 
      this.getAll();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          return value.name.toLowerCase().includes(this.search.toLowerCase())
        })
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