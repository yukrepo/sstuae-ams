<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Appointment Queries List</h3>
                        </div>
                        <div class="card-body p-0">
                            <table id="stock-alert" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="wf-50">ID</th>
                                        <th>Location</th>
                                        <th>Name</th>
                                        <th>Appointment Type</th>
                                        <th>Reason</th>
                                        <th>Date / Time</th>
                                        <th class="w-25">Remark</th>
                                        <th class="wf-100">Added On</th>
                                        <th class="wf-85">Status</th>
                                        <th class="wf-120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(query, sn) in queries.data" :key="query.id">
                                        <td>{{ (queries.current_page - 1)*(queries.per_page) + sn + 1 }}</td>
                                        <td>{{ query.location }}</td>
                                        <td><button class="text-theme text-left blank-btn text-uppercase font-weight-bold" @click="viewCustomer(query.user_id)">{{ query.first_name+' '+query.last_name }}<br>{{query.username}}</button> </td>
                                        <td>{{ query.appointment_type | capitalize }}<br><span>{{ query.doctor_name }}</span></td>
                                        <td>{{ query.appointment_reason }}</td>
                                        <td>{{ query.date }}<br>{{ query.timeframe }}</td>
                                        <td><p class="m-0" style="max-height: 70px; overflow: auto;">{{ query.remark }}</p></td>
                                        <td>{{ query.created_at | setdate }}</td>
                                        <td>
                                            <label class="status-label-full" :class="query.css">{{ query.status }}</label>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" @click="makeItApoointment(query)" v-show="query.status_id == 1">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <span class="btn btn-sm btn-dark" v-show="query.apid"> {{ query.apid }}</span>
                                            <button class="btn btn-danger btn-sm" @click="deleteQuery(query.id)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <pagination class="m-0 float-right" :data="queries" @pagination-change-page="getResults" :show-disabled="true">
                                <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                            </pagination>
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
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name+' '+customer.last_name }}</p></li>
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
        <div class="modal fullbarmodal fade" id="addApponitPopModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addApponitPopModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createAppointment()" class="popup-form">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAppointModalTitle">Create Appointment</h5>
                            <span class="float-right"> {{ paform.date}} </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <div class="alert alert-danger font-weight-bold text-uppercase py-1" v-show="catchmessage">
                                        {{ catchmessage }}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 border-right">
                                            <div class="form-group">
                                                <vp-date-picker locale="en" :min="today" format="YYYY-MM-DD" v-model="paform.date" :auto-submit="true" placeholder="select appointment date"></vp-date-picker>
                                            </div>
                                            <div class="row" v-show="paform.date">
                                                <div class="form-group col-md-9 col-xs-9">
                                                    <label for="user_id" class="control-label">Selected Patient</label>
                                                    <input type="text" readonly="readonly" class="form-control" v-model="paform.name">
                                                </div>
                                                <div class="form-group col-md-3 col-xs-3">
                                                    <label class="control-label m-b-0">View</label><br>
                                                    <a class="btn btn-sm btn-success p-b-0 p-t-1 view-btn" v-if="sselected != ''" href="javascript:;" @click="viewCustomer(paform.user_id)"> <i class="fas fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-secondary p-b-0 p-t-1 view-btn btn-block" v-else href="javascript:;"> <i class="fas fa-times"></i></a>
                                                    <a class="btn btn-sm btn-dark p-b-0 p-t-1 view-btn" v-show="sselected != ''" href="javascript:;" @click="hideCustomer"> <i class="fas fa-eye-slash"></i></a>
                                                </div>
                                                <div class="form-group col-md-4 col-xs-12">
                                                    <label for="visit_type_id" class="control-label">Visit Type</label>
                                                    <select v-model="paform.visit_type_id" name="visit_type_id" id="visit_type_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('visit_type_id') }" @change="resetFollowupCheck">
                                                        <option disabled value="">Select Visit Type</option>
                                                        <option value="1">New Visit</option>
                                                        <option value="2">Re-Visit (Free Follow Up)</option>
                                                        <option value="3">Follow Up</option>
                                                    </select>
                                                    <has-error :form="paform" field="visit_type_id"></has-error>
                                                </div>
                                                <div class="form-group col-md-8 col-xs-12">
                                                    <span v-show="paform.visit_type_id >= 2">
                                                        <label for="followup_appointment" class="control-label">Followup Appointment</label>
                                                        <div class="input-group">
                                                            <input v-model="paform.followup_appointment" type="text" name="followup_appointment" id="followup_appointment" placeholder="appointment ID"
                                                            class="form-control" :class="{ 'is-invalid': paform.errors.has('followup_appointment') }" :readonly="paform.followup_verified == 1">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-sm btn-outline-secondary" @click="checkFollowup" type="button">Verify</button>
                                                            </div>
                                                        </div>
                                                        <has-error :form="paform" field="appointment_type_id"></has-error>
                                                    </span>
                                                    <i class="text-danger d-block"><b> {{ followuptext }} </b></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 border-right">
                                            <div class="row" v-show="paform.date">
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <label for="appointment_type_id" class="control-label">Appointment type</label>
                                                    <select v-model="paform.appointment_type_id" name="appointment_type_id" id="appointment_type_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('appointment_type_id') }">
                                                        <option disabled value="">Select Type</option>
                                                        <option value="1">Consultation</option>
                                                        <option value="2">Treatment</option>
                                                    </select>
                                                    <has-error :form="paform" field="appointment_type_id"></has-error>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <span v-if="paform.appointment_type_id == 1">
                                                        <label for="treatment_id" class="control-label">Consultaion</label>
                                                        <select v-model="paform.treatment_id" name="treatment_id" id="treatment_id" @click="showType" class="form-control" :class="{ 'is-invalid': paform.errors.has('treatment_id') }">
                                                            <option disabled value="">Select Consultaion</option>
                                                            <option v-for="consult in consultations" :key="consult.id" v-bind:value="consult.id">
                                                                {{ consult.treatment }}
                                                            </option>
                                                        </select>
                                                        <has-error :form="paform" field="treatment_id"></has-error>
                                                    </span>
                                                    <span v-else-if="paform.appointment_type_id == 2">
                                                        <label for="treatment_id" class="control-label">Treatments</label>
                                                        <select v-model="paform.treatment_id" name="treatment_id" id="treatment_id"  @change="showType" class="form-control" :class="{ 'is-invalid': paform.errors.has('treatment_id') }">
                                                            <option disabled value="">Select Treatments</option>
                                                            <option v-for="treatment in treatments" :key="treatment.id" v-bind:value="treatment.id">
                                                                {{ treatment.treatment }}
                                                            </option>
                                                        </select>
                                                        <has-error :form="paform" field="treatment_id"></has-error>
                                                        <i class="d-block text-danger"> {{ gendermsg }} </i>
                                                    </span>
                                                    <span v-else>
                                                        <label for="treatment_id" class="control-label">Treatment</label>
                                                        <select v-model="paform.treatment_id" name="treatment_id" id="treatment_id"  class="form-control" :class="{ 'is-invalid': paform.errors.has('treatment_id') }">
                                                            <option disabled value="">Select Appointment Type First</option>
                                                        </select>
                                                        <has-error :form="paform" field="treatment_id"></has-error>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <label class="control-label">Default Time Required</label>
                                                    <p>{{ timetaken }}</p>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <span v-if="paform.appointment_type_id == 1">
                                                        <label for="doctor_id" class="control-label">Doctors</label>
                                                        <select v-model="paform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('doctor_id') }" @change="getTimings(1, paform.doctor_id)">
                                                            <option disabled value="">Select Doctor</option>
                                                            <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                                {{ doctor.name | capitalize }}
                                                            </option>
                                                        </select>
                                                        <has-error :form="paform" field="doctor_id"></has-error>
                                                    </span>
                                                    <span v-else-if="paform.appointment_type_id == 2">
                                                        <label for="doctor_id" class="control-label">Therapists</label>
                                                        <select v-model="paform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('doctor_id') }" @change="getTimings(2, paform.doctor_id)">
                                                            <option disabled value="">Select Therapists</option>
                                                            <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                                {{ doctor.name | capitalize }}
                                                            </option>
                                                        </select>
                                                        <has-error :form="paform" field="doctor_id"></has-error>
                                                    </span>
                                                    <span v-else>
                                                        <label for="doctor_id" class="control-label">Therapists/Doctors</label>
                                                        <select v-model="paform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('doctor_id') }">
                                                            <option disabled value="">Select Appointment Type first</option>
                                                        </select>
                                                        <has-error :form="paform" field="doctor_id"></has-error>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <label for="start_time" class="control-label">Start Time</label>
                                                    <select v-model="paform.start_time" name="start_time" id="start_time" class="form-control" :class="{ 'is-invalid': paform.errors.has('start_time') }" @change="getClosings(paform.start_time)">
                                                        <option disabled value="">Select Start Time</option>
                                                        <option v-for="(timeslot, key) in start_times" :key="key" v-bind:value="key">
                                                            {{ timeslot }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="paform" field="start_time"></has-error>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <label for="end_time" class="control-label">End Time</label>
                                                    <select v-model="paform.end_time" name="end_time" id="end_time" class="form-control" :class="{ 'is-invalid': paform.errors.has('end_time') }" @change="getRooms(paform.end_time)">
                                                        <option disabled value="">Select End Time</option>
                                                        <option v-for="(timeslot, key) in end_times" :key="key" v-bind:value="key">
                                                            {{ timeslot }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="paform" field="end_time"></has-error>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12" v-show="ttype == 1">
                                                    <label  class="control-label">This is Dual Therapy</label>
                                                    <select v-model="paform.second_doctor_id" name="second_doctor_id" id="second_doctor_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('second_doctor_id') }">
                                                        <option disabled value="">Select Therapists</option>
                                                        <option v-for="doctor in stherapists" :key="doctor.id" v-bind:value="doctor.id">
                                                            {{ doctor.name | capitalize }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="paform" field="second_doctor_id"></has-error>
                                                </div>
                                                <div class="form-group col-md-6 col-xs-12">
                                                    <span v-show="paform.appointment_type_id == 2">
                                                        <label for="room_id" class="control-label">Rooms</label>
                                                        <select v-model="paform.room_id" name="room_id" id="room_id" class="form-control" :class="{ 'is-invalid': paform.errors.has('room_id') }">
                                                            <option disabled value="">Select Room</option>
                                                            <option v-for="(room, key) in rooms" :key="key" v-bind:value="key">
                                                                {{ room }}
                                                            </option>
                                                        </select>
                                                        <has-error :form="paform" field="room_id"></has-error>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="m-b-10 p-b-5 m-l-0 m-r-0 table-responsive">
                                        <textarea class="form-control" v-model="paform.remark" placeholder="put remarks here"></textarea>
                                    </div>
                                    <div class="m-b-10 p-b-5 m-l-0 m-r-0 table-responsive" v-show="sselected">
                                        <table class="table table-bordered table-condensed table-striped">
                                            <thead>
                                                <tr>
                                                    <th>SNo</th>
                                                    <th>Appointment ID</th>
                                                    <th>Visit Type</th>
                                                    <th>Consultaion</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Doctor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(appts, acount) in customer_appointments" :key="acount">
                                                    <td> {{ ++acount }} </td>
                                                    <td> {{ appts.appointment_code }}</td>
                                                    <td> {{ appts.visit_type }} </td>
                                                    <td> {{ appts.treatment }} </td>
                                                    <td> {{ appts.date | setdate }} </td>
                                                    <td> {{ appts.timeslot }} </td>
                                                    <td> {{ appts.name | capitalize }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="doc-schdular">
                                        <div class="m-l-0 m-r-0">
                                            <ul class="patch-list">
                                                <li> <span class="color-patch bg-teal"></span> <label class="label-control"> Available Slot</label> </li>
                                                <li> <span class="color-patch bg-pink"></span> <label class="label-control"> Booked Slot</label> </li>
                                                <li> <span class="color-patch bg-opurple"></span> <label class="label-control"> Blocked Slot</label> </li>
                                            </ul>
                                        </div>
                                        <div class="dbody">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">TimeSlots</th>
                                                        <th>Schedule</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="slot in timeslots" :key="slot.id">
                                                        <td>{{ slot.time }}</td>
                                                        <td :class="[(isExist(aval_slots, slot.id)) ? 'bg-teal' : ((isExist(fixedslots, slot.id)) ? 'bg-pink' : 'bg-opurple') ]"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 3">
                            <button type="button" class="btn btn-wide btn-sm btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 3">
                            <button  type="submit" class="btn btn-wide btn-sm btn-success" v-else> Create </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
const _today = new Date();
    export default {
        data() {
            return {
                loader: false,
                profile:'',
                loaderurl: '/svg/loop.gif',
                today:_today.getFullYear()+'-'+(_today.getMonth()+1)+'-'+_today.getDate(),
                catchmessage:'',
                cmessage:'',
                editmode: true,
                queries: {},
                customer: {},
                form: new Form({
                    id:'',
                    status_id:'',
                }),
                followuptext:'',
                customer_appointments:'',
                timeslots:'',
                timetaken:'',
                aval_slots:[],
                treatments:{},
                consultations: {},
                doctors: {},
                listSlots: [],
                clistSlots: [],
                therapists: {},
                start_timeslots: {},
                end_timeslots: {},
                rooms: {},
                customers: [],
                customer:'',
                sselected: '',
                blockslots: [],
                fixedslots: [],
                ttype:'',
                tdual:0,
                start_times: {},
                end_times: {},
                stherapists: {},
                paform: new Form({
                    id:'',
                    date:'',
                    location_id:'',
                    user_id:'',
                    room_id:'',
                    doctor_id: '',
                    appointment_type_id:'',
                    treatment_id:'',
                    second_doctor_id:'',
                    start_time:'',
                    end_time:'',
                    visit_type_id:'',
                    status_id:4,
                    followup_appointment:'',
                    followup_verified:'',
                    course_id:'',
                    name:'',
                    remark:'',
                    rsn:''
                }),
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/appointment-queries?page=' + page)
                    .then(response => {
                        this.queries = response.data;
                    });
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
            },
            showAppointmentQueries() {
                this.$Progress.start();
                axios.get('/api/appointment-queries').then(({ data }) => {
                    this.queries = data;
                    this.$Progress.finish();
                });
            },
            updateQuery() {
                this.form.put('/api/appointment-queries/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addqueryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'query details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editQuery(query) {
                this.form.fill(query);
                $('#addqueryModal').modal('show');
            },
            deleteQuery(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('/api/appointment-queries/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            },
            resetFollowupCheck() {
                this.paform.followup_appointment = '';
                this.paform.followup_verified = '';
                this.followuptext = '';
            },
            isExist(arr, cid) {
                for (let i = 0; i < arr.length; i++) {
                    if(cid == parseInt(arr[i])){
                        return true;
                    }
                };
                return false;
            },
            makeItApoointment(query) {
                this.getAllAssets();
                this.getTimeSlots();
                axios.get('/api/get-active-user').then(response => { this.profile = response.data; });
                this.onCustomerSelect(query.user_id);
                this.catchmessage = '';
                this.sselected = '';
                this.followuptext = '';
                this.paform.reset();
                this.blockslots = [];
                this.fixedslots = [];
                this.start_times = [];
                this.paform.location_id = this.profile.location_id;
                this.paform.user_id = query.user_id;
                this.paform.date = query.date;
                this.paform.rsn = query.id;
                $('#addApponitPopModal').modal('show');
            },
            createAppointment() {
                if(this.paform.visit_type_id >= 2) {
                    if((this.paform.followup_appointment == '') || (this.paform.followup_verified != 1)) {
                        swal.fire('Please enter Followup Appointment ID and verify it.');
                        return false;
                    }
                }
                this.loader = 3;
                this.paform.post('/api/appointments/make-appointment')
                .then((response) => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    this.loader = false;
                    $('#addApponitPopModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Appointment created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
            },
            hideCustomer() {
                $('#userModal').modal('hide');
            },
            showType(){
                let treat = this.paform.treatment_id;
                this.paform.second_doctor_id = '';
                axios.get('/api/treatments/'+treat).then((data) => {
                        this.timetaken = data.data.timing+' mins';
                    if(data.data.is_it_dual >= 1){ this.ttype = 1; this.tdual = data.data.is_it_dual;}
                    else { this.ttype = 0; this.tdual = 0; }
                }).catch();
            },
            getTimings(atype, did){
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+this.paform.date+'&at='+atype+'&lid='+this.paform.location_id+'&did='+did).then(({ data }) => { this.blockslots = data['blocked']; this.fixedslots = data['fixed'];  this.start_times = data['start_slots']; this.aval_slots = data['aval_slots']; });
            },
            getClosings(st){
                axios.get('/api/appointments/get-end-timings?q='+this.paform.date+'&at='+this.paform.appointment_type_id+'&lid='+this.paform.location_id+'&did='+this.paform.doctor_id+'&st='+st).then(({ data }) => {
                    this.end_times = data;
                });
            },
            getRooms(et){
                axios.get('/api/appointments/get-rooms?q='+this.paform.date+'&lid='+this.paform.location_id+'&st='+this.paform.start_time+'&et='+et).then(({ data }) => {
                    this.rooms = data;
                });
                if(this.ttype == 1){
                    axios.get('/api/appointments/get-second-therapist?q='+this.paform.date+'&lid='+this.paform.location_id+'&did='+this.paform.doctor_id+'&st='+this.paform.start_time+'&et='+et+'&dtype='+this.tdual).then(({ data }) => {
                    this.stherapists = data;
                });
                }
            },
            getAllAssets() {
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/getTherapistsArrayList').then((data) => {this.extratherapists = data.data }).catch();
                axios.get('/api/getOptionsList').then((data) => {this.listoptions = data.data }).catch();
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            checkFollowup()
            {
                if(this.paform.user_id && this.paform.followup_appointment) {
                    this.followuptext = 'verifying...';
                    axios.post('/api/check-followup', {
                        user_id:this.paform.user_id,
                        date:this.paform.date,
                        appointment_code:this.paform.followup_appointment,
                    }).then((response) => {
                        if(response.data.status == 1) {
                            this.followuptext = 'Free visit applicable';
                            this.paform.visit_type_id = 2;
                            this.paform.followup_verified = 1;
                        }
                        else if(response.data.status == 2) {
                            this.followuptext = 'Followup applicable (Not free visit)';
                            this.paform.visit_type_id = 3;
                            this.paform.followup_verified = 1;
                        }
                        else if(response.data.status == 3) {
                            this.followuptext = 'Followup or free visit not applicable';
                            this.paform.visit_type_id = 1;
                            this.paform.followup_verified = '';
                        }
                        else {
                            this.followuptext = 'Either Appointment does not exist or not valid for free re-visit.';
                            this.paform.visit_type_id = 1;
                            this.paform.followup_verified = '';
                        }
                    }).catch();
                } else{
                    this.followuptext = 'please select patient and enter followup appointment ID.';
                }
            },
            getTimeSlots(){
                axios.get('/api/get-all-time-slots').then(({ data }) => { this.timeslots = data; });
            },
            onCustomerSelect(option)
            {
                this.gendermsg = '';
                axios.get('/api/customers/'+option)
                .then((data) => {
                    this.sselected = data.data[0].id;
                    this.paform.user_id = data.data[0].id;
                    this.paform.name = data.data[0].username+' - '+data.data[0].first_name;
                    axios.post('/api/appointments/get-users-quick-history', {
                        user_id: data.data[0].id,
                        date: this.paform.date
                    })
                    .then((response) => {
                        this.customer_appointments = response.data;
                    });
                    if(data.data[0].insurance_id == 1) {
                        this.catchmessage = 'This patient is a cash customer. Please check if he has insurance now.';
                    }
                    else {
                        let today = new Date(this.paform.date);
                        let ndate = new Date(data.data[0].expiry_date);
                        var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                        var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                        var diffDuration = moment.duration(b.diff(a));
                        var days = diffDuration.as('days');
                        if(Math.sign(days) == '-1'){
                            this.catchmessage = 'Insurance Policy will be expired before appointment date.';
                        }
                        else if(days == 0) {
                            this.catchmessage = 'Insurance Policy is expiring on appointment date. Inform Customer to renew it on time.';
                        } else if(15 >= days > 0) {
                            this.catchmessage = days +' day(s) left to expire the Insurance as compare to appointment date.';
                        } else {
                            this.catchmessage = '';
                        }
                    }
                })
                .catch();
            },
        },
        created() {
            Fire.$on('searching', () => {
                axios.get('/api/findQuery?q='+this.$parent.search)
                    .then((data) => {
                        this.queries = data.data;
                    })
                    .catch();
            });
            this.showAppointmentQueries();
            Fire.$on('AfterCreate', () => {
                this.showAppointmentQueries();
            });

            //setInterval(() -> this.showAppointmentQueries(), 3000);
        }
    }
</script>
