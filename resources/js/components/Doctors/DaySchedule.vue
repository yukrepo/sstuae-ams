<template>
    <div>
        <div class="content appointmentDiv m-t-10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Today's Appointments</h3>
                            </div>
                            <div class="card-body p-0">
                                 <table id="upcomings" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>ID</th>
                                            <th>Visit Type</th>
                                            <th>App. Type</th>
                                            <th>Timing</th>
                                            <th>Patient</th>
                                            <th>Treatment</th>
                                            <th class="wf-75">Added</th>
                                            <th class="wf-50 text-center">Invoice</th>
                                            <th class="wf-120 text-center">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(appointment, sn) in appointments.data" :key="appointment.id">
                                            <td>{{ ++sn }}</td>
                                            <td>{{ appointment.appointment_code }}</td>
                                            <td>{{ appointment.visit_type }}</td>
                                            <td>{{ appointment.appointment_type }}</td>
                                            <td>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                           <td><button class="text-theme blank-btn text-uppercase font-weight-bold text-left" @click="viewCustomer(appointment.user_id)">{{ appointment.first_name | capitalize }} {{ appointment.last_name | capitalize }}</button></td>
                                            <td>{{ appointment.reason }}</td>
                                            <td>{{ appointment.created_at | setdate }}</td>
                                             <td align="center" v-if="appointment.ainvoice">
                                                <label class="status-label-full bg-teal"> <i class="fas fa-check"></i> </label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label-full bg-pink"> <i class="fas fa-times"></i> </label>
                                            </td>
                                            <td align="center">
                                                <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                                            </td>
                                            <td v-if="[2,4,8,9,10].indexOf(appointment.status_id) >= 1">
                                                <!-- <router-link v-if="(appointment.status_id == 4) && (appointment.ainvoice)" class="btn btn-warning btn-sm" :to="'/doctors/prescribe/'+appointment.appointment_code"><i class="fas fa-edit"></i></router-link>
                                                <router-link v-else-if="appointment.status_id == 5" class="btn btn-warning btn-sm" :to="'/doctors/prescribe/'+appointment.appointment_code"><i class="fas fa-edit"></i></router-link> -->
                                                <router-link target="_blank" class="btn btn-warning btn-sm" :to="'/doctors/prescribe/'+appointment.appointment_code"><i class="fas fa-edit"></i></router-link>
                                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/doctors/view-appointment/'+appointment.appointment_code"><i class="fas fa-laptop"></i></router-link>
                                            </td>
                                            <td v-else>
                                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/doctors/view-appointment/'+appointment.appointment_code"><i class="fas fa-laptop"></i></router-link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :limit="12" :data="appointments" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="userModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Customer Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>User ID</b><br>{{ customer.username }}</p></li>
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name }} {{ customer.last_name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ customer.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.mobile }}</p></li>
                            <li><p class="alert m-0"><b>City</b><br>{{ customer.city }}</p></li>
                            <li><p class="alert m-0"><b>Address</b><br>{{ customer.address }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ customer.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Identity Card</b><br>{{ customer.verification_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.identity_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Insurance</b><br>{{ customer.policy_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.insurance_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ customer.status }}</p></li>
                        </ul>
                    </div>
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
                catchmessage: '',
                activeYear: this.$route.params.id,
                active_location: '',
                appointments:{},
                treatments:{},
                consultations: {},
                doctors: {},
                listSlots: [],
                clistSlots: [],
                therapists: {},
                start_timeslots: {},
                end_timeslots: {},
                rooms: {},
                customer: {},
                filteredOptions: [],
                options: [],
                sselected: '',
                ttype:'',
                listoptions: [],
                start_times: {},
                end_times: {},
                rooms:{}
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            getResults(page = 1) {
                axios.get('/api/appointments/get-todays-appointments?page=' + page)
                    .then(response => {
                        this.appointments = response.data;
                    });
            },
            viewCustomer(id) {
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
                $('#userModal').modal('show');
            },
            hideCustomer() {
                $('#userModal').modal('hide');
            },
            showType(){
                let tType = this.form.treatment_id;
                axios.get('/api/treatments/'+tType).then((data) => {
                    if(data.data.is_it_dual == 1){ this.ttype = 1; }
                    else { this.ttype = 0; }
                }).catch();
            },
            getTimings(atype, did){
                axios.get('/api/appointments/get-start-timings?q='+this.form.date+'&at='+atype+'&lid='+this.form.location_id+'&did='+did).then(({ data }) => {
                    this.start_times = data;
                });
            },
            getClosings(st){
                axios.get('/api/appointments/get-end-timings?q='+this.form.date+'&at='+this.form.appointment_type_id+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+st).then(({ data }) => {
                    this.end_times = data;
                });
            },
            getRooms(et){
                axios.get('/api/appointments/get-rooms?q='+this.form.date+'&lid='+this.form.location_id+'&st='+this.form.start_time+'&et='+et).then(({ data }) => {
                    this.rooms = data;
                });
            },
            getAllAssets() {
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/getOptionsList').then((data) => {this.listoptions = data.data }).catch();
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            getPrimaryAssets() {
                axios.get('/api/get-active-user').then((response) => {this.active_location = response.data[0].location_id;}).catch();
            },
            showAppointments() {
                this.$Progress.start();
                axios.get('/api/appointments/get-todays-appointments').then(({ data }) => {
                    this.appointments = data;
                    this.$Progress.finish();
                });
            },
            checkRefresh() {
                this.showAppointments();
            }
        },
        mounted() {
            this.getPrimaryAssets();
            this.showAppointments();
            this.getAllAssets();
            Fire.$on('AfterCreate', () => {
                this.showAppointments();
            });
            setInterval(this.checkRefresh, 40000);
        }

    }
</script>
