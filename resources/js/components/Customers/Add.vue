<template>
    <div>
        <div class="content bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form @submit.prevent="createCustomer()">
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Add New Customer</h3>
                                </div>
                                <div class="full-form-body">
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="first_name" class="control-label">First Name <span class="text-danger">*</span> </label>
                                                <input type="text" v-model="form.first_name" name="first_name" id="first_name" class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }">
                                                <has-error :form="form" field="first_name"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="last_name" class="control-label">Last Name</label>
                                                <input type="text" v-model="form.last_name" name="last_name" id="last_name" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }">
                                                <has-error :form="form" field="last_name"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="mobile" class="control-label">Mobile Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">971</span>
                                                    </div>
                                                    <input type="text" v-model="form.mobile" name="mobile" id="mobile" class="form-control" maxlength="9" :class="{ 'is-invalid': form.errors.has('mobile') }">
                                                    <has-error :form="form" field="mobile"></has-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="email" class="control-label">Email</label>
                                                <input type="email" v-model="form.email" name="email" id="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="date_of_birth" class="control-label">Date Of Birth</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date_of_birth" :auto-submit="true"></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="gender" class="control-label">Gender <span class="text-danger">*</span></label>
                                                <select v-model="form.gender" name="gender" id="gender" class="form-control" :class="{ 'is-invalid': form.errors.has('gender') }">
                                                    <option disabled value="">Select Gender</option>
                                                    <option value="0">Unknown</option>
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                </select>
                                                <has-error :form="form" field="gender"></has-error>
                                            </div>
                                        </div>
                                        <!-- <div class="col-2">
                                            <div class="form-group">
                                                <label for="married" class="control-label">Marrital Status<span class="text-danger">*</span></label>
                                                <select v-model="form.married" name="married" id="married" class="form-control" :class="{ 'is-invalid': form.errors.has('married') }">
                                                    <option disabled value="">Select Status</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="divorced">Divorced</option>
                                                </select>
                                                <has-error :form="form" field="married"></has-error>
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="contact_no" class="control-label">Whatsapp Number</label>
                                                <input type="text" v-model="form.contact_no" name="contact_no" id="contact_no" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address" class="control-label">Address</label>
                                                <input type="text" v-model="form.address" name="address" id="address" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="city" class="control-label">City</label>
                                                <input type="text" v-model="form.city" name="city" id="city" class="form-control">
                                            </div>
                                        </div>
                                       <!--  <div class="col-2">
                                            <div class="form-group">
                                                <label for="zipcode" class="control-label">Zipcode</label>
                                                <input type="text" v-model="form.zipcode" name="zipcode" id="zipcode" class="form-control">
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="country" class="control-label">Country</label>
                                                <input type="text" readonly value="UAE" class="form-control">
                                            </div>
                                        </div>
                                         <div class="col-2">
                                            <div class="form-group">
                                                <label for="nationality_id" class="control-label">nationality<span class="text-danger">*</span></label>
                                                <select v-model="form.nationality_id" name="nationality_id" class="form-control" :class="{ 'is-invalid': form.errors.has('nationality_id') }">
                                                    <option disabled value="">Select Nationality</option>
                                                    <option v-for="nation in nationalities" :key="nation.id" v-bind:value="nation.id">
                                                        {{ nation.nationality }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="nationality_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="company_name" class="control-label">Company name</label>
                                                <input type="text" v-model="form.company_name" name="company_name" id="company_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="identity_type_id" class="control-label">Identity Type<span class="text-danger">*</span></label>
                                                <select v-model="form.identity_type_id" name="identity_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('identity_type_id') }">
                                                    <option disabled value="">Select Identity Type</option>
                                                    <option v-for="indentity in indentities" :key="indentity.id" v-bind:value="indentity.id">
                                                        {{ indentity.value }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="identity_type_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="verification_number" class="control-label">ID Number</label>
                                                <input type="text" v-model="form.verification_number" name="verification_number" id="verification_number" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="identity_copy" class="control-label">Identity Copy</label>
                                                <input type="file" @change="uploadIdCopy" placeholder="upload identity copy" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <!-- <div class="form-group">
                                                <label for="concern_form" class="control-label">concern form</label>
                                                <input type="file" @change="uploadCFCopy" placeholder="upload concern form" class="form-control">
                                            </div> -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="insurance_id" class="control-label">Payment Mode<span class="text-danger">*</span></label>
                                                <select v-model="form.insurance_id" name="insurance_id" class="form-control" :class="{ 'is-invalid': form.errors.has('insurance_id') }">
                                                    <option disabled value="">Select Insurance Company</option>
                                                    <option v-for="insurance in insurancers" :key="insurance.id" v-bind:value="insurance.id">
                                                        {{ insurance.name }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="insurance_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="policy_number" class="control-label">Card Number</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false" v-model="form.policy_number" name="policy_number" id="policy_number" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="effective_date" class="control-label">Effective Date</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.effective_date" :auto-submit="true" :disabled="(this.form.insurance_id == 1)?true : false" ></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="expiry_date" class="control-label">Expiry</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.expiry_date" :auto-submit="true" :disabled="(this.form.insurance_id == 1)?true : false"></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group" v-show="(this.form.insurance_id == 1)?false : true">
                                                <label for="insurance_copy" class="control-label">Insurance Copy</label>
                                                <input type="file"  @change="uploadICopy" placeholder="upload insurance copy" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="consult_deductable" class="control-label">Consultation Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.consult_deductable" name="consult_deductable" id="consult_deductable" class="form-control">
                                            </div>
                                        </div>
                                       <!--  <div class="col-2">
                                            <div class="form-group">
                                                <label for="treatment_deductable" class="control-label">treatment Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.treatment_deductable" name="treatment_deductable" id="treatment_deductable" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="pharmacy_deductable" class="control-label">pharmacy Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.pharmacy_deductable" name="pharmacy_deductable" id="pharmacy_deductable" class="form-control">
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="co-insurance" class="control-label">Co insurance</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.co_insurance" name="co-insurance" id="co-insurance" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-10">
                                        </div>
                                        <div class="col-2">
                                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 1">
                                            <button type="submit" class="btn btn-block btn-sm btn-dark" v-else>Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="addApponitModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addApponitModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createAppointment()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAppointModalTitle">Create Appointment</h5>
                            <span class="text-right float-right">
                                <b class="text-uppercase m-r-5">DATE : </b> {{ new Date() | setdate }}
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row border-bottom m-b-10 p-b-15 m-l-0 m-r-0">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="date" class="control-label">Change Date</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="aform.date"  :auto-submit="true" @change="onDateSelect"></vp-date-picker>
                                                <has-error :form="aform" field="date"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label for="visit_type_id" class="control-label">Visit Type</label>
                                            <select v-model="aform.visit_type_id" name="visit_type_id" id="visit_type_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('visit_type_id') }">
                                                <option disabled value="">Select Visit Type</option>
                                                <option value="1">New Visit</option>
                                                <option value="2">Re-Visit</option>
                                                <option value="3">Follow Up</option>
                                            </select>
                                            <has-error :form="aform" field="visit_type_id"></has-error>
                                        </div>
                                        <div class="col-3" v-show="aform.visit_type_id == 3">
                                            <label for="followup_appointment" class="control-label">Followup Appointment</label>
                                            <input v-model="aform.followup_appointment" type="text" name="followup_appointment" id="followup_appointment" placeholder="enter appointment code"
                                                class="form-control" :class="{ 'is-invalid': aform.errors.has('followup_appointment') }">
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="appointment_type_id" class="control-label">Appointment type</label>
                                                <select v-model="aform.appointment_type_id" name="appointment_type_id" id="appointment_type_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('appointment_type_id') }">
                                                    <option disabled value="">Select Type</option>
                                                    <option value="1">Consultation</option>
                                                    <option value="2">Treatment</option>
                                                </select>
                                                <has-error :form="aform" field="appointment_type_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <span v-if="aform.appointment_type_id == 1">
                                                <label for="treatment_id" class="control-label">Consultaion</label>
                                                <select v-model="aform.treatment_id" name="treatment_id" id="treatment_id" @click="showType" class="form-control" :class="{ 'is-invalid': aform.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Consultaion</option>
                                                    <option v-for="consult in consultations" :key="consult.id" v-bind:value="consult.id">
                                                        {{ consult.treatment }}
                                                    </option>
                                                </select>
                                                <has-error :form="aform" field="treatment_id"></has-error>
                                            </span>
                                            <span v-else-if="aform.appointment_type_id == 2">
                                                <label for="treatment_id" class="control-label">Treatments</label>
                                                <select v-model="aform.treatment_id" name="treatment_id" id="treatment_id"  @click="showType" class="form-control" :class="{ 'is-invalid': aform.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Treatments</option>
                                                    <option v-for="treatment in treatments" :key="treatment.id" v-bind:value="treatment.id">
                                                        {{ treatment.treatment }}
                                                    </option>
                                                </select>
                                                <has-error :form="aform" field="treatment_id"></has-error>
                                            </span>
                                            <span v-else>
                                                <label for="treatment_id" class="control-label">Treatment</label>
                                                <select v-model="aform.treatment_id" name="treatment_id" id="treatment_id"  class="form-control" :class="{ 'is-invalid': aform.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Appointment Type First</option>
                                                </select>
                                                <has-error :form="aform" field="treatment_id"></has-error>
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label">Default Time Required</label>
                                            <p>{{ timetaken }}</p>
                                        </div>
                                        <div class="col-3">
                                            <span v-if="aform.appointment_type_id == 1">
                                                <label for="doctor_id" class="control-label">Doctors</label>
                                                <select v-model="aform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('doctor_id') }" @change="getTimings(1, aform.doctor_id)">
                                                    <option disabled value="">Select Doctor</option>
                                                    <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                        {{ doctor.name | capitalize }}
                                                    </option>
                                                </select>
                                                <has-error :form="aform" field="doctor_id"></has-error>
                                            </span>
                                            <span v-else-if="aform.appointment_type_id == 2">
                                                <label for="doctor_id" class="control-label">Therapists</label>
                                                <select v-model="aform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('doctor_id') }" @change="getTimings(2, aform.doctor_id)">
                                                    <option disabled value="">Select Therapists</option>
                                                    <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                        {{ doctor.name | capitalize }}
                                                    </option>
                                                </select>
                                                <has-error :form="aform" field="doctor_id"></has-error>
                                            </span>
                                            <span v-else>
                                                <label for="doctor_id" class="control-label">Therapists/Doctors</label>
                                                <select v-model="aform.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('doctor_id') }">
                                                    <option disabled value="">Select Appointment Type first</option>
                                                </select>
                                                <has-error :form="aform" field="doctor_id"></has-error>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-3">
                                            <label for="start_time" class="control-label">Start Time</label>
                                            <select v-model="aform.start_time" name="start_time" id="start_time" class="form-control" :class="{ 'is-invalid': aform.errors.has('start_time') }" @change="getClosings(aform.start_time)">
                                                <option disabled value="">Select Start Time</option>
                                                <option v-for="(timeslot, key) in start_times" :key="key" v-bind:value="key">
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error :form="aform" field="start_time"></has-error>
                                        </div>
                                        <div class="col-3">
                                            <label for="end_time" class="control-label">End Time</label>
                                            <select v-model="aform.end_time" name="end_time" id="end_time" class="form-control" :class="{ 'is-invalid': aform.errors.has('end_time') }" @change="getRooms(aform.end_time)">
                                                <option disabled value="">Select End Time</option>
                                                <option v-for="(timeslot, key) in end_times" :key="key" v-bind:value="key">
                                                    {{ timeslot }}
                                                </option>
                                            </select>
                                            <has-error :form="aform" field="end_time"></has-error>
                                        </div>
                                        <div class="col-3" v-show="ttype == 1">
                                            <label  class="control-label">This is Dual Therapy</label>
                                            <select v-model="aform.second_doctor_id" name="second_doctor_id" id="second_doctor_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('second_doctor_id') }">
                                                <option disabled value="">Select Therapists</option>
                                                <option v-for="doctor in stherapists" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="aform" field="second_doctor_id"></has-error>

                                        </div>
                                        <div class="col-3">
                                            <span v-show="aform.appointment_type_id == 2">
                                                <label for="room_id" class="control-label">Rooms</label>
                                                <select v-model="aform.room_id" name="room_id" id="room_id" class="form-control" :class="{ 'is-invalid': aform.errors.has('room_id') }">
                                                    <option disabled value="">Select Room</option>
                                                    <option v-for="(room, key) in rooms" :key="key" v-bind:value="key">
                                                        {{ room }}
                                                    </option>
                                                </select>
                                                <has-error :form="aform" field="room_id"></has-error>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-12">
                                            <label for="user_remark" class="control-label">Remark</label>
                                            <input v-model="aform.user_remark" name="user_remark" id="user_remark" class="form-control">
                                        </div>

                                    </div>
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
                                                        <td :class="[(isExist(blockslots, slot.id)) ? 'bg-opurple' : ((isExist(fixedslots, slot.id)) ? 'bg-pink' : 'bg-teal') ]"></td>
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
                            <button type="submit" class="btn btn-wide btn-success"> Create </button>
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
                svgurl: '/svg/',
                docurl: 'files/docs/',
                editmode: false,
                loader: false,
                loaderurl: '/svg/loop.gif',
                customers: {},
                insurancers: {},
                indentities: {},
                nationalities: {},
                genders: ['Unknown', 'Male', 'Female'],
                activeYear: '',
                active_location: '',
                treatments:{},
                consultations: {},
                doctors: {},
                listSlots: [],
                therapists: {},
                timeslots:'',
                start_timeslots: {},
                end_timeslots: {},
                rooms: {},
                blockslots: [],
                fixedslots: [],
                aval_slots: [],
                ttype:'',
                tdual:0,
                timetaken:'',
                listoptions: [],
                start_times: {},
                end_times: {},
                stherapists:{},
                form: new Form({
                    first_name:'',
                    last_name:'',
                    mobile:'',
                    email: '',
                    gender:'',
                    date_of_birth:'',
                    contact_no:'',
                    married:'',
                    city:'',
                    company_name:'',
                    zipcode:'',
                    address:'',
                    nationality:'',
                    policy_number:'',
                    verification_number:'',
                    insurance_id:'1',
                    identity_type_id:'',
                    insurance_copy:'',
                    identity_copy:'',
                    pharmacy_deductable:'',
                    consult_deductable:'',
                    treatment_deductable:'',
                    co_insurance:'',
                    effective_date:'',
                    expiry_date:'',
                    nationality_id:'201',
                    concern_form:''
                }),
                aform: new Form({
                    location_id:'',
                    user_id:'',
                    room_id:'',
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
                    user_remark:''
                })
            }
        },
        methods: {
            uploadICopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.insurance_copy = reader.result;
                }
                reader.readAsDataURL(file);
            },
            uploadIdCopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.identity_copy = reader.result;
                }
                reader.readAsDataURL(file);
            },
            uploadCFCopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.concern_form = reader.result;
                }
                reader.readAsDataURL(file);
            },
            addCustomer() {
                this.editmode = false;
                this.form.reset();
                $('#addcustomerModal').modal('show');
            },
            getAssets() {
                axios.get('/api/getInsurancersList').then(({ data }) => (this.insurancers = data));
                axios.get('/api/getIdentitiesList').then(({ data }) => (this.indentities = data));
                axios.get('/api/getNationalityList').then(({ data }) => (this.nationalities = data));
            },
            createCustomer() {
                this.loader = 1;
                this.form.post('/api/customers')
                .then(response => {
                    this.aform.user_id = response.data.profile.user_id;
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        title: 'Customer profile has been created successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Create Appointment',
                        cancelButtonText: 'Register New User',
                        footer: '<a href="/customers/all"> Go To Customers List</a>'
                    }).then((result) => {
                        if (result.value) {
                            this.makeAppointment();
                            this.loader = false;
                            //this.$router.push('/customers/all');
                        }
                    }).catch(() => {
                        this.form.reset();
                    });
                    this.form.reset();
                    this.loader = false;
                }).catch(() => {
                    this.loader = false;
                });
            },
            isExist(arr, cid) {
                for (let i = 0; i < arr.length; i++) {
                    if(cid == parseInt(arr[i])){
                        return true;
                    }
                };
                return false;
            },
            onDateSelect(){
                let apid = this.aform.appointment_type_id;
                let did = this.aform.doctor_id;
                let st = this.aform.start_time;
                let et = this.aform.end_time;
                if(this.aform.appointment_code) {
                    this.getTimings(apid, did);
                    this.getClosings(st);
                    this.getRooms(et);
                }
            },
            showType(){
                let treat = this.aform.treatment_id;
                this.aform.second_doctor_id = '';
                axios.get('/api/treatments/'+treat).then((data) => {
                     this.timetaken = data.data.timing+' mins';
                    if(data.data.is_it_dual >= 1){ this.ttype = 1; this.tdual = data.data.is_it_dual;}
                    else { this.ttype = 0; this.tdual = 0; }
                }).catch();
            },
            getTimings(atype, did){
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+this.aform.date+'&at='+atype+'&lid='+this.aform.location_id+'&did='+did).then(({ data }) => { this.blockslots = data['blocked']; this.fixedslots = data['fixed']; this.start_times = data['start_slots']; this.aval_slots = data['aval_slots']; });
            },
            getClosings(st){
                axios.get('/api/appointments/get-end-timings?q='+this.aform.date+'&at='+this.aform.appointment_type_id+'&lid='+this.aform.location_id+'&did='+this.aform.doctor_id+'&st='+st).then(({ data }) => {
                    this.end_times = data;
                });
            },
            getRooms(et){
                axios.get('/api/appointments/get-rooms?q='+this.aform.date+'&lid='+this.aform.location_id+'&st='+this.aform.start_time+'&et='+et).then(({ data }) => {
                    this.rooms = data;
                });
                if(this.ttype == 1){
                   axios.get('/api/appointments/get-second-therapist?q='+this.aform.date+'&lid='+this.aform.location_id+'&did='+this.aform.doctor_id+'&st='+this.aform.start_time+'&et='+et+'&dtype='+this.tdual).then(({ data }) => {
                    this.stherapists = data;
                });
                }
            },
            getAllAssets() {
                axios.get('/api/get-active-user').then((response) => {this.aform.location_id = response.data[0].location_id;}).catch();
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/getOptionsList').then((data) => {this.listoptions = data.data }).catch();
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
                axios.get('/api/get-all-time-slots').then(({data}) => {this.timeslots = data;});
            },
            makeAppointment() {
                swal.close();
                this.getAllAssets();
                this.blockslots = [];
                this.fixedslots = [];
                this.start_times = [];
                let currenttime = new Date();
                let dd = (currenttime.getDate() < 10 ? '0' : '') + currenttime.getDate();
                let MM = ((currenttime.getMonth() + 1) < 10 ? '0' : '') + (currenttime.getMonth() + 1);
                //console.log(currenttime.getHours());
                //console.log(currenttime.getMinutes());
                this.aform.date = currenttime.getFullYear()+'-'+MM+'-'+dd;
                this.aform.location_id = this.active_location;
                //console.log(this.form.date);
                if((currenttime.getHours() > this.listoptions['appointment_end_hour']) || ((currenttime.getHours() == this.listoptions['appointment_end_hour']) && (currenttime.getMinutes() >= 1))) {
                    swal.fire('Ohh Time is Over', 'Appointment Booking time is over. Please make bookings in upcoming appointments or Contact Administrator for support.', 'error');
                }
                else if(currenttime.getHours() < this.listoptions['appointment_start_hour']){
                    swal.fire('Wait for It', 'Appointment Booking time has not started yet. Please Contact Administrator for any support.', 'error');
                }
                else {
                    $('#addApponitModal').modal('show');
                }
            },
            createAppointment() {
                this.aform.post('/api/appointments/make-appointment')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addApponitModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Appointment created successfully'
                    });
                    this.$Progress.finish();
                    this.form.reset();
                    this.aform.reset();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                });
            },
        },
        mounted() {
            this.getAssets();
        }
    }
</script>
