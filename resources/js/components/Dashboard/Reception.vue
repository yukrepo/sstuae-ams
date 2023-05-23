<template>
    <div>
        <div class="row synop mb-3">
            <div class="col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">NLIC Course's Appointments</h3>
                    </div>
                    <div class="card-body table-responsive p-0 day-content-box">
                        <table class="table table-condensed table-stripped m-0">
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
                                        <button type="button" class="btn btn-sm btn-warning" @click="completeCourseAppointment(appointment.appointment_code)"><i class="fas fa-plane-departure"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Todays Appointments
                            <small class="card-tools text-warning">Actions Required</small>
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0 m-0 day-content-box">
                        <table class="table table-condensed table-stripped m-0">
                            <thead>
                                <tr>
                                    <th>SNo</th>
                                    <th>Patient</th>
                                    <th>Therapy</th>
                                    <th>Therapist</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(appointment, akey) in fullappointments" :key="akey">
                                    <td>{{ akey+1 }}</td>
                                    <td>
                                        <router-link target="_blank" class="text-theme blank-btn text-uppercase font-weight-bold text-left" :to="'/appointments/view/'+appointment.appointment_code">{{ appointment.appointment_code }}</router-link><br>{{ appointment.first_name | capitalize }}</td>
                                    <td v-if="appointment.reason.length <= 30">
                                        {{ appointment.reason }}
                                    </td>
                                    <td v-else>
                                        {{ appointment.reason.substr(0,30)+'..' }}
                                    </td>
                                    <td>
                                        {{ appointment.doctor }} <br>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}
                                    </td>
                                    <td align="center">
                                        <label class="status-label-full" :class="appointment.status_css">
                                            <small>{{  appointment.status }}</small></label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                synops: '',
                products: '',
                imgurl: 'images/',
                appointments:'',
                profile:'',
                user:'',
                fullappointments:'',
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
            getResults() {
                axios.get('api/dashboard').then(({ data }) => (this.synops = data));
            },
            showProducts() {
                this.$Progress.start();
                this.products = '';
               // axios.get('api/dashboard/stocks').then(({ data }) => (this.products = data));
                this.$Progress.finish();
            },
             getProfile() {
                axios.get('/api/get-active-user').then(response => { this.user = response.data[0]; });
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            getAppointments() {
                axios.get('/api/get-dashboard-appointments').then(response => { this.appointments = response.data;  });
                axios.get('/api/get-todays-active-appointments').then(response => { this.fullappointments = response.data;  });
            },
            completeCourseAppointment(apcode) {
                swal.fire('updating', 'Please Keep Patience, This action is being updated', 'info');
            },
            cancelAppointment(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "Please enter the reason before cancelling the appointment.",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'off'  },
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Cancel it!',
                    cancelButtonText: 'Not Now',
                    preConfirm: (reason) => {
                        return axios.post('/api/cancel-appointment', {
                            appointment_code: id,
                            reason:reason
                        })
                        .then(response => {
                            Fire.$emit('changeyear');
                            swal.fire('Cancelled!', 'Appointment has been cancelled successfully.', 'success');
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
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
                    Fire.$emit('AfterCreate');
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
        },
        mounted() {
            this.getProfile();
            this.getAppointments();
        }
    }
</script>
