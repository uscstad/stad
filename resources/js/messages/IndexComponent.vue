<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-comments"></i> <b>Lista de Mensajes</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-12 col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar Mensaje" />
              </div>
            </div>
            <div class="col-12 col-sm-6 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
              <a href="#" class="btn btn-primary" @click="showCreate=true"><i class="fas fa-plus-circle fa-lg"></i> Mensaje</a>
            </div>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th scope="col">Estado</th>
                  <th scope="col">Estado</th>
                  <th scope="col">Mensaje</th>
                  <th scope="col">Remitentes</th>
                  <th scope="col">Tarea</th>
                  <th scope="col">Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, name, index) in searchAll">
                  <td class="text-center">
                    <i v-if="value.status" class="fas fa-envelope-open fa-2x"></i>
                    <i v-else class="fas fa-envelope fa-2x"></i>
                  </td>
                  <td>
                    <b v-if="value.users_id !== users_id">Recibido</b>
                    <b v-if="value.users_id === users_id">Enviado</b>
                  </td>
                  <td v-text="value.body"></td>  
                  <td>
                    <div v-for="senders in JSON.parse(value.senders)" v-if="value.users_id === users_id">
                      {{ senders.name }} {{ senders.lastname }}
                    </div>
                    <div v-if="value.users_id !== users_id">
                      {{ value.users.name }} {{ value.users.lastname }}
                    </div>
                  </td>
                  <td v-text="value.tasks_contents !== null ? value.tasks_contents.tasks.name +' ('+value.tasks_contents.tasks.id+')' : 'No Asignado'"></td>
                  <td v-text="value.created_at"></td>
                  <!--<td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>La bandeja de entrada esta vacia.</h4>
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
    <div v-if="showCreate">
      <transition body="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-comments"></i> <b>Enviar Mensaje</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="store">
                  <div class="modal-body" style="margin: 20px 0;max-height: 400px;overflow-y: auto;">

                    <div class="form-group">
                      <label>Tarea </label>
                      <multiselect v-model="tasks_id" :options="tasks" @input="getSenders" placeholder="Seleccione la Tarea" label="name" track-by="name"></multiselect>
                      <span v-for="error in errors.tasks_id" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Remitentes *</label>
                      <multiselect v-model="senders_ids" :options="senders" :multiple="true" placeholder="Seleccione los Remitentes" label="name" track-by="name"></multiselect>
                      <span v-for="error in errors.senders_ids" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Mensaje *</label>
                      <textarea name="name" class="form-control" v-model="body"></textarea>
                      <span v-for="error in errors.body" class="text-danger">{{ error }}</span>
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
      <transition body="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-comments"></i> <b>Editar Año lectivo</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body">

                    <div class="form-group">
                      <label>Nombre *</label>
                      <input type="text" body="body" class="form-control" v-model="find.body">
                      <span v-for="error in errors.body" class="text-danger">{{ error }}</span>
                    </div>
                    <div class="form-group">
                      <label>Descripcion</label>
                      <textarea body="senders" class="form-control" v-model="find.senders"></textarea>
                      <span v-for="error in errors.senders" class="text-danger">{{ error }}</span>
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
      <transition body="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title"><i class="fas fa-comments"></i> <b>Eliminar Año lectivo</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong>{{find.body}}</strong> de los Años lectivos?</h4>
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
        body: '',
        tasks_id: '',
        senders_ids: [],
        senders: [],
        tasks: [],
        find: {'id': '', 'body': '', 'senders': '', 'tasks_id': 1},
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
        users_id: '',
        search: '',
        searchData: [],
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
        var url = 'messages/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData = response.data.messages;
          vm.pagination = response.data.pagination;
          vm.users_id   = response.data.users_id;
        });
      },
      gettasks_id(value){
        var url = 'messages/gettasks_id/' + value.id;
        axios.put(url, value).then(response => {
          this.getAll();
          this.cancel();
          toastr.info('Se ha cambiado el estado del mensaje Correctamente');
        }).catch(error => {
          toastr.error('Algo ha salido mal contactate con el Administrador');
        });
      },
      store(){
        var url = 'messages';
        axios.post(url,{
          body: this.body,
          senders: this.senders_ids,
          tasks_contents_id: this.tasks_id.id,
        }).then(response => {
          this.getAll();
          this.body = '';
          this.senders_ids = '';
          this.tasks_id = 1;
          this.errors = [];
          this.showCreate = false;
          toastr.success('Mensaje enviado Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
        this.find.id = value.id;
        this.find.body = value.body;
        this.find.senders = value.senders;
        this.find.tasks_id = value.tasks_id;
        this.showUpdate = true;
      },
      update(id){
        var url = 'messages/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.find = {'id': '', 'body': '', 'senders': '', 'tasks_id': 1};
          this.errors = [];
          this.showUpdate = false;
          toastr.warning('Mensaje Actualizado Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      deleteModal(value){
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.find = {'id': '', 'body': '', 'senders': '', 'tasks_id': 1};
        this.showDelete = false;
        var url = 'messages/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.success('Mensaje Eliminado Correctamente');
        });
      },
      cancel(){
        this.body = '';
        this.senders_ids = '';
        this.tasks_id = 1;
        this.find = {'id': '', 'body': '', 'senders': '', 'tasks_id': 1};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
      },
      getTasks(){
        var url = 'messages/tasks';
        axios.get(url).then(response => {
          this.tasks = response.data;
        });
      },
      getAllSenders(){
        var url = 'messages/senders';
        axios.get(url).then(response => {
          this.senders = response.data;
        });
      },
      getSenders(value){
        this.senders_ids = '';
        var url = 'messages/senders/' + value.id;
        axios.get(url).then(response => {
          this.senders = response.data;
        });
      }
    },
    created(){ 
      this.getAll();
      this.getTasks();
      this.getAllSenders();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          return value.body.toLowerCase().includes(this.search.toLowerCase())
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