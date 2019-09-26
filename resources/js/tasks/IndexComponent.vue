<template>
  <div class="containerx">
    <div class="card">
      <div class="card-header">
        <div class="col-md-12">
          <i class="fas fa-tasks"></i> <b>Tareas</b>
        </div>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <select class="form-control" v-model="teaching_period_id">
                  <option :value="teaching_periods.id" v-for="teaching_periods in teaching_period">{{teaching_periods.name}}</option>
                </select>
                <span v-for="error in errors.teaching_period_id" class="text-danger">{{ error }}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <select class="form-control" v-model="teachers_id">
                  <option value="">Docente (Todos)</option>
                  <option :value="teacher.id" v-for="teacher in teachers">{{teacher.name}}</option>
                </select>
                <span v-for="error in errors.teachers_id" class="text-danger">{{ error }}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <select class="form-control" v-model="status_id">
                  <option value="">Estado (Todos)</option>
                  <option :value="state.id" v-for="state in status">{{state.name}}</option>
                </select>
                <span v-for="error in errors.status_id" class="text-danger">{{ error }}</span>
              </div>
            </div>
            <div class="col-12 col-sm-3 text-right">
              <a href="./" class="btn btn-primary"><i class="fas fa-chevron-circle-left fa-lg"></i> Atras</a>  
              <a href="#" class="btn btn-primary" @click="showCreate=true"><i class="fas fa-plus-circle fa-lg"></i> Tarea</a>
            </div>
          </div>
        </div>
        <div class="col-md-12" v-if="searchAll.length > 0">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
            <!--<table class="table table-striped table-bordered  table-wrapper-scroll-y my-custom-scrollbar">-->
              <thead>
                <tr>
                  <th >Pendientes</th>
                  <th >En proceso</th>
                  <th >Pendientes de revisión</th>
                  <th >Terminadas</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td >
                    <div v-for="(value, index) in searchAll">
                      <div v-if="value.status == 'assigned' ">
                        <div :class="'card border-primary '+(setPending==value ? ' border-success ' : ' ')">  
                          <a @click="setPendingCheck=null;setInprocess=null;setFinish=null;setPending=value;" @dblclick="setPending=null;setPendingCheck=null;setInprocess=null;setFinish=null;">
                            <div class="card-body" style="
                              padding-top: 10px;
                              padding-right: 10px;
                              padding-left: 10px;
                              padding-bottom: 5px;"
                            >
                              <p class="card-title">{{ value.tasks.name }} 1</p>
                              <!--<p class="card-text">{{ key.tasks.description }}</p>-->
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td >
                    <div v-for="(value, index) in searchAll">
                      <div v-if="value.status == 'processing' || value.status == 'reprogrammed' ">
                        <div :class="'card border-primary '+(value.prologues ? ' bg-warning ' : ' ')+ (setInprocess==value ? ' border-success ' : ' ')">  
                          <a @click="setPending=null;setPendingCheck=null;setFinish=null;setInprocess=value;" @dblclick="setPending=null;setPendingCheck=null;setInprocess=null;setFinish=null;">
                            <div class="card-body" style="
                              padding-top: 10px;
                              padding-right: 10px;
                              padding-left: 10px;
                              padding-bottom: 5px;"
                            >
                              <p class="card-title">{{ value.tasks.name }} {{ value.prologues ? '(Prorroga)' : ''}}</p>
                              <!--<p class="card-text">{{ key.tasks.description }}</p>-->
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td >
                    <div v-for="(value, index) in searchAll">
                      <div v-if="value.status == 'pending' ">
                        <div :class="'card border-primary '+(value.prologues ? ' bg-warning' : '')+ (setPendingCheck==value ? ' border-success ' : ' ')">  
                          <a @click="setPending=null;setInprocess=null;setFinish=null;setPendingCheck=value;" @dblclick="setPending=null;setPendingCheck=null;setInprocess=null;setFinish=null;">
                            <div class="card-body" style="
                              padding-top: 10px;
                              padding-right: 10px;
                              padding-left: 10px;
                              padding-bottom: 5px;"
                            >
                              <p class="card-title">{{ value.tasks.name }} {{ value.prologues ? '(Prorroga)' : ''}}</p>
                              <!--<p class="card-text">{{ key.tasks.description }}</p>-->
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td >
                    <div v-for="(value, index) in searchAll">
                      <div v-if="value.status == 'finishedtime' || value.status == 'finished' || value.status == 'unfulfilled' || value.status == 'unsatisfactory'  ">
                        <div :class="'card border-primary '+(value.status == 'unfulfilled' ? ' bg-danger' : '')+(setFinish==value ? ' border-success ' : ' ') ">  
                          <a @click="setPending=null;setPendingCheck=null;setInprocess=null;setFinish=value;" @dblclick="setPending=null;setPendingCheck=null;setInprocess=null;setFinish=null;">
                            <div class="card-body" style="
                              padding-top: 10px;
                              padding-right: 10px;
                              padding-left: 10px;
                              padding-bottom: 5px;"
                            >
                              <p class="card-title">{{ value.tasks.name }} {{ value.prologues ? '(Prorroga)' : ''}}</p>
                              <!--<p class="card-text">{{ key.tasks.description }}</p>-->
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </td>
                  <!--<td class="group">
                    <a v-if="value.status == 'assigned' || value.status == 'processing' || value.status == 'reprogrammed' || value.status == 'unfulfilled' " href="#" v-on:click.prevent="justification(value)"><i class="fas fa-clipboard-check fa-2x"></i></a>
                    <a v-if="value.status == 'canceled' || value.status == 'finish' || value.status == 'finishedtime' " href="#" v-on:click.prevent=""><i class="fas fa-check-circle fa-2x"></i></a>
                  </td>
                  <td>{{ value.tasks.name }}</td>
                  <td>
                    <p>Fecha Inicio: <b>{{ value.start_date | getdate }}</b></p>
                    <p>Fecha Final: <b>{{ value.final_date | getdate }}</b></p>
                    <p>Estado: <b>{{ value.status  | getStatus }}</b></p>
                    <p>Tipo: <b>{{ value.tasks.type != "groups" ? "Individuales" : "Colectivas" }}</b></p>
                    <p>Lider: <b>{{ value.teachers.users.name }} {{ value.teachers.users.lastname }}</b></p>
                    <p>Suplentes: 
                      <b v-if="value.type != 'individuals'" v-for="key in value.tcxts">
                        {{ key.teachers.users.name }} {{ key.teachers.users.lastname }} 
                      </b>
                      <b v-if="value.type == 'individuals'">No</b>
                    </p>
                  </td>
                  <td>{{ value.created_at }}</td>
                  <td>{{ since(value.created_at) }}</td>-->
                </tr>
              </tbody>
              <thead>
                <tr>
                  <th class="text-center" >
                    <a href="#" @click="getPending()" class="btn btn-primary btn-sm"><i class="far fa-eye"></i> Ver</a>
                  </th>
                  <th class="text-center" >
                    <a href="#" @click="getInprocess()" class="btn btn-primary btn-sm"><i class="far fa-eye"></i> Ver</a>
                  </th>
                  <th class="text-center" >
                    <a href="#" @click="getPendingCheck()" class="btn btn-primary btn-sm"><i class="far fa-eye"></i> Revisar</a>
                  </th>
                  <th class="text-center" >
                    <a href="#" @click="getFinish()" class="btn btn-primary btn-sm"><i class="far fa-eye"></i> Ver</a>
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="col-md-12" v-else>
          <h4>No se han encontrado registros de las Tareas.</h4>
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
                  <h4 class="modal-title"><i class="fas fa-tasks"></i> <b>Creación de Tarea</b></h4>
                  <button type="button" class="close" @click="cancel">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form method="POST" v-on:submit.prevent="store">
                  <div class="modal-body" style="margin: 0px 0;max-height: 400px;overflow-y: auto;">

                    <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"> General</a>
                        <a v-if="showAssign" class="nav-item nav-link" id="nav-individual-tab" data-toggle="tab" href="#nav-individual" role="tab" aria-controls="nav-individual" aria-selected="false"> Asignación Individual</a>
                        <a v-if="showGroups" class="nav-item nav-link" id="nav-groups-tab" data-toggle="tab" href="#nav-groups" role="tab" aria-controls="nav-groups" aria-selected="false"> Asignación Grupales</a>
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
                                v-model="activity" 
                                :options="activities" 
                                label="name" 
                                track-by="id"
                                placeholder="Seleccione Actividad" 
                                :searchable="true"
                                @input="singleActivity"
                              ></multiselect>
                              <span v-for="error in errors.tasks_id" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Tipo *</label>
                              <multiselect 
                                v-model="type_select" 
                                :options="types" 
                                label="name" 
                                track-by="id"
                                @input="getTasks"
                                placeholder="Seleccione Tipo" 
                              ></multiselect>
                              <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">                        
                            <div class="form-group">
                              <label>Fecha Inicial *</label>
                              <datetime type="datetime" v-model="start_date" :min-datetime="new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha"></datetime>
                              <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Fecha Final *</label>
                              <datetime type="datetime" v-model="final_date" :min-datetime="start_date ? new Date( start_date.substring(0, 19).replace('T', ' ')).toISOString() : null" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha" ></datetime>
                              <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="form-group">
                              <label>Detalle *</label>
                              <textarea name="detail" class="form-control" v-model="detail" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;"></textarea>
                              <span v-for="error in errors.detail" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Solicitar evidencia *</label>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="1" v-model="attachments" >
                                <label class="form-check-label">Si</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="0" v-model="attachments" checked>
                                <label class="form-check-label">No</label>
                              </div>
                              <span v-for="error in errors.attachments" class="text-danger">{{ error }}</span>
                            </div> 
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>¿Cual?</label>
                              <textarea name="comments" class="form-control" v-model="comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="attachments == 0" ></textarea>
                              <span v-for="error in errors.comments" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Categoria</label>
                              <multiselect v-model="tags" :options="categories" :multiple="true" placeholder="Seleccione una categoria" track-by="name" label="name"></multiselect>
                              <span v-for="error in errors.tags" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Periodo *</label>
                              <input type="text" name="name" class="form-control" v-model="teaching_period_name" disabled="">
                              <span v-for="error in errors.teaching_period_name" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-individual" role="tabpanel" aria-labelledby="nav-individual-tab">
                        <br>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Docente Encargado *</label>
                              <multiselect 
                                v-model="teacher" 
                                :options="teachers_groups" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                placeholder="Seleccione un Docente" 
                              ></multiselect>
                              <span v-for="error in errors.teacher" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group" >
                              <label>Nivel de educación </label>
                              <multiselect 
                                v-model="selectLevel" 
                                :options="level_educations" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                @input="aplyFilter()"
                                placeholder="Seleccione un nivel de Educación" 
                              ></multiselect>
                            </div>
                            <div class="form-group" >
                              <label>Asignación por Areas </label>
                              <multiselect 
                                v-model="selectSubje" 
                                :options="subjects" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                @input="aplyFilter()"
                                placeholder="Seleccione una Asignatura" 
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
                                v-model="teacher" 
                                :options="teachers_groups" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                placeholder="Seleccione un Docente" 
                              ></multiselect>
                              <span v-for="error in errors.teacher" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="form-group">
                              <label>Profesores Asignados *</label>
                              <multiselect v-model="teachers_fulls" :options="teachers_groups" :multiple="true" placeholder="Seleccione Profesores" track-by="name" label="name"></multiselect>
                              <span v-for="error in errors.teachers_fulls" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group" >
                              <label>Nivel de educación </label>
                              <multiselect 
                                v-model="selectLevel" 
                                :options="level_educations" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                @input="aplyFilter()"
                                placeholder="Seleccione un nivel de Educación" 
                              ></multiselect>
                            </div>
                            <div class="form-group" >
                              <label>Asignación por Areas </label>
                              <multiselect 
                                v-model="selectSubje" 
                                :options="subjects" 
                                label="name" 
                                track-by="id"
                                :multiple="true"
                                @input="aplyFilter()"
                                placeholder="Seleccione una Asignatura" 
                              ></multiselect>
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
    <div v-if="showConfig">
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
                        <a v-show="!view" class="nav-item nav-link" id="nav-task-tab" data-toggle="tab" href="#nav-task" role="tab" aria-controls="nav-task" aria-selected="false"> Respuesta</a>
                        <a v-show="find.prologues" class="nav-item nav-link" id="nav-reprocessing-tab" data-toggle="tab" href="#nav-reprocessing" role="tab" aria-controls="nav-reprocessing" aria-selected="false"> Reprogramación</a>
                        <a v-show="show_pending" class="nav-item nav-link" id="nav-calification-tab" data-toggle="tab" href="#nav-calification" role="tab" aria-controls="nav-calification" aria-selected="false"> Calificación</a>
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
                                :disabled="view == 0"
                              ></multiselect>
                              <span v-for="error in errors.activity" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">                  
                            <div class="form-group">
                              <label>Tipo *</label>
                              <multiselect 
                                v-model="find.type" 
                                :options="types" 
                                label="name" 
                                track-by="id"
                                @input="getTasks(type.id)"
                                placeholder="Seleccione Tipo" 
                                :disabled="view == 0"
                              ></multiselect>
                              <span v-for="error in errors.type" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">                        
                            <div class="form-group">
                              <label>Fecha Inicial *</label>
                              <datetime type="datetime" v-model="find.start_date" :min-datetime="new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="view == 0"></datetime>
                              <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                            </div>
                          </div>  
                          <div class="col-sm-6">      
                            <div class="form-group">
                              <label>Fecha Final *</label>
                              <datetime type="datetime" v-model="find.final_date" :min-datetime="start_date ? new Date( start_date.substring(0, 19).replace('T', ' ')).toISOString() : new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()" input-class="form-control" placeholder="Seleccione una etiqueta" :disabled="view == 0"></datetime>
                              <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="form-group">
                              <label>Detalle *</label>
                              <textarea name="detail" class="form-control" v-model="find.detail" :disabled="view == 0" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;"></textarea>
                              <span v-for="error in errors.detail" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Solicitar evidencia *</label>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="1" v-model="find.attachments" :disabled="view == 0">
                                <label class="form-check-label">Si</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="radio" value="0" v-model="find.attachments" :disabled="view == 0">
                                <label class="form-check-label">No</label>
                              </div>
                              <span v-for="error in errors.attachments" class="text-danger">{{ error }}</span>
                            </div> 
                          </div>
                          <div class="col-sm-6">   
                            <div class="form-group">
                              <label>¿Cual?</label>
                              <textarea name="comments" class="form-control" v-model="find.comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.attachments == 0 || view == 0"></textarea>
                              <span v-for="error in errors.comments" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Categorias</label>
                              <multiselect v-model="find.tags" :options="categories" :multiple="true" placeholder="Seleccione una fecha" track-by="name" label="name" :disabled="view == 0"></multiselect>
                              <span v-for="error in errors.tag" class="text-danger">{{ error }}</span>
                            </div>
                          </div>
                          <div class="col-sm-6">   
                            <div class="form-group">
                              <label>Periodo *</label>
                              <input type="text" name="name" class="form-control" v-model="find.teaching_periods" disabled="">
                              <span v-for="error in errors.teaching_periods" class="text-danger">{{ error }}</span>
                            </div>
                          </div>  
                          
                          <hr>
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
                                :disabled="view == 0"
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
                                :disabled="view == 0"
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
                                :disabled="view == 0"
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
                                :disabled="view == 0"
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
                                :disabled="view == 0"
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
                                :multiple="true"
                                placeholder="No tiene un nivel de Educación" 
                                :disabled="view == 0"
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
                                :disabled="view == 0"
                              ></multiselect>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-task" role="tabpanel" aria-labelledby="nav-task-tab">
                        <form method="POST" v-on:submit.prevent="saveFinish(find)">
                          <br>
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="form-group">
                                <label>Respuesta *</label>
                                <textarea name="answer" class="form-control" v-model="find.answer" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="view1 == 0"></textarea>
                                <span v-for="error in errors.answer" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-12" v-if="!find.answered">
                              <div class="form-group" >
                                <label>Anexo *</label>
                                <input type="file" name="annexed" class="form-control" v-on:change="onImageChange" :disabled="view1 == 0">
                                <span v-for="error in errors.annexed" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-12" v-else>
                              <div class="form-group" v-if="find.annexed">
                                <label>Anexo</label>
                                <a :href="find.annexed" target="_blank" class="btn btn-primary">Ver</a>
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-group">
                                <label>Comentario *</label>
                                <textarea name="comments" class="form-control" v-model="find.comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="view1 == 0"></textarea>
                                <span v-for="error in errors.comments" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                          </div>
                          <div class="text-center">
                            <input type="submit" class="btn btn-primary" value="Terminar tarea" v-show="view1">
                          </div>
                        </form>
                      </div>
                      <div class="tab-pane fade" id="nav-reprocessing" role="tabpanel" aria-labelledby="nav-reprocessing-tab">
                        <form method="POST" v-on:submit.prevent="saveReprocessing(find)">
                          <br>
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label>Fecha Inicial *</label>
                                <datetime type="datetime" v-model="find.start_date"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="view == 0"></datetime>
                                <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                              </div>
                              <div class="form-group">
                                <label>Nueva Fecha Final *</label>
                                <datetime type="datetime" v-model="find.new_final_date"  :min-datetime="find.start_date ? new Date(find.start_date).toISOString() : new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()" input-class="form-control" placeholder="Seleccione una fecha" :disabled="view1 == 0"></datetime>
                                <span v-for="error in errors.new_final_date" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label>Fecha Final *</label>
                                <datetime type="datetime" v-model="find.final_date" :min-datetime="find.start_date ? new Date(find.start_date).toISOString() : new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una etiqueta" :disabled="view == 0"></datetime>
                                <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                              </div>
                              <div class="form-group">
                                <label>Justificación</label>
                                <textarea name="find.justifications" class="form-control" v-model="find.justifications" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="view1 == 0"></textarea>
                                <span v-for="error in errors.justifications" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-if="!find.evidence && !find.prologues">
                              <div class="form-group">
                                <label>Evidencia</label>
                                <input type="file" accept=".pdf" name="evidence" class="form-control" v-on:change="onImageChange" :disabled="view1 == 0">
                                <span v-for="error in errors.image" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-12" v-else>
                              <div class="form-group">
                                <label>Evidencia</label>
                                <a :href="find.evidence" target="_blank" class="btn btn-primary">Ver</a>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.prologues">
                              <div class="form-group">
                                <label>¿Valida? *</label>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="1" v-model="find.positive" :disabled="find.prologues.accepted == 'yes' ">
                                  <label class="form-check-label">Si</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="0" v-model="find.positive" :disabled="find.prologues.accepted == 'yes' ">
                                  <label class="form-check-label">No</label>
                                </div>
                                <span v-for="error in errors.positive" class="text-danger">{{ error }}</span>
                              </div> 
                            </div>
                            <div class="col-sm-6" v-show="find.prologues">
                              <div class="form-group">
                                <label>Detalles / Recomendaciones *</label>
                                <textarea name="recommendations" class="form-control" v-model="find.recommendations" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.prologues.accepted == 'yes'"></textarea>
                                <span v-for="error in errors.recommendations" class="text-danger">{{ error }}</span>
                              </div>
                            </div>

                          </div>  
                          <div class="text-center">
                            <input type="submit" class="btn btn-primary" value="Guardar" v-show="find.prologues.accepted == 'pending' ">
                          </div>
                        </form>  
                      </div>
                      <div class="tab-pane fade" id="nav-calification" role="tabpanel" aria-labelledby="nav-calification-tab">
                        <form method="POST" v-on:submit.prevent="saveCalification(find)">
                          <br>
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label>El trabajo es satisfactorio *</label>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="1" v-model="find.jobs" :disabled="find.status == 'finished' || find.status == 'finishedtime' " >
                                  <label class="form-check-label">Si</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="0" v-model="find.jobs" :disabled="find.status == 'finished' || find.status == 'finishedtime' || find.prologues " >
                                  <label class="form-check-label">No</label>
                                </div>
                                <span v-for="error in errors.jobs" class="text-danger">{{ error }}</span>
                              </div> 
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label>Comentario *</label>
                                <textarea name="jobs_comments" class="form-control" v-model="find.jobs_comments" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.status == 'finished' || find.status == 'finishedtime'"></textarea>
                                <span v-for="error in errors.jobs_comments" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.jobs == 0">
                              <div class="form-group">
                                <label>Desea solicitar correcciíon *</label>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="1" v-model="find.correction" :disabled="find.status == 'finished' || find.status == 'finishedtime' " >
                                  <label class="form-check-label">Si</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" value="0" v-model="find.correction" :disabled="find.status == 'finished' || find.status == 'finishedtime' || find.prologues " >
                                  <label class="form-check-label">No</label>
                                </div>
                                <span v-for="error in errors.jobs" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.correction == 1 "></div>
                            <div class="col-sm-6" v-show="find.correction == 1 ">
                              <div class="form-group">
                                <label>Fecha Inicial *</label>
                                <datetime type="datetime" v-model="find.start_date"  input-class="form-control" placeholder="Seleccione una fecha" :disabled="view == 0"></datetime>
                                <span v-for="error in errors.start_date" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.correction == 1 ">
                              <div class="form-group">
                                <label>Nueva Fecha Final *</label>
                                <datetime type="datetime" v-model="find.new_final_date" :min-datetime="find.start_date ? new Date(find.start_date).toISOString() : new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una fecha" ></datetime>
                                <span v-for="error in errors.new_final_date" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.correction == 1 ">
                              <div class="form-group">
                                <label>Fecha Final *</label>
                                <datetime type="datetime" v-model="find.final_date" :min-datetime="find.start_date ? new Date(find.start_date).toISOString() : new Date(teaching_periods_start).toISOString()" :max-datetime="new Date(teaching_periods_final).toISOString()"  input-class="form-control" placeholder="Seleccione una etiqueta" :disabled="view == 0"></datetime>
                                <span v-for="error in errors.final_date" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.correction == 1 ">
                              <div class="form-group">
                                <label>Justificación</label>
                                <textarea name="justifications" class="form-control" v-model="find.justifications" style="margin-top: 0px; margin-bottom: 0px; height: 57px; resize: none;" :disabled="find.prologues"></textarea>
                                <span v-for="error in errors.justifications" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-6" v-show="find.correction == 1  && !find.evidence">
                              <div class="form-group">
                                <label>Evidencia</label>
                                <input type="file" accept=".pdf" name="evidence" class="form-control" v-on:change="onImageChange" :disabled="find.prologues">
                                <span v-for="error in errors.evidence" class="text-danger">{{ error }}</span>
                              </div>
                            </div>
                            <div class="col-sm-2" v-show="!find.prologues && find.evidence">
                              <div class="form-group">
                                <label>Evidencia</label>
                                <a :href="find.evidence" target="_blank" class="btn btn-primary">Ver</a>
                              </div>
                            </div>
                          </div>  
                          <div class="text-center">
                            <input type="submit" class="btn btn-primary" value="Calificar" v-show="find.status == 'pending' ">
                          </div>
                        </form>  
                      </div>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="deleteOne(find)" v-show="view">Eliminar</button>
                    <input type="submit" class="btn btn-primary" value="Modificar" v-show="view">
                    <button type="button" class="btn btn-secondary" @click="cancel()">Cancelar</button>
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
 
  Vue.use(ToggleButton);

  export default { 
    props: ['data'],
    data(){ 
      return { 
        view: 0,
        view1: 0,
        activity: null,
        activities_id: null,
        tasks_id: null,
        type: null,
        type_select: null,
        teaching_period_id: '',
        teaching_period_name: '',
        teaching_period: [],
        teachers_id: '',
        status_id: '',
        view: 0,
        types:[
          {id: 'individuals', name:'Individuales'},
          {id: 'groups',      name:'Colaborativos'}
        ],
        categories: [],
        attachments: 0,
        teacher: null,
        tag: '',
        tags: null,
        start_date: '',
        new_final_date: '',
        final_date: '',
        teaching_periods: {},
        comments: '',
        detail: '',
        justifications: '',
        evidence: '',
        teachers_group: '',
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
        teachers: [],
        teachers_groups: [],
        teachers_tmp: [],
        errors: [],
        activities: [],
        level_educations: [],
        subjects: [],
        setPending: '',
        teaching_periods_start: "",
        teaching_periods_final: "",
        setInprocess: '',
        setFinish: '',
        setPendingCheck: '',
        status: [
          {'id': 'assigned',      'name': 'Asignación'},
          {'id': 'processing',    'name': 'En proceso'},
          {'id': 'pending',       'name': 'Pendientes'},
          {'id': 'finishedtime',  'name': 'Finalizado sobre tiempo'},
          {'id': 'finish',        'name': 'Finalizado'},
        ],
        show_pending: 0,
        showCreate: false,
        showUpdate: false,
        showDelete: false,
        showAssign: false,
        showGroups: false,
        showConfig: false,
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
        let vm = this;
        vm.searchData           = this.data.tasks;
        vm.categories           = this.data.categories;
        vm.teaching_period      = this.data.teaching_periods;
        vm.teaching_period_id   = this.data.teaching_periods_id;
        vm.teaching_period_name = this.data.teaching_periods_name;
        vm.teaching_periods_start = this.data.teaching_periods_start;
        vm.teaching_periods_final = this.data.teaching_periods_final;
        vm.activities           = this.data.activities;
        vm.level_educations     = this.data.level_educations;
        vm.subjects             = this.data.subjects;
        vm.teachers             = this.data.teachers;
        vm.teachers_groups      = this.data.teachers;
        vm.teachers_tmp         = this.data.teachers;
        console.log(vm.teachers_groups);
        //var url = 'tasks/list';
        //this.fetchStories(url);
      },
      /*fetchStories(page_url) {
        let vm = this;
        axios.get(page_url).then(function (response) {
          vm.searchData           = response.data.tasks;
          vm.categories           = response.data.categories;
          vm.teaching_period      = response.data.teaching_periods;
          vm.teaching_period_id   = response.data.teaching_periods_id;
          vm.teaching_period_name = response.data.teaching_periods_name;
          vm.activities           = response.data.activities;
          vm.level_educations     = response.data.level_educations;
          vm.subjects             = response.data.subjects;
          vm.teachers             = response.data.teachers;
        });
      },*/
      store(){
        var url = 'tasks';
        axios.post(url,{
          tasks_id:             this.tasks_id,
          type:                 this.type,
          attachments:          this.attachments,
          teacher:              this.teacher,
          categories:           this.tags,
          start_date:           this.start_date,
          final_date:           this.final_date,
          comments:             this.comments,
          detail:               this.detail,          
          selectLevel:          this.selectLevel,
          selectSubje:          this.selectSubje,
          teaching_periods_id:  this.teaching_period_id,
          teachers_fulls:       this.teachers_fulls,
          teachers_fulls:       this.teachers_fulls,
        }).then(response => {
          window.location.reload();
          this.cancel();
          toastr.success('Nueva Tarea Creada Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      aplyFilter(){
        let vm = this;
        var teachers = [];
        var level;
        var subject;
        if (vm.selectLevel.length || vm.selectSubje.length) {
          for (var i =  0; i < vm.teachers_tmp.length; i++) {
            level = vm.selectLevel.length ? false : true;
            subject =  vm.selectSubje.length ? false : true;
            for (var j =  0; j < vm.selectLevel.length && !level; j++) {
              if(vm.teachers_tmp[i].lexts.map(function(e) { return e.level_educations.id }).indexOf(vm.selectLevel[j].id) > 0)
                level = true;
            }
            for (var j =  0; j < vm.selectSubje.length && !subject; j++) {
              if(vm.teachers_tmp[i].suxts.map(function(e) { return e.subjects.id }).indexOf(vm.selectSubje[j].id) > 0)
                  subject = true; 
            }
            
            if (subject && level) 
              teachers.push(vm.teachers_tmp[i]);
            
          }
        } else{ 
          teachers = vm.teachers_tmp;
        }
        vm.teachers_groups = teachers;
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
        this.find = {
          'id': value.id,
          'status': value.status,
          'activity': {'id': value.tasks.id, 'name': value.tasks.name},
          'tasks_id': value.tasks.id,
          'type': {'id': value.type, 'name': value.type != 'individuals' ? 'Colaborativos' : 'Individuales'},
          'types': value.type,
          'teacher':{'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname},
          'teachers_id': value.teachers.id,
          'tags': value.categories != "" ? JSON.parse(value.categories) : "",
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_group': value.tcxts != "" ? array : "No Data",
          'teaching_periods': this.teaching_periods,
          'teaching_periods_id': this.teaching_periods_id,
          'attachments': value.attachments != 0 ? 1 : 0,
          'comments': value.comments,
          'detail': value.detail,
          'positive': value.positive,
          'positive_status': value.positive,
          'accepted': value.prologues ? value.prologues.accepted : '',
          'prologue_id': value.prologues ? value.prologues.id : false,
          'justifications': value.prologues ? value.prologues.justifications : '',
          'evidence': value.prologues ? value.prologues.archiveName : '',
          'new_final_date': value.prologues ? value.prologues.final_date : '',
          'answered': value.answer ? true : false,
          'answer': value.answer,
          'annexed': value.archiveName,
          'level_educations': value.level_educations != "" ? JSON.parse(value.level_educations) : "",
          'subjects': value.subjects != "" ? JSON.parse(value.subjects) : "",
          'recommendations': value.recommendations,
          'jobs': value.jobs,
          'jobs_comments': value.jobs_comments,
        };
        if(value.type == 'groups'){
          this.showGroups = true;
          this.showAssign = false;
        } else {
          this.showAssign = true;
          this.showGroups = false;
        }
        this.showUpdate = true;
      },
      update(id){
        var url = 'tasks/' + id;
        axios.put(url, this.find).then(response => {
          window.location.reload();
          this.cancel();
          toastr.warning('Tarea Actualizada Correctamente');
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
        this.showConfig = false;
        var url = 'tasks/' + value.id;
        axios.delete(url).then(response => {
          window.location.reload();
          toastr.success('Tarea Eliminada Correctamente');
        });
      },
      getPending(){
        if(this.setPending !== ''){
          this.justification(this.setPending);
          this.showConfig = true;
          this.view = 1;
          this.show_pending = 0;
        }
      },
      getInprocess(){
        if(this.setInprocess !== ''){
          this.justification(this.setInprocess);
          this.showConfig = true;
          this.view = 0;
          this.show_pending = 0;
        }
      },
      getPendingCheck(){
        if(this.setPendingCheck !== ''){
          this.justification(this.setPendingCheck);
          this.showConfig = true;
          this.view = 0;
          this.show_pending = 1;
        }
      },
      getFinish(){
        if(this.setFinish !== ''){
          this.justification(this.setFinish);
          this.showConfig = true;
          this.view = 0;
          this.show_pending = 1;
        }
      },
      justification(value){
        var array = [];
        if(value.tcxts != null){
          $.each(value.tcxts, function(key, value){
            array.push({'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname});
          })
        }
        this.find = {
          'id': value.id,
          'status': value.status,
          'activity': {'id': value.tasks.id, 'name': value.tasks.name},
          'tasks_id': value.tasks.id,
          'type': {'id': value.type, 'name': value.type != 'individuals' ? 'Colaborativos' : 'Individuales'},
          'types': value.type,
          'teacher':{'id':  value.teachers.id, 'name': value.teachers.users.name+' '+value.teachers.users.lastname},
          'teachers_id': value.teachers.id,
          'tags': value.categories != "" ? JSON.parse(value.categories) : "",
          'start_date': value.start_date,
          'final_date': value.final_date,
          'teachers_fulls': value.tcxts != "" ? array : "No Data",
          'teaching_periods': value.teaching_periods.name,
          'teaching_periods_id': value.teaching_periods_id,
          'attachments': value.attachments != 0 ? 1 : 0,
          'comments': value.comments,
          'detail': value.detail,
          'prologues': value.prologues ? value.prologues : false,
          'justifications': value.prologues ? value.prologues.justifications : '',
          'evidence': value.prologues ? value.prologues.archiveName : '',
          'new_final_date': value.prologues ? value.prologues.final_date : '',
          'answered': value.answer ? true : false,
          'answer': value.answer,
          'annexed': value.archiveName,
          'level_educations': value.level_educations,
          'subjects': value.subjects,
          'positive': value.positive == 'yes' ? 1 : (value.positive == 'pending' ? value.positive : 0),
          'recommendations': value.recommendations,
          'jobs': value.jobs,
          'jobs_comments': value.jobs_comments,
          'correction': null,
        };
        if(value.type == 'groups'){
          this.showGroups = true;
          this.showAssign = false;
        } else {
          this.showAssign = true;
          this.showGroups = false;
        }
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
        this.teachers_groups = this.teachers_tmp;
        this.type = type.id;
      },
      saveReprocessing(value){
        console.log(value);
        var url = 'accepts';
        axios.post(url,{
          id: value.id,
          positive: value.positive,
          recommendations: value.recommendations,
        }).then(response => {
          //window.location.reload();
          this.cancel();
          //toastr.success('Nueva Tarea Creada Correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      saveCalification(value){
        var url = 'calification';
        axios.post(url,{
          id: value.id,
          jobs: value.jobs,
          jobs_comments: value.jobs_comments,
          correction: value.correction,
          positive: value.positive,
          final_date: value.correction == 1 ? value.final_date : '',
          new_final_date: value.correction == 1 ? value.new_final_date : '',
          justifications: value.correction == 1 ? value.justifications : '',
          evidence: value.correction == 1 ? value.evidence : '',
        }).then(response => {
          window.location.reload();
          this.cancel();
          toastr.success('Se ha realizado la Calificación correctamente');
        }).catch(error => {
          this.errors = error.response.data.errors;
        });
      },
      cancel(){
        this.activity = null;
        this.type = null;
        this.attachments = 0;
        this.teacher = null;
        this.tag = [];
        this.level_educations = [];
        this.subjects = [];
        this.start_date = '';
        this.final_date = '';
        this.comments = '';
        this.detail = '';
        this.teachers_group = '';
        this.find = {};
        this.errors = [];
        this.showCreate = false;
        this.showUpdate = false;
        this.showDelete = false;
        this.showGroups = false;
        this.showConfig = false;
        this.setPending = null;
        this.setInprocess = null;
        this.setPendingCheck = null;
        this.setFinish = null;
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
          //return value.tasks.name.toLowerCase().includes(this.search.toLowerCase()) 
          let teaching_periods_id = value.teaching_periods_id.toLocaleString().includes(this.teaching_period_id.toLocaleString())
          let teachers_id = value.teachers_id.toLocaleString().includes(this.teachers_id.toLocaleString())
          let status_id = value.status.includes(this.status_id) 
          return teaching_periods_id && teachers_id && status_id;
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
  .my-custom-scrollbar {
    position: relative;
    height: 200px;
    overflow: auto;
  }
  .table-wrapper-scroll-y {
    display: block;
  }
</style>