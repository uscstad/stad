<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-user-tie"></i> <b>Lista de docentes</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-12 col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar docentes" />
              </div>
            </div>
            <div class="col-12 col-sm-6 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
              <a href="#" class="btn btn-primary" @click="showCreate=true"><i class="fas fa-plus-circle fa-lg"></i> Profesor</a>
            </div>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Accion</th>
                  <th scope="col">Documento</th>
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
                  <td>{{ value.users.type_doc }} - {{ value.users.doc }}</td>
                  <td>{{ value.users.name }} {{ value.users.lastname }}</td>
                  <td>
                    <p>Usuario: <b>{{ value.users.user }}</b></p>
                    <p>Dirección: <b>{{ value.users.address }}</b></p>
                    <p>Celular: <b>{{ value.users.mobile }}</b></p>
                    <p>Telefono: <b>{{ value.users.phone }}</b></p>
                    <p>Nivel de educación: 
                      <div v-if="value.lexts != null"> 
                        <a href="#" class="btn btn-success btn-sm" v-for="key in value.lexts">
                          {{ key.level_educations.name }}
                        </a>
                      </div>
                      <div v-else="">
                        <b>No tiene.</b>
                      </div>  
                    </p>
                    <p>Asignatura: 
                      <div v-if="value.suxts != null"> 
                        <a href="#" class="btn btn-success btn-sm" v-for="key in value.suxts">
                          {{ key.subjects.name }}
                        </a>
                      </div>
                      <div v-else="">
                        <b>No tiene.</b>
                      </div>  
                    </p>
                  </td>   
                  <td>{{ value.users.created_at }}</td>
                  <!--<td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de Docentes.</h4>
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
                  <h4 class="modal-title"><i class="fas fa-user-tie"></i> <b>Creación de docentes</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="store">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Tipo de documento *</label>
                          <select name="type_doc" class="form-control" v-model="type_doc">
                            <option value="cc">Cedula de ciudadania</option>
                            <option value="ti">Tarjeta de Identidad</option>
                            <option value="pasaporte">Pasaporte</option>
                          </select>
                          <span v-for="error in errors.type_doc" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Nombre *</label>
                          <input type="text" name="name" class="form-control" v-model="name">
                          <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Usuario *</label>
                          <input type="text" name="user" class="form-control" v-model="user">
                          <span v-for="error in errors.user" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Contraseña *</label>
                          <input type="password" name="password" class="form-control" v-model="password">
                          <span v-for="error in errors.password" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Celular *</label>
                          <input type="text" name="mobile" class="form-control" v-model="mobile">
                          <span v-for="error in errors.mobile" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Profesión </label>
                          <input type="text" name="profession" class="form-control" v-model="profession">
                          <span v-for="error in errors.profession" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Capacitación </label>
                          <input type="text" name="training" class="form-control" v-model="training">
                          <span v-for="error in errors.training" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Nivel de educación *</label>
                          <multiselect 
                            v-model="level_education" 
                            :options="level_educations" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione Nivel de educación" 
                            :searchable="true"
                            :multiple="true"
                          ></multiselect>
                          <span v-for="error in errors.level_education" class="text-danger">{{ error }}</span>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Documento *</label>
                          <input type="text" name="doc" class="form-control" v-model="doc">
                          <span v-for="error in errors.doc" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Apellido *</label>
                          <input type="text" name="lastname" class="form-control" v-model="lastname">
                          <span v-for="error in errors.lastname" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Correo Electronico *</label>
                          <input type="email" name="email" class="form-control" v-model="email">
                          <span v-for="error in errors.email" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Direccion *</label>
                          <input type="text" name="address" class="form-control" v-model="address">
                          <span v-for="error in errors.address" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Telefono *</label>
                          <input type="text" name="phone" class="form-control" v-model="phone">
                          <span v-for="error in errors.phone" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Especialización </label>
                          <input type="text" name="specialty" class="form-control" v-model="specialty">
                          <span v-for="error in errors.specialty" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Maestria </label>
                          <input type="text" name="scale" class="form-control" v-model="scale">
                          <span v-for="error in errors.scale" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Asignatura *</label>
                          <multiselect 
                            v-model="subject" 
                            :options="subjects" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione Asignatura" 
                            :searchable="true"
                            :multiple="true"
                          ></multiselect>
                          <span v-for="error in errors.subject" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="fas fa-user-tie"></i> <b>Editar Profesor</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="update(find.id)">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Tipo de documento *</label>
                          <select name="type_doc" class="form-control" v-model="find.type_doc">
                            <option value="">Seleciona una Opción</option>
                            <option value="cc">Cedula de ciudadania</option>
                            <option value="ti">Tarjeta de Identidad</option>
                            <option value="pasaporte">Pasaporte</option>
                          </select>
                          <span v-for="error in errors.type_doc" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Nombre *</label>
                          <input type="text" name="name" class="form-control" v-model="find.name">
                          <span v-for="error in errors.name" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Usuario *</label>
                          <input type="text" name="user" class="form-control" v-model="find.user">
                          <span v-for="error in errors.user" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Contraseña *</label>
                          <input type="password" name="password" class="form-control" v-model="find.password">
                          <span v-for="error in errors.password" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Celular *</label>
                          <input type="text" name="mobile" class="form-control" v-model="find.mobile">
                          <span v-for="error in errors.mobile" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Profesión *</label>
                          <input type="text" name="profession" class="form-control" v-model="find.profession">
                          <span v-for="error in errors.profession" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Capacitación </label>
                          <input type="text" name="training" class="form-control" v-model="find.training">
                          <span v-for="error in errors.training" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Nivel de educación *</label>
                          <multiselect 
                            v-model="find.level_educations" 
                            :options="level_educations" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione Nivel de educación" 
                            :searchable="true"
                            :multiple="true"
                          ></multiselect>
                          <span v-for="error in errors.level_educations_id" class="text-danger">{{ error }}</span>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Documento *</label>
                          <input type="text" name="doc" class="form-control" v-model="find.doc">
                          <span v-for="error in errors.doc" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Apellido *</label>
                          <input type="text" name="lastname" class="form-control" v-model="find.lastname">
                          <span v-for="error in errors.lastname" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Correo Electronico *</label>
                          <input type="email" name="email" class="form-control" v-model="find.email">
                          <span v-for="error in errors.email" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Direccion *</label>
                          <input type="text" name="address" class="form-control" v-model="find.address">
                          <span v-for="error in errors.address" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Telefono *</label>
                          <input type="text" name="phone" class="form-control" v-model="find.phone">
                          <span v-for="error in errors.phone" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Especialización </label>
                          <input type="text" name="specialty" class="form-control" v-model="find.specialty">
                          <span v-for="error in errors.specialty" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Maestria </label>
                          <input type="text" name="scale" class="form-control" v-model="find.scale">
                          <span v-for="error in errors.scale" class="text-danger">{{ error }}</span>
                        </div>
                        <div class="form-group">
                          <label>Asignatura *</label>
                          <multiselect 
                            v-model="find.subjects" 
                            :options="subjects" 
                            label="name" 
                            track-by="id"
                            placeholder="Seleccione Asignatura" 
                            :searchable="true"
                            :multiple="true"
                          ></multiselect>
                          <span v-for="error in errors.subjects_id" class="text-danger">{{ error }}</span>
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
                  <h4 class="modal-title"><i class="fas fa-user-tie"></i> <b>Eliminar Profesor</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">
                  <h4 class="text-center">¿Esta seguro de que desea eliminar a <strong>{{find.users.name}}</strong> de los docentes?</h4>
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
        type_doc: 'cc',
        doc: '',
        name: '',
        lastname: '',
        user: '',
        email: '',
        password: '',
        address: '',
        mobile: '',
        training: '',
        phone: '',
        type: '',
        profession: '',
        specialty: '',
        scale: '',
        status: 1,
        find: {'id': '', 'type_doc': '', 'doc': '', 'name': '', 'lastname': '', 'user': '', 'email': '', 'password': '', 'address': '', 'mobile': '', 'training': '', 'phone': '', 'type': '', 'profession': '', 'specialty': '', 'scale': ''},
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
        level_education: null,
        level_educations_id: '',
        level_educations: [],
        subject: null,
        subjects_id: '',
        subjects: [],
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
        var url = 'teachers/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData       = response.data.teachers;
          vm.level_educations = response.data.level_educations;
          vm.subjects         = response.data.subjects;
          vm.pagination       = response.data.pagination;
        });
      },
      store(){
        var url = 'teachers';
        axios.post(url,{
          type_doc:          this.type_doc,
          doc:               this.doc,
          name:              this.name,
          lastname:          this.lastname,
          user:              this.user,
          email:             this.email,
          password:          this.password,
          address:           this.address,
          mobile:            this.mobile,
          phone:             this.phone,
          profession:        this.profession,
          specialty:         this.specialty,
          training:          this.training,
          scale:             this.scale,
          level_educations:  this.level_education,
          subjects:          this.subject,
        }).then(response => {
          this.getAll();
          this.cancel();
          toastr.success('Nuevo docente creado correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
        var level_educations = [];
        if(value.lexts != null){
          $.each(value.lexts, function(key, value){
            level_educations.push({'id':  value.level_educations.id, 'name': value.level_educations.name});
          })
        }
        var subjects = [];
        if(value.suxts != null){
          $.each(value.suxts, function(key, value){
            subjects.push({'id':  value.subjects.id, 'name': value.subjects.name});
          })
        }
        this.find = {   
          'id': value.id,
          'type_doc': value.users.type_doc,
          'doc': value.users.doc,
          'name': value.users.name,
          'lastname': value.users.lastname,
          'user': value.users.user,
          'email': value.users.email,
          'password': value.users.password,
          'address': value.users.address,
          'mobile': value.users.mobile,
          'phone': value.users.phone,
          'type': value.users.type,
          'profession': value.profession,
          'specialty': value.specialty,
          'training': value.training,
          'subjects': subjects,
          'subjects_id': value.suxts.subjects_id,
          'scale': value.scale,
          'level_educations': level_educations,
          'level_educations_id': value.lexts.level_educations_id,
        };
        this.showUpdate = true;
      },
      update(id){
        console.log(this.find);
        var url = 'teachers/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.cancel();
          toastr.warning('Docente actualizado correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      deleteModal(value){
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.cancel();
        var url = 'teachers/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.info('Docente eliminado correctamente');
        });
      },
      cancel(){
        this.type_doc = '';
        this.doc = '';
        this.name = '';
        this.lastname = '';
        this.user = '';
        this.email = '';
        this.password = '';
        this.address = '';
        this.mobile = '';
        this.phone = '';
        this.type = '';
        this.profession = '';
        this.specialty = '';
        this.level_education = null;
        this.subject = null;
        this.status = 1;
        this.find = {'id': '', 'type_doc': '', 'doc': '', 'name': '', 'lastname': '', 'user': '', 'email': '', 'password': '', 'address': '', 'mobile': '', 'phone': '', 'type': '', 'profession': '', 'specialty': '', 'status': 1};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
      },
    },
    created(){ 
      this.getAll();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          let searchText = value.users.name.toLowerCase() +' '+  value.users.lastname.toLowerCase()
          return searchText.includes(this.search.toLowerCase()) 
        })
      }
    },
    filters: {
      capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      }
    }
  }
</script>
<style>
  .containerx{
    padding-bottom: 70px;
  }
  .main {
  overflow: auto;
  background-color: #fff;
}

.main-text {
  height: 1000px;
}

.modal_open {
  position: fixed;
}

.modal-text {
  min-height: 400px
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

.modal-container {
  width: 300px;
  margin: 0px auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
  max-height: 200px;
  overflow-y: auto;
}

.modal-default-button {
  float: right;
}


/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
} 
</style>