<template>
  <div class="containerx">
    <div class="card" v-if="auth">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-chart-bar"></i> <b>Reportes</b>
        </div>
      </div>
      <div class="card-body text-center">
        <div class="row ">
          <div class="col-sm-4">
            <div class="card mb-3" style="width: 18rem;">
              <img class="card-img-top" src="image/estadis-02.png" width="50%" height="50%">
              <div class="card-body">
                <h5 class="card-title">Reporte de cumplimiento</h5>
                <p class="card-text">Reporte de cumplimiento filtrado por profesor y periodo.</p>
                <a v-if="auth == 'coordinators' || auth == 'admins' " :href="'reports/tasks#'" class="btn btn-primary">Más información</a>
                <a v-if="auth == 'teachers' " :href="'reports/task#'" class="btn btn-primary">Más información</a>
              </div>
            </div>
          </div>
          <div class="col-sm-4" v-if="auth == 'coordinators' ">
            <div class="card mb-3" style="width: 18rem;">
              <img class="card-img-top" src="image/estadis-03.png" width="50%" height="50%">
              <div class="card-body">
                <h5 class="card-title">Historico</h5>
                <p class="card-text">Historico de tareas por periodo.</p>
                <a :href="'reports/hystoric#'" class="btn btn-primary">Más información</a>
              </div>
            </div>
          </div>
          <!--
          <div class="col-sm-4">
            <div class="card mb-3" style="width: 18rem;">
              <img class="card-img-top" src="image/estadis-01.png" width="50%" height="50%">
              <div class="card-body">
                <h5 class="card-title">Estadisticas</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="btn btn-primary">Más información</a>
              </div>
            </div>
          </div>
          -->
        </div>
      </div>
    </div>
      
  </div>
</template>
<script>
  import axios  from 'axios'
  import moment from 'moment'
  import Multiselect from 'vue-multiselect'
  import ToggleButton from 'vue-js-toggle-button'
  import Chart from 'chart.js';
 
  Vue.use(ToggleButton);

  export default { 
    data(){ 
      return { 
        teachers_id: '',
        teaching_p_id_bytc: '',
        teaching_p_id_bysc: '',
        school_years_id: '',
        teachers: [],
        school_years: [],
        teaching_periods: [],
        teaching_periods_bysc: [],
        total: 0,
        justify: 0,
        injustify: 0,
        average: 0,
        showReport: false,
        status: 1,
        auth: '',
        find: {'teachers_id': '', 'teaching_periods_id': '', 'school_years_id': '', 'name': '', 'lastname': '', 'user': '', 'email': '', 'password': '', 'address': '', 'mobile': '', 'phone': '', 'type': '', 'profession': '', 'specialty': '', 'status': 1},
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
      VPaginator: VuePaginator,
      Multiselect
    },
    methods: {
      since(time){
        return moment(time).fromNow(true);
      },
      getAll(){
        var url = 'reports/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.auth = response.data.auth;
          vm.teachers = response.data.teachers;
          vm.school_years = response.data.school_years;
          vm.teaching_periods = response.data.teaching_periods;
        });
      },
    },
    created(){ 
      this.getAll();
    },
    computed: {
      searchAll() {
        return this.searchData.filter(value => {
          return value.users.name.includes(this.search.toLowerCase())
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