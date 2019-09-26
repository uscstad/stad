<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <i class="fas fa-cogs"></i> <b> Configuracion</b>
      </div>
      <form method="POST" v-on:submit.prevent="store">
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
              <h3 class="text-center">Establecer Periodo Activo</h3><br>
              <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">Seleccionar Año</label>

                <div class="col-md-6">
                  <select v-model="school_years_id" class="form-control" @change="setSchool_years(school_years_id)">
                    <option :value="school_year.id" v-for="school_year in school_years">{{ school_year.name }}</option>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">Seleccionar Periodo</label>
                <div class="col-md-6">
                  <select v-model="teaching_periods_id" class="form-control">
                    <option :value="teaching_period.id" v-for="teaching_period in teaching_periods">{{ teaching_period.name }}</option>
                  </select>
                </div>
              </div>

              <div class="form-group row text-center">
                <div class="col-md-12">
                  <br><br>
                  <input type="submit" class="btn btn-primary" value="Establecer">
                  <a href="./" class="btn btn-primary">Cancelar</a>
                </div>
              </div>

            </div>
            <div class="col-md-2">    
            </div>
          </div>
        </div>
      </form>
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
        school_years_id: '',
        teaching_periods_id: '',
        admins_id: '',
        data: [],
        school_years: [],
        teaching_periods: [],
        status: 1,
        find: {'id': '', 'school_years_id': '', 'teaching_periods_id': ''},
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
        var url = 'settings/list';
        let vm = this;
        axios.get(url).then(function (response) {
          vm.admins_id = response.data.configs.admins_id;
          if(vm.school_years_id === ''){
            vm.school_years_id = response.data.configs.school_years_id;
            vm.teaching_periods_id = response.data.configs.teaching_periods_id;
            vm.school_years = response.data.school_years;
            vm.teaching_periods = response.data.teaching_periods;
            //vm.getTeaching_period(vm.school_years_id);
          } else {
            vm.teaching_periods = response.data.teaching_periods;
            //vm.getTeaching_period(vm.school_years_id);
          }
        });

      },
      getSchool_years(){
        var url = 'settings/school_years';
        axios.get(url).then(response => {
          this.school_years = response.data;
        });
      },
      getTeaching_period(){
        var url = 'settings/teaching_periods/' + this.school_years_id;
        axios.get(url).then(response => {
          this.teaching_periods = response.data;
        });
      },
      setSchool_years(school_years_id){
        this.teaching_periods_id = '';
        this.teaching_periods = []; 
        this.getTeaching_period();
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData = response.data.coordinators.data;
          vm.pagination = response.data.pagination;
        });
      },
      getStatus(value){
        var url = 'coordinators/getstatus/' + value.id;
        axios.put(url, value).then(response => {
          this.getAll();
          this.cancel();
          toastr.info('Se ha cambiado el estado del Coordinador Correctamente');
        }).catch(error => {
          toastr.error('Algo ha salido mal contactate con el Administrador');
        });
      },
      store(){
        var url = 'settings';
        axios.post(url,{
          school_years_id: this.school_years_id,
          teaching_periods_id: this.teaching_periods_id,
          admins_id: this.admins_id,
        }).then(response => {
          conosole.log("pasa por aqui 1");
          this.school_years_id = '';
          this.teaching_periods_id = '';
          this.admins_id = '';
          this.errors = [];
          toastr.success('Nuevo Años lectivo Creado Correctamente');
          conosole.log("pasa por aqui 2");
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
        this.find = {
          'id': value.id,
          'specialty': value.specialty,
          'profession': value.profession,
          'status': value.status
        };
        this.showUpdate = true;
      },
      update(id){
        var url = 'coordinators/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.find = {'id': '', 'specialty': '', 'profession': '', 'status': 1};
          this.errors = [];
          this.showUpdate = false;
          toastr.warning('Años lectivo Actualizado Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      deleteModal(value){
        this.showDelete = true;
        this.find = value;
      },
      deleteOne(value){
        this.find = {'id': '', 'specialty': '', 'profession': '', 'status': 1};
        this.showDelete = false;
        var url = 'coordinators/' + value.id;
        axios.delete(url).then(response => {
          this.getAll();
          toastr.success('Años lectivo Eliminado Correctamente');
        });
      },
      cancel(){
        this.name = '';
        this.description = '';
        this.status = 1;
        this.find = {'id': '', 'specialty': '', 'profession': '', 'status': 1};
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
          return value.specialty.toLowerCase().includes(this.search.toLowerCase())
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