<template>
    <div class="appointmentDiv">
        <div class="container-fluid p-0">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-1">
                            <input type="text" class="form-control" v-model="sform.appointment_code" placeholder="App. ID">
                        </div>
                        <div class="col-md-1">
                            <vp-date-picker locale="en" format="YYYY-MM-DD" placeholder="Appointment Date" v-model="sform.date" :auto-submit="true" min="2019-10-06"></vp-date-picker>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.doctor_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Doctor/Therapist</option>
                                <option v-for="doctor in sdoctors" :key="doctor.id" v-bind:value="doctor.id">
                                    {{ doctor.name | capitalize }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.treatment_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Treatment/Consultation</option>
                                <option v-for="treatment in streatments" :key="treatment.id" v-bind:value="treatment.id">
                                    {{ treatment.treatment | capitalize }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
                        </div>
                        <div class="col-md-3 text-right">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                            <button class="btn btn-sm btn-danger" @click="clearFilter" type="button">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card mx-1">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Appointments
                        <small class="m-l-10" v-show="sform.search == 1">
                            <b class="text-warning">{{ appointments.total | freeNumber }}</b> Results found
                        </small>
                        <div class="card-tools">
                            <div class="btn-group" role="group" aria-label="First group">
                                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
                            </div>
                        </div>
                    </h3>
                </div>
                <div class="card-body p-0 m-0 table-responsive content-box-200">
                    <table id="upcomings" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="wf-50">SNo</th>
                                <th>App. Type</th>
                                <th>ID</th>
                                <th>Visit Type</th>
                                <th>Date</th>
                                <th>Timing</th>
                                <th>Patient</th>
                                <th>Treatment</th>
                                <th>Doctor</th>
                                <th class="wf-120">Status</th>
                                <th class="wf-150" v-show="profile.admin_type_id != 2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(appointment, sn) in appointments.data" :key="appointment.id">
                                <td>{{ (appointments.current_page - 1)*(appointments.per_page) + sn + 1 }}</td>
                                <td><label class="status-label-full-outline" :class="(appointment.appointment_type_id == 1)?'text-dark':'text-primary' ">{{ appointment.appointment_type }}</label>
                                </td>
                                <td>{{ appointment.appointment_code }}</td>
                                <td>{{ appointment.visit_type }}</td>
                                <td>{{ appointment.date | setdate }}</td>
                                <td>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                <td>
                                    <router-link class="btn btn-sm btn-warning" target="_blank" :to="'/customers/edit/'+appointment.user_id"  v-show="profile.admin_type_id != 2"> <i class="fas fa-user-edit"></i> </router-link>
                                    <button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewCustomer(appointment.user_id)"> {{ appointment.first_name }} {{ appointment.last_name }}</button></td>
                                <td>{{ appointment.reason }}</td>
                                <td>{{ appointment.doctor }}</td>
                                <td>
                                    <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                                </td>
                                <td v-show="profile.admin_type_id != 2">
                                    <a class="btn btn-primary btn-sm" :href="'/appointments/view/'+appointment.appointment_code"><i class="fas fa-desktop"></i></a>
                                    <button type="button" class="btn btn-sm btn-purple" v-show="((profile.admin_type_id == 1) || (profile.admin_type_id == 4)) && (appointment.course_id === null)" @click="shiftInCourse(appointment)"><i class="fas fa-exchange-alt"></i></button>
                                    <span class="btn-group editbox" role="group">
                                        <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <span class="dropdown-menu dropdown-menu-right m-0 p-0" aria-labelledby="btnGroupDrop1">
                                            <button class="dropdown-item text-left bb-1 p-2" @click="patientBox(appointment)">Change Patient</button>
                                            <button class="dropdown-item text-left bb-1 p-2" @click="doctorBox(appointment)">Modify Appointment</button>
                                            <button class="dropdown-item text-left bb-1 p-2 text-danger" @click="cancelAppointment(appointment.appointment_code)">Cancel Appointment</button>
                                        </span>
                                    </span>
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
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}</p></li>
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
        <div class="modal fade" id="adddoctorModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddoctorModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updatePatient()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDoctorModalTitle">Change Patient</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <label for="" class="control-label">Current Patient</label>
                                    <hr>
                                    <ul class="popup-list">
                                        <li><p class="alert m-0"><b>User ID</b><br>{{ customer.username }}</p></li>
                                        <li><p class="alert m-0"><b>Name</b><br>{{ customer.name }}</p></li>
                                        <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.contact_no }}</p></li>
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
                                <div class="col-6">
                                    <label for="" class="control-label">Choose Patient</label>
                                    <hr>
                                    <model-select :options="customers"
                                                            name="user_id"
                                                            id="user_id"
                                                            aria-required="true"
                                                            v-model="form.user_id"
                                                            placeholder="search patient" >
                                                </model-select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Update </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="doctorModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="doctorModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateDoctor()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDoctorModalTitle">Modify Appointment</h5>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                                <div class="col-md-8">
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-2 form-group">
                                            <label for="date" class="control-label">Change Date</label>
                                            <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date" :auto-submit="true" @change="onDateSelect"></vp-date-picker>
                                        </div>
                                        <div class="form-group col-md-3 col-4">
                                            <label for="visit_type_id" class="control-label">Visit Type</label>
                                            <select v-model="form.visit_type_id" name="visit_type_id" id="visit_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('visit_type_id') }" @change="resetFollowupCheck2">
                                                <option disabled value="">Select Visit Type</option>
                                                <option value="1">New Visit</option>
                                                <option value="2">Re-Visit (Free Follow Up)</option>
                                                <option value="3">Follow Up</option>
                                            </select>
                                            <has-error :form="form" field="visit_type_id"></has-error>
                                        </div>
                                        <div class="form-group col-md-3 col-4">
                                            <span v-show="form.visit_type_id >= 2">
                                                <label for="followup_appointment" class="control-label">Followup Appointment</label>
                                                <div class="input-group">
                                                    <input v-model="form.followup_appointment" type="text" name="followup_appointment" id="followup_appointment" placeholder="appointment ID"
                                                    class="form-control" :class="{ 'is-invalid': form.errors.has('followup_appointment') }" :readonly="form.followup_verified == 1">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-sm btn-outline-secondary" @click="checkFollowup2" type="button">Verify</button>
                                                    </div>
                                                </div>
                                                <has-error :form="form" field="appointment_type_id"></has-error>
                                            </span>
                                            <i class="text-danger d-block"><b> {{ followuptext }} </b></i>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="appointment_type_id" class="control-label">Appointment type</label>
                                                <select v-model="form.appointment_type_id" name="appointment_type_id" id="appointment_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('appointment_type_id') }">
                                                    <option disabled value="">Select Type</option>
                                                    <option value="1">Consultation</option>
                                                    <option value="2">Treatment</option>
                                                </select>
                                                <has-error :form="form" field="appointment_type_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <span v-if="form.appointment_type_id == 1">
                                                <label for="treatment_id" class="control-label">Consultaion</label>
                                                <select v-model="form.treatment_id" name="treatment_id" id="treatment_id" @click="showType" class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Consultaion</option>
                                                    <option v-for="consult in consultations" :key="consult.id" v-bind:value="consult.id">
                                                        {{ consult.treatment }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="treatment_id"></has-error>
                                            </span>
                                            <span v-else-if="form.appointment_type_id == 2">
                                                <label for="treatment_id" class="control-label">Treatments</label>
                                                <select v-model="form.treatment_id" name="treatment_id" id="treatment_id"  @click="showType" class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Treatments</option>
                                                    <option v-for="treatment in treatments" :key="treatment.id" v-bind:value="treatment.id">
                                                        {{ treatment.treatment }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="treatment_id"></has-error>
                                            </span>
                                            <span v-else>
                                                <label for="treatment_id" class="control-label">Treatment</label>
                                                <select v-model="form.treatment_id" name="treatment_id" id="treatment_id"  class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Appointment Type First</option>
                                                </select>
                                                <has-error :form="form" field="treatment_id"></has-error>
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <span v-if="form.appointment_type_id == 1">
                                                <label for="doctor_id" class="control-label">Doctors</label>
                                                <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(1, form.doctor_id, form.appointment_code)">
                                                    <option disabled value="">Select Doctor</option>
                                                    <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                        {{ doctor.name | capitalize }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="doctor_id"></has-error>
                                            </span>
                                            <span v-else-if="form.appointment_type_id == 2">
                                                <label for="doctor_id" class="control-label">Therapists</label>
                                                <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(2, form.doctor_id, form.appointment_code)">
                                                    <option disabled value="">Select Therapists</option>
                                                    <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                        {{ doctor.name | capitalize }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="doctor_id"></has-error>
                                            </span>
                                            <span v-else>
                                                <label for="doctor_id" class="control-label">Therapists/Doctors</label>
                                                <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }">
                                                    <option disabled value="">Select Appointment Type first</option>
                                                </select>
                                                <has-error :form="form" field="doctor_id"></has-error>
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label">Default Time Required</label>
                                            <p>{{ timetaken }}</p>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">

                                        <div class="col-3">
                                            <label for="start_time" class="control-label">Start Time</label>
                                            <select v-model="form.start_time" name="start_time" id="start_time" class="form-control" :class="{ 'is-invalid': form.errors.has('start_time') }" @change="getClosings(form.start_time, form.appointment_code)">
                                                <option disabled value="">Select Start Time</option>
                                                <option v-for="(timeslot, key) in start_times" :key="key" v-bind:value="key">
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="start_time"></has-error>
                                        </div>
                                        <div class="col-3">
                                            <label for="end_time" class="control-label">End Time</label>
                                            <select v-model="form.end_time" name="end_time" id="end_time" class="form-control" :class="{ 'is-invalid': form.errors.has('end_time') }" @change="getRooms(form.end_time)">
                                                <option disabled value="">Select End Time</option>
                                                <option v-for="(timeslot, key) in end_times" :key="key" v-bind:value="key">
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="end_time"></has-error>
                                        </div>
                                        <div class="col-3" v-show="ttype == 1">
                                            <label  class="control-label">This is Dual Therapy</label>
                                            <select v-model="form.second_doctor_id" name="second_doctor_id" id="second_doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('second_doctor_id') }">
                                                <option disabled value="">Select Therapists</option>
                                                <option v-for="doctor in stherapists" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="second_doctor_id"></has-error>

                                        </div>
                                        <div class="col-3">
                                            <span v-show="form.appointment_type_id == 2">
                                                <label for="room_id" class="control-label">Rooms</label>
                                                <select v-model="form.room_id" name="room_id" id="room_id" class="form-control" :class="{ 'is-invalid': form.errors.has('room_id') }">
                                                    <option disabled value="">Select Room</option>
                                                    <option v-for="(room, key) in rooms" :key="key" v-bind:value="key">
                                                        {{ room }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="room_id"></has-error>
                                            </span>
                                        </div>
                                    </div>
                                    <textarea name="remark" id="remark" rows="5" v-model="form.user_remark" class="form-control"></textarea>
                                </div>
                                <div class="col-md-4">
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
                                                        <td :class="[(isExist(naval_slots, slot.id)) ? 'bg-teal' : ((isExist(nfixedslots, slot.id)) ? 'bg-pink' : 'bg-opurple') ]"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Update </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
const _today = new Date();
const _todayComps = _today.getFullYear()+'-'+(_today.getMonth() + 1)+'-'+(_today.getDate()+1);
    export default {
        data() {
            return {
                asearch:'',
                customers: [],
                followuptext:'',
                currentYear: new Date().getFullYear(),
                yearList:[],
                activeYear: '',
                appointments:{},
                customer: {},
                listSlots: [],
                clistSlots: [],
                therapists: {},
                timeslots:'',
                start_timeslots: {},
                end_timeslots: {},
                rooms: {},
                customer: {},
                filteredOptions: [],
                options: [],
                sselected: '',
                blockslots: [],
                fixedslots: [],
                nblockslots: [],
                nfixedslots: [],
                ttype:'',
                timetaken:'',
                listoptions: [],
                start_times: {},
                end_times: {},
                stherapists: [],
                inputProps: {
                    id: "autosuggest__input",
                    name: 'customer',
                    onInputChange: this.onInputChange,
                    placeholder: "search by mobile number / user ID / ID Card"
                },
                activetype:'c',
                appointmentDate:'',
                appointmentFormatedDate:'',
                customer_appointments: [],
                aval_slots:[],
                naval_slots:[],
                consultations: {},
                doctors: {},
                form: new Form({
                    id:'',
                    location_id:'',
                    user_id:'',
                    room_id:'',
                    appointment_code:'',
                    doctor_id: '',
                    appointment_type_id:'',
                    treatment_id:'',
                    second_doctor_id:'',
                    date:'',
                    start_time:'',
                    end_time:'',
                    visit_type_id:'',
                    status_id:4,
                    followup_appointment:'',
                    course_id:'',
                    followup_verified:'',
                    user_remark:''
                }),
                eform: new Form({
                    appointment_code:'',
                    user_id:'',
                    room_id:'',
                    doctor_id: '',
                    treatment_id:'',
                    second_doctor_id:'',
                    date:'',
                    start_time:'',
                    end_time:'',
                }),
                loader:false,
                loaderurl:'/svg/loop.gif',
                sform: new Form({
                        date:'',
                        doctor_id:'',
                        treatment_id:'',
                        user_id:'',
                        appointment_code:'',
                        search:'',
                        year:''
                }),
                streatments:[],
                sdoctors:[],
                min_date: _todayComps,
                profile:this.$store.getters.user
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            getResults(page = 1) {
                 let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                this.$Progress.start();
                if(this.sform.search == 1) {
                    this.sform.post('/api/findUpcomingAppointment?page=' + page)
                    .then(({data}) => {this.appointments = data;});
                } else {
                    axios.get('/api/appointments/get-upcomings-yearwise/'+this.activeYear+'?page=' + page)
                    .then(response => {
                        this.appointments = response.data;
                    });
                }
                this.$Progress.finish();
            },
            showAppointments() {
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
                this.$Progress.start();
                let activeId = this.$route.path.split("/");
                this.activeYear = activeId[3];
                axios.get('/api/appointments/get-upcomings-yearwise/'+this.activeYear).then(({ data }) => {
                    this.appointments = data;
                });
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                 this.$Progress.finish();
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
            },
            yearsList() {
                axios.get('/api/getNxtYearsList').then(({ data }) => (this.yearList = data));
            },
            setSlots(slots) {
                //console.log(slots);
            },
            showType(){
                let treat = this.form.treatment_id;
                this.form.second_doctor_id = '';
                axios.get('/api/treatments/'+treat).then((data) => {
                     this.timetaken = data.data.timing+' mins';
                    if(data.data.is_it_dual >= 1){ this.ttype = 1; this.tdual = data.data.is_it_dual;}
                    else { this.ttype = 0; this.tdual = 0; }
                }).catch();
            },
            isExist(arr, cid) {
                for (let i = 0; i < arr.length; i++) {
                    if(cid == parseInt(arr[i])){
                        return true;
                    }
                };
                return false;
            },
            checkFollowup() {
                if(this.form.user_id && this.form.followup_appointment) {
                    this.followuptext = 'verifying...';
                    axios.post('/api/check-followup', {
                        user_id:this.form.user_id,
                        appointment_code:this.form.followup_appointment,
                    }).then((response) => {
                        if(response.data.status == 1) {
                            this.followuptext = 'Free visit applicable';
                            this.form.visit_type_id = 2;
                            this.form.followup_verified = 1;
                        }
                        else if(response.data.status == 2) {
                            this.followuptext = 'Followup applicable (Not free visit)';
                            this.form.visit_type_id = 3;
                            this.form.followup_verified = 1;
                        }
                        else if(response.data.status == 3) {
                            this.followuptext = 'Followup or free visit not applicable';
                            this.form.visit_type_id = 1;
                            this.form.followup_verified = '';
                        }
                        else {
                            this.followuptext = 'Either Appointment does not exist or not completed.';
                            this.form.visit_type_id = 1;
                            this.form.followup_verified = '';
                        }
                    }).catch();
                } else{
                    this.followuptext = 'please select patient and enter followup appointment ID.';
                }
            },
            getTimings(atype, did, apid = ''){
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+this.form.date+'&at='+atype+'&lid='+this.form.location_id+'&did='+did+'&apid='+apid).then(({ data }) => {
                    this.blockslots = data['blocked'];
                    this.fixedslots = data['fixed'];
                    this.start_times = data['start_slots'];
                    this.aval_slots = data['aval_slots'];
                });
            },
            getClosings(st, apid = ''){
                axios.get('/api/appointments/get-end-timings?q='+this.form.date+'&at='+this.form.appointment_type_id+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+st+'&apid='+apid).then(({ data }) => {
                    this.end_times = data;
                });
                /* this.start_times.forEach(([key, value]) => {
                    //console.log(key + ' - '+ value);
                }); */
            },
            getRooms(et, apid = ''){
                axios.get('/api/appointments/get-rooms?q='+this.form.date+'&lid='+this.form.location_id+'&st='+this.form.start_time+'&et='+et+'&apid='+apid).then(({ data }) => {
                    this.rooms = data;
                });
                if(this.ttype == 1){
                   axios.get('/api/appointments/get-second-therapist?q='+this.form.date+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+this.form.start_time+'&et='+et+'&dtype='+this.tdual).then(({ data }) => {
                    this.stherapists = data;
                });
                }
            },
            onDateSelect(){
                let apid = this.form.appointment_type_id;
                let did = this.form.doctor_id;
                let st = this.form.start_time;
                let et = this.form.end_time;
                if(this.form.appointment_code) {
                    this.getTimings(apid, did);
                    this.getClosings(st);
                    this.getRooms(et);
                }
            },
            checkFollowup2()
            {
                if(this.form.user_id && this.form.followup_appointment) {
                    this.followuptext = 'verifying...';
                    axios.post('/api/check-followup', {
                        user_id:this.form.user_id,
                        date:this.form.date,
                        appointment_code:this.form.followup_appointment,
                    }).then((response) => {
                        if(response.data.status == 1) {
                            this.followuptext = 'Free visit applicable';
                            this.form.visit_type_id = 2;
                            this.form.followup_verified = 1;
                            this.updateList();
                        }
                        else if(response.data.status == 2) {
                            this.followuptext = 'Followup applicable (Not free visit)';
                            this.form.visit_type_id = 3;
                            this.form.followup_verified = 1;
                            this.updateList();
                        }
                        else if(response.data.status == 3) {
                            this.followuptext = 'Followup or free visit not applicable';
                            this.form.visit_type_id = 1;
                            this.form.followup_verified = '';
                            this.updateList();
                        }
                        else {
                            this.followuptext = 'Either Appointment does not exist or not valid for free re-visit.';
                            this.form.visit_type_id = 1;
                            this.form.followup_verified = '';
                            this.updateList();
                        }
                    }).catch();
                } else{
                    this.followuptext = 'please select patient and enter followup appointment ID.';
                }
            },
            resetFollowupCheck2() {
                this.form.followup_appointment = '';
                this.form.followup_verified = '';
                this.followuptext = '';
                this.updateList();
            },
            updateList() {
                axios.get('/api/getConsultationsList?vtype='+this.form.visit_type_id).then((data) => {this.consultations = data.data }).catch();
            },
            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/appointments/upcoming-list/'+year);
                Fire.$emit('changeyear');
            },
            patientBox(apid) {
                this.form.appointment_code = apid.appointment_code;
                axios.get('/api/customers/'+apid.user_id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
                $('#adddoctorModal').modal('show');
            },
            updatePatient(){
                this.form.post('/api/appointments/change-patient')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('changeyear');
                    $('#adddoctorModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Patient has been updated successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                });
            },
            getAllAssets() {
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/getOptionsList').then((data) => {this.listoptions = data.data }).catch();
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
                axios.get('/api/get-time-slots').then(({data}) => {this.timeslots = data;});
            },
            doctorBox(apid) {
                let pi = this.form;
                axios.get('/api/appointments/form-view/'+apid.appointment_code)
                    .then((data) => {
                        axios.get('/api/treatments/'+data.data.treatment_id).then((data2) => {
                            this.timetaken = data2.data.timing+' mins';
                            if(data2.data.is_it_dual == 1){ this.ttype = 1;  }
                            else { this.ttype = 0;  }
                        }).catch();
                        axios.get('/api/appointments/get-doctor-appointment-timings?q='+data.data.date+'&at='+data.data.appointment_type_id+'&lid='+data.data.location_id+'&did='+data.data.doctor_id+'&apid='+data.data.appointment_code).then(( data3 ) => {
                            this.nblockslots = data3.data['blocked'];
                            this.nfixedslots = data3.data['fixed'];
                            this.start_times = data3.data['start_slots'];
                            this.naval_slots = data3.data['aval_slots'];
                        });
                        axios.get('/api/appointments/get-end-timings?q='+data.data.date+'&at='+data.data.appointment_type_id+'&lid='+data.data.location_id+'&did='+data.data.doctor_id+'&st='+data.data.start_timeslot+'&apid='+data.data.appointment_code).then(( data4 ) => {
                            this.end_times = data4.data;
                        });
                        axios.get('/api/appointments/get-rooms?q='+data.data.date+'&lid='+data.data.location_id+'&st='  +data.data.start_timeslot+'&et='+data.data.end_timeslot+'&apid='+data.data.appointment_code).then(( data5 ) => {
                            this.rooms = data5.data;
                        });
                        pi.fill(data.data);
                        this.form.start_time = data.data.start_timeslot;
                        this.form.end_time = data.data.end_timeslot;
                    })
                    .catch();
                $('#doctorModal').modal('show');
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
                            Fire.$emit('AfterCreate');
                            swal.fire('Cancelled!', 'Appointment has been cancelled successfully.', 'success');
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            },
            updateDoctor() {
                this.form.post('/api/appointments/update-appointment')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#doctorModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Patient has been updated successfully.'
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                });
            },
            searchAssets() {
                axios.get('/api/getDoctorsList').then(({ data }) => (this.sdoctors = data));
                axios.get('/api/getAllTreatmentsList').then(({ data }) => (this.streatments = data));
            },
            clearFilter() {
                this.sform.reset();
                this.showAppointments();
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                this.sform.year = this.activeYear;
                this.appointments = {};
                this.sform.post('/api/findUpcomingAppointment')
                    .then(({data}) => {
                        this.appointments = data;
                         this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            },
            shiftInCourse(appointment) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You want to shift this appointment under Prescribed Course.",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'off', placeholder: 'Enter course ID'  },
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Shift it!',
                    cancelButtonText: 'Not Now',
                     showLoaderOnConfirm: true,
                    preConfirm: (course_id) => {
                       return axios.post('/api/appointments/shift-in-course', {
                            user_id:appointment.user_id,
                            course_code:course_id,
                            apid:appointment.appointment_code
                        })
                        .then(response => {
                            //console.log(response.data.status);
                            if(response.data.status == 'error') {
                                throw new Error(response.data.message);
                            } else {
                                swal.fire('Shifted!', 'Appointment has been moved in prescribed course successfully. Please check there.', 'success');
                            }
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            }
        },
        created() {
            this.yearsList();
            this.showAppointments();
            this.getAllAssets();
            Fire.$on('changeyear', () => {
                this.showAppointments();
            });
             this.searchAssets();
        }
    }
</script>
