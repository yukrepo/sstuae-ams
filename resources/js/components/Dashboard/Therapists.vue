<template>
    <div>
        <div class="row synop mb-3">
            <div class="col-md-6 col-sm-6 col-12 m-b-20">
                <div class="card m-b-20">
                    <div class="card-header">
                            <h3 class="card-title">Today's Appointments</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <thead>
                                <tr>
                                    <th>SNo</th>
                                    <th>Appointment</th>
                                    <th>Patient</th>
                                    <th>Therapy</th>
                                    <th>Therapist</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(appointment, akey) in appointments" :key="akey">
                                    <td>{{ akey+1 }}</td>
                                    <td>
                                        <router-link target="_blank" class="text-theme blank-btn text-uppercase font-weight-bold text-left" :to="'/appointments/courses/view/'+appointment.course_id">{{ appointment.course_id }}</router-link>
                                        <br>
                                        <router-link target="_blank" class="text-theme blank-btn text-uppercase font-weight-bold text-left" :to="'/appointments/view/'+appointment.appointment_code">{{ appointment.appointment_code }}</router-link>
                                    </td>
                                    <td>{{ appointment.first_name | capitalize }}</td>
                                    <td>{{ appointment.reason }}</td>
                                        <td>{{ appointment.doctor }} <br>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                    <td align="center">
                                        <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" @click="completeAppointment(appointment.appointment_code)"><i class="fas fa-plane-departure"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-12 m-b-20">
                <div class="card m-b-20">
                    <div class="card-header">
                        <h3 class="card-title">TODAYS REPORT</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row p-3">
                            <div class="col-6 text-uppercase border-right">
                                <h5>No Of Appointments <label class="float-right bg-danger"> {{ score.todaycount | freeNumber }} </label> </h5>
                            </div>
                            <div class="col-6 text-uppercase">
                                <h5>Your APPOINTMENTS  <label class="float-right bg-danger"> {{ score.todayscore | freeNumber }} </label> </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-b-20">
                    <div class="card-header">
                        <h3 class="card-title">SCOREBOARD</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row p-3">
                            <div class="col-6 text-uppercase border-right">
                                <h5>Yesterday APPOINTMENTS <label class="float-right bg-danger"> {{ score.dayscore | freeNumber }}</label></h5>
                                <div class="text-center p-4">
                                    <span v-if="score.dayscore <= 3">
                                        <img class="wf-175" :src="imgurl+'poor.png'">
                                        <p class="text-danger p-t-20">Oops! Not to worry.<br> Next day ahead.</p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 3) && (score.dayscore <= 4.5)">
                                        <img class="wf-175" :src="imgurl+'like.png'" >
                                        <p class="text-info p-t-20">Ohh! Little more effort needed !!</p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 4.5) && (score.dayscore <= 5.5)">
                                        <img class="wf-175" :src="imgurl+'good.png'">
                                        <p class="text-primary p-t-20">Well! You are doing good !!</p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 5.5) && (score.dayscore <= 6.5)">
                                        <img class="wf-175" :src="imgurl+'excellent.png'">
                                        <p class="text-primary p-t-20">Wow! Excellent Work. You are too close to be <span class="text-uppercase">Star of the day.</span></p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 6.5)">
                                        <img class="wf-175" :src="imgurl+'day-star.png'">
                                        <p class="text-success p-t-20">Wow! You are</p>
                                        <h4 class="text-uppercase font-weight-bold text-theme">star of the day</h4>
                                    </span>
                                    <span v-else>
                                        <img class="wf-175" :src="imgurl+'missing.png'">
                                        <p class="text-danger p-t-20">What! your score is missing !!</p>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 text-uppercase">
                                <h5>Previous Month APPOINTMENTS <label class="float-right bg-success"> {{ score.monthscore | freeNumber }}</label> </h5>
                                <div class="text-center p-4">
                                    <span v-if="(score.monthscore >= 150)">
                                        <img class="wf-175" :src="imgurl+'month-star.png'">
                                        <p class="text-success p-t-20">Wow! You are</p>
                                        <h4 class="text-uppercase font-weight-bold text-theme">star of the MONTH</h4>
                                    </span>
                                    <span v-else-if="(score.monthscore > 1) && (score.monthscore < 150)">
                                        <img class="wf-175" :src="imgurl+'star.png'">
                                        <p class="text-primary p-t-20">Well! You did good last month.<br>Keep working.<br> You can also be <span class="text-uppercase">Star of This Month.</span>.</p>
                                    </span>
                                    <span v-else>
                                        <img class="wf-175" :src="imgurl+'missing.png'">
                                        <p class="text-danger p-t-20">What! your APPOINTMENTS is missing !!</p>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="cformModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="cformModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateComment">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addxrayModalTitle">Add Your comment and Close Appointment</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="comment" class="control-label">Your Comment</label>
                                    <select v-model="cform.comment" name="comment" id="comment" class="form-control" :class="{ 'is-invalid': cform.errors.has('comment') }">
                                        <option value="" disabled>Select Comment</option>
                                        <option :value="com.comment" v-for="(com, comid) in comments" :key="'com'+comid">
                                            {{ com.comment }}
                                        </option>
                                        <option value="other">Other</option>
                                    </select>
                                    <has-error :form="cform" field="comment"></has-error>
                                </div>
                                <div class="form-group col-12" v-if="cform.comment == 'other'">
                                    <label for="comment" class="control-label">Please Add Comment Here</label>
                                    <input type="text" class="form-control" v-model="cform.other">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Close Now </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,
                score: [],
                imgurl: 'images/',
                appointments:'',
                profile:'',
                user:'',
                clistSlots:[],
                listSlots:[],
                comments:{},
                cform: new Form({
                    appointment_code:'',
                    comment:'',
                    other:''
                }),
            }
        },
        methods: {
            getAppointments() {
                axios.get('/api/get-dashboard-appointments').then(response => { this.appointments = response.data;  });

            },
            updateComment() {
                if((this.cform.comment == 'other') && (this.cform.other == '')) {
                    swal.fire({
                        type:'error',
                        title:'Please fill all the fields.'});
                    return false;
                }
                this.cform.post('/api/appointments/update-comment').then(() => {
                    this.$Progress.start();
                    this.getAppointments();
                    this.cform.reset();
                    $('#cformModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Appointment has been closed successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                   console.lof(response);
                });
            },

            completeAppointment(id) {
                axios.get('/api/getCommentsList').then((data) => { this.comments = data.data; });
                $('#cformModal').modal('show');
                this.cform.appointment_code = id;
            },
            getProfile() {
                axios.get('/api/get-active-user').then(response => { this.user = response.data[0]; });
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            getScore() {
                axios.get('/api/get-dashboard-score').then(response => { this.score = response.data;  });
            }
        },
        beforeMount() {
             this.getProfile();
        },
        mounted() {
            this.getScore();
            this.getAppointments();
         //   this.getResults();
         //   this.showProducts();
            //setInterval(() => { this.getResults()}, 3000);
        }
    }
</script>
