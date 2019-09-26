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
              <select class="form-control" v-model="teaching_p_id_bytc">
                <option :value="teaching_period.id" v-for="teaching_period in teaching_periods">{{teaching_period.namexsy}}</option>
              </select>
              <span v-for="error in errors.teaching_p_id_bytc" class="text-danger">{{ error }}</span>
            </div>
          </div>
          <div class="col-md-6 text-right">
            <button type="button" class="btn btn-secondary" v-on:click.prevent="cancelTeacher()">Cancelar</button>
            <button type="button" class="btn btn-primary" v-on:click.prevent="search_by_teacher(teaching_p_id_bytc)">Buscar</button>
          </div>
          <br><br><hr>
          <div v-if="showReport" class="col-md-12">
            <div class="form-group">
              <div class="form-row">
                <div class="col">
                  <center><h2>Cumplimiento a tiempo</h2></center>
                  <h4>Tareas en el periodo selccionado : <strong>{{ total }}</strong></h4>
                  <h4>Tiempo promedio : <strong>{{ average }} Dias</strong></h4>
                  <canvas id="myAreaChart" width="100%" height="30"></canvas>
                </div>
                <div class="col">
                  <center><h2>Cumplimiento extra tiempo</h2></center>
                  <h4>Demoras Justificadas :  <strong>{{ justify }}</strong></h4>
                  <h4>Demoras Injustificadas : <strong>{{ injustify }}</strong></h4>
                  <canvas id="myAreaChart2" width="100%" height="30"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <br>
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
        var url = 'task/list';
        this.fetchStories(url);
      },
      fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.school_years = response.data.school_years;
          vm.teaching_periods = response.data.teaching_periods;
          vm.teacher_id = response.data.teacher_id;
          vm.teaching_p_id_bytc = response.data.teaching_periods_id;
          vm.search_by_teacher(response.data.teaching_periods_id);
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
      search_by_teacher(teaching_p_id) {
        console.log('teaching_p_id', teaching_p_id);
        if(teaching_p_id && this.teacher_id){
          this.showReport = true;
          let vm = this;
          var url = 'teachers/' + this.teacher_id + '/' + teaching_p_id;
          axios.get(url).then(function (response) {
            if(response.data[0]){
              vm.prologues(response.data[0]);
            } else {
              vm.showReport = false;
            }
          });
        }
      },
      prologues(response){
        var data = response;
        this.total = data.total;
        this.justify = data.justifications;
        this.injustify = data.prologues - data.justifications;
        this.average = (data.days/data.total + data.extra/data.total).toFixed(1);
        var time = data.finish - data.prologues;
        var time_percent = (time / data.total) * 100;
        var prologue_percent = (data.prologues / data.total) * 100;
        var prologue = data.prologues;
        var myPieChart = new Chart(document.getElementById("myAreaChart"), {
        type: 'pie',
        data: {
          labels: ["A tiempo " + time + " ("+ time_percent.toFixed(1) +"%)", "Exra tiempo - " + prologue + " ("+ prologue_percent.toFixed(1) +"%)"],
          datasets: [{
            data: [time, prologue],
            backgroundColor: ['#007bff', '#ffc107'],
          }],
        },
        });
        var myPieChart2 = new Chart(document.getElementById("myAreaChart2"), {
        type: 'pie',
        data: {
          labels: ["Tareas Extra Tiempo " + prologue + "(100%)"],
          datasets: [{
            data: [prologue],
            backgroundColor: ['#28a745'],
          }],
        },
        });
      },
      onSelect(){
        this.teaching_periods_bysc = [];
        for (var i = this.teaching_periods.length - 1; i >= 0; i--) {
          if(this.teaching_periods[i].school_years.id == this.school_years_id.id){
              this.teaching_periods_bysc.push(this.teaching_periods[i]);
          }
        }
        console.log(this.school_years_id);

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
      store(){
        var url = 'teachers';
        axios.post(url,{
          type_doc: this.type_doc,
          doc: this.doc,
          name: this.name,
          lastname: this.lastname,
          user: this.user,
          email: this.email,
          password: this.password,
          address: this.address,
          mobile: this.mobile,
          phone: this.phone,
          type: this.type,
          status: this.status,
          profession: this.profession,
          specialty: this.specialty,
        }).then(response => {
          this.getAll();
          this.cancel();
          toastr.success('Nuevo Usaurio Creado Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      edit(value){
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
          'status': 1
        };
        this.showUpdate = true;
      },
      update(id){
        var url = 'teachers/' + id;
        axios.put(url, this.find).then(response => {
          this.getAll();
          this.cancel();
          toastr.warning('Usaurio Actualizado Correctamente');
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
          toastr.success('Usaurio Eliminado Correctamente');
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
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
      },
      cancelTeacher(){
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