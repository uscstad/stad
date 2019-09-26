<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <i class="fas fa-chart-bar"></i>
        Reporte por Docente
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Docente*</label>
              <select class="form-control" v-model="teachers_id">
                <option value="">Seleccione un Profesor</option>
                <option value="0">Todos</option> 
                <option :value="teacher.id" v-for="teacher in teachers">{{teacher.name}}</option>
              </select>
              <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span> 
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label> Periodo *</label>
              <select class="form-control" v-model="teaching_p_id_bytc">
                <option value="">Seleccione un Periodo</option>
                <option :value="teaching_period.id" v-for="teaching_period in teaching_periods">{{teaching_period.namexsy}}</option>
              </select>
              <span v-for="error in errors.teaching_p_id_bytc" class="text-danger">{{ error }}</span>
            </div>
          </div>
        </div>
        <br>
        <div class="text-center">
          <button type="button" class="btn btn-primary" v-on:click.prevent="search_by_teacher(teachers_id, teaching_p_id_bytc)">Buscar</button>
          <button type="button" class="btn btn-secondary" v-on:click.prevent="cancelTeacher()">Cancelar</button>
        </div>
      </div>
    </div>
    <div v-if="showReport" class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-users"></i> <b>Graficos de Cumplimiento en el periodo seleccionado</b>
        </div>
      </div>
      <div class="card-body">
        <div className="form-group">
          <div class="form-row">
            <div class="col">
              <center><h5>Cumplimiento a tiempo</h5></center>
              <p>Tareas en el periodo selccionado : <strong>{{ total }}</strong></p>
              <p>Tiempo promedio : <strong>{{ average }} Dias</strong></p>
              <canvas id="myAreaChart" width="60%" height="30"></canvas>
            </div>
            <div class="col">
              <center><h5>Cumplimiento extra tiempo</h5></center>
              <p>Demoras Justificadas :  <strong>{{ justify }}</strong></p>
              <p>Demoras Injustificadas : <strong>{{ injustify }}</strong></p>
              <canvas id="myAreaChart2" width="70%" height="30"></canvas>
            </div>
          </div>
        </div>
          
      </div>
      <div class="card-footer">
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
        var url = '../reports/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.teachers = response.data.teachers;
          vm.school_years = response.data.school_years;
          vm.teaching_periods = response.data.teaching_periods;
          console.log(response.data);
        });
      },
      search_by_school_year(school_year_id, teaching_p_id) {
        if(teaching_p_id){
          this.showReport = true;
          let vm = this;
          axios.get('periods?teaching_p_id=' + teaching_p_id.id).then(function (response) {
            var data = response.data[0];
            vm.total = data.total;
            vm.justify = data.justifications;
            vm.injustify = data.prologues - data.justifications;
            vm.average = (data.days/data.total + data.extra/data.total).toFixed(1);
            var time = data.finish - data.prologues;
            var time_percent = (time / data.total) * 100;
            var prologue_percent = (data.prologues / data.total) * 100;
            var prologue = data.prologues;
            var ctx2 = document.getElementById("myAreaChart2");
            var myPieChart = new Chart(document.getElementById("myAreaChart"), {
            type: 'pie',
            data: {
              labels: ["A tiempo " + time + " ("+ time_percent.toFixed(1) +"%)", "Exra tiempo " + prologue + " ("+ prologue_percent.toFixed(1) +"%)"],
              datasets: [{
                data: [time, prologue],
                backgroundColor: ['#007bff', '#ffc107'],
              }],
            },
            });
            var myPieChart2 = new Chart(ctx2, {
            type: 'pie',
            data: {
              labels: ["Tareas Extra Tiempo " + prologue + "(100%)"],
              datasets: [{
                data: [prologue],
                backgroundColor: ['#28a745'],
              }],
            },
            });
          });
        }
      },
      search_by_teacher(teacher_id, teaching_p_id) {
        if(teaching_p_id && teacher_id){
          this.showReport = true;
          let vm = this;
          var url = 'teachers/' + teacher_id + '/' + teaching_p_id;
          axios.get(url).then(function (response) {
            if (response.data.length) {
              vm.showReport = true;
            } else {
              vm.showReport = false;
              toastr.error('No se encontraron tareas con los filtros seleccionados.');
              return false;
            }            
            var data = response.data[0];
            vm.total = data.total;
            vm.justify = data.justifications;
            vm.injustify = data.prologues - data.justifications;
            vm.unsatisfactory = data.unsatisfactory;
            vm.unfulfilled = data.unfulfilled;
            vm.average = (data.days/data.total + data.extra/data.total).toFixed(1);
            var time = data.finish > 0 ? data.finish - data.prologues : 0;
            var time_percent = (time / data.total) * 100;
            var prologue = data.finish > 0 ? data.prologues : 0;
            var prologues = data.prologues;
            var prologue_percent = (prologue / data.total) * 100;
            var ctx2 = document.getElementById("myAreaChart2");
            var myPieChart = new Chart(document.getElementById("myAreaChart"), {
            type: 'pie',
            data: {
              labels: ["A tiempo " + time + " ("+ time_percent.toFixed(1) +"%)", "Exra tiempo - " + prologue + " ("+ prologue_percent.toFixed(1) +"%)", "No satisfactorias - " + vm.unsatisfactory, "Incumplidas - " + vm.unfulfilled],
              datasets: [{
                data: [time, prologue, vm.unsatisfactory, vm.unfulfilled],
                backgroundColor: ['#007bff', '#ffc107', '#fa2323', '#fa6823'],
              }],
            },
            });
            var myPieChart2 = new Chart(ctx2, {
            type: 'pie',
            data: {
              labels: ["Tareas Extra Tiempo " + prologues + "(100%)"],
              datasets: [{
                data: [prologues],
                backgroundColor: ['#28a745'],
              }],
            },
            });
          });
        }
      },
      onSelect(){
        this.teaching_periods_bysc = [];
        for (var i = this.teaching_periods.length - 1; i >= 0; i--) {
          if(this.teaching_periods[i].school_years.id == this.school_years_id.id){
              this.teaching_periods_bysc.push(this.teaching_periods[i]);
          }
        }
      },
      getStatus(value){
        var url = 'teachers/getstatus/' + value.id;
        axios.put(url, value).then(response => {
          this.getAll();
          this.cancel();
          toastr.info('Se ha cambiado el estado del Usaurio Correctamente');
        }).catch(error => {
          toastr.error('Algo ha salido mal contactate con el Administrador');
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
        this.status = 1;
        this.find = {'id': '', 'type_doc': '', 'doc': '', 'name': '', 'lastname': '', 'user': '', 'email': '', 'password': '', 'address': '', 'mobile': '', 'phone': '', 'type': '', 'profession': '', 'specialty': '', 'status': 1};
        this.errors = [];
      },
      cancelTeacher(){
        this.teachers_id = '';
        this.teaching_p_id_bytc = '';
        this.showReport = false;
      },
      cancelYears(){
        this.school_years_id = '';
        this.teaching_p_id_bysc = '';
        this.showReport = false;
      }
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