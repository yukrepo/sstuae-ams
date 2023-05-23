<template>
    <div>
        <div class="container-fluid  m-t-10">
            <div class="form-group">
                <div class="button-group">
                    <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/print-dacknowledgement/'+course.course_code" >Print Acknowledgement </a>
                    <span class="float-right">
                        <a class="btn btn-sm btn-outline-secondary" :href="'/appointments/direct-courses/'+activeYear">Courses List </a>
                    </span>
                </div>
            </div>
            <div class="card full-tabber">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="pills-tab-desc" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-dstab-1" data-toggle="pill" href="#pills-dstab1" role="tab" aria-controls="pills-dstab1" aria-selected="true">Course Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-dstab-2" data-toggle="pill" href="#pills-dstab2" role="tab" aria-controls="pills-dstab2" aria-selected="false">Patient Details </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-dstab-3" data-toggle="pill" href="#pills-dstab3" role="tab" aria-controls="pills-dstab3" aria-selected="true">Appointment Details</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content p-0 card-body" id="pills-tabContent-desc">
                    <div class="tab-pane fade show active" id="pills-dstab1" role="tabpanel" aria-labelledby="pills-dstab1-tab">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <h5>Course Details</h5>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Course ID</th>
                                                <th>Remark</th>
                                                <th class="wf-100">Started On</th>
                                                <th class="wf-100">Appointments</th>
                                                <th class="text-center wf-100">Payment</th>
                                                <th class="text-center wf-100">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ course.course_code }}</td>
                                                <td>{{ (course.remark)?course.remark:'---' }}</td>
                                                <td>{{ course.start_date | setdate }}</td>
                                                <td>{{ course.appointments_count | freeNumber }}</td>
                                                <td align="center">
                                                    <label class="status-label-full bg-danger" v-if="course.pstatus == 1">Pending</label>
                                                    <label class="status-label-full bg-primary" v-else-if="course.pstatus == 2">Partial</label>
                                                    <label class="status-label-full bg-success" v-else-if="course.pstatus == 3">Paid</label>
                                                    <label class="status-label-full bg-warning text-dark" v-else>Unknown</label>
                                                </td>
                                                <td align="center">
                                                    <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <h5>Invoice Details</h5>
                                    <table class="table table-striped table-bordered" v-if="course.invoice">
                                        <thead>
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Date</th>
                                                <th>Payment Mode</th>
                                                <th>Sub Total</th>
                                                <th>Net Payable</th>
                                                <th class="wf-150">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(invoice, ikey) in invoices" :key="ikey">
                                                <td>{{ invoice.invoice_number }}</td>
                                                <td>{{ invoice.created_at | setfulldate}}</td>
                                                <td>{{ (invoice.insurance_id == 1) ? 'CASH' : 'Insurance' }}</td>
                                                <td>{{ invoice.amount | formatOMR }}</td>
                                                <td>{{ invoice.payable_amount | formatOMR }}</td>

                                                <td>
                                                    <router-link class="btn btn-sm btn-dark" :to="'/appointments/print-dcourse-invoice/'+invoice.invoice_number" v-if="invoice.approved == 1">
                                                    <i class="fas fa-print"></i> Print</router-link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p v-else-if="course.ins_approval == 1">
                                        <label class="status-label-full bg-danger">Invoice Is Under Approval Process</label>
                                    </p>
                                    <p v-else-if="course.ins_approval == 2">
                                        <label class="status-label-full bg-success">Invoice Approved</label>
                                    </p>
                                    <p v-else-if="course.ins_approval == 3">
                                        <label class="status-label-full bg-purple">Invoice Rejected</label>
                                    </p>
                                    <p v-else>
                                        <label class="status-label-full bg-warning">Status - Unknown</label>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-dstab2" role="tabpanel" aria-labelledby="pills-dstab2-tab">
                        <div class="form-group">
                            <h5>Patient Details</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Married</th>
                                        <th>D.O.B. (Age)</th>
                                        <th>Address</th>
                                        <th>Insurance</th>
                                        <th>Identity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span v-if="customer.gender == 1">
                                                <img class="img-icon" :src="svgurl+'boy.svg'" alt="Male">
                                            </span>
                                            <span v-else-if="customer.gender == 2">
                                                <img class="img-icon" :src="svgurl+'girl.svg'" alt="Female">
                                            </span>
                                        {{ customer.username }}</td>
                                        <td>{{ customer.first_name+' '+customer.last_name  }}</td>
                                        <td>{{ customer.mobile }}</td>
                                        <td>{{ (customer.email)?customer.email:'--' }}</td>
                                        <td>{{ customer.married | capitalize }}</td>
                                        <td>{{ customer.date_of_birth | setdate }} {{ getAge(customer.date_of_birth) }}</td>
                                        <td>{{ customer.address }}, {{ customer.city }}</td>
                                        <td>
                                            <span v-show="customer.insurance_copy">
                                                <img class="btn-img" v-img:docurl+customer.insurance_copy :src="this.docurl+customer.insurance_copy">
                                            </span>
                                            {{ customer.insurancer | capitalize }} ({{ customer.policy_number }})
                                        </td>
                                        <td>
                                            <span v-show="customer.identity_copy">
                                                <img class="btn-img" v-img:docurl+customer.identity_copy :src="docurl+customer.identity_copy">
                                            </span>
                                            {{ customer.id_type | capitalize }} ({{ customer.verification_number }})
                                        </td>
                                        <td>
                                            <button class="btn btn-sm" :class="customer.css">{{ customer.status  }} </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-dstab3" role="tabpane3" aria-labelledby="pills-dstab3-tab">
                        <div class="form-group">
                            <h5 class="m-b-15">Appointment Details
                                <span class="float-right">
                                    <button class="btn btn-sm btn-purple" v-show="course.status_id < 5" @click="setInvoice()">Set Invoice </button>
                                    <button class="btn btn-sm btn-dark" type="button" @click="makeAppointment"> <i class="fas fa-plus"></i> New Appointment</button>
                                </span>
                            </h5>
                            <table id="upcomings" class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="wf-50">SNo</th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Timing</th>
                                        <th>Treatment</th>
                                        <th>Doctor</th>
                                        <th>Invoice</th>
                                        <th class="wf-100">Added On</th>
                                        <th class="wf-120">Status</th>
                                        <th class="wf-120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(appointment, sn) in appointments" :key="appointment.id">
                                        <td>{{ ++sn }}</td>
                                        <td>{{ appointment.appointment_code }}</td>
                                        <td>{{ appointment.date | setdate }}</td>
                                        <td>{{ appointment.time }}</td>
                                        <td>{{ appointment.reason }}</td>
                                        <td>{{ appointment.doctor }}</td>
                                        <td>
                                            <label class="bg-success status-label-full" v-if="appointment.invoice">
                                                {{ appointment.invoice }}
                                            </label>
                                            <label class="bg-danger status-label-full" v-else>
                                                Not invoiced
                                            </label>
                                        </td>
                                        <td>{{ appointment.created | setdate }}</td>
                                        <td>
                                            <label class="status-label-full" :class="appointment.css">{{  appointment.status }}</label>
                                        </td>
                                        <td>
                                            <span class="btn-group editbox" role="group">
                                                <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <span class="dropdown-menu dropdown-menu-right m-0 p-0" aria-labelledby="btnGroupDrop1">
                                                    <button class="dropdown-item text-left bb-1 p-2" @click="patientBox(appointment)">Change Patient</button>
                                                    <button class="dropdown-item text-left bb-1 p-2" @click="doctorBox(appointment)">Modify Appointment</button>
                                                </span>
                                            </span>
                                            <button class="btn btn-danger wf-25 btn-sm" @click="cancelAppointment(appointment.appointment_code)"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateInvoice() : createInvoice()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Create Course Invoice</h5>
                            <h5 class="modal-title" v-show="editmode">Update Course Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row m-b-10">
                                <div class="col-12">
                                    <table class="table table-striped table-bordered m-b-0">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th class="wf-50">#</th>
                                                <th class="wf-50">SNo</th>
                                                <th>Description</th>
                                                <th class="wf-100">Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(acount, akey) in acountlist" :key="akey">
                                                <td>
                                                    <button type="button" @click="removeBar(akey)" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </td>
                                                <td>{{ akey+1 }}</td>
                                                <td>
                                                    <b>{{ aform.apt__reason[akey] }}</b>
                                                    [Appointment ID: <b>{{ aform.apt__apcode[akey] }}</b>]
                                                    [Date Time: {{aform.apt__datetime[akey]}}]
                                                </td>
                                                <td>
                                                    <b>{{ aform.apt__cost[akey] | formatOMR }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Sub Total</th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input type="text" v-model="aform.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('sub_total') }">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered  m-b-0">
                                        <thead>
                                            <tr v-show="aform.offered == 1">
                                                <th class="text-right text-uppercase">Discount</th>
                                                <td style="width: 100px;"><input type="text" v-model="aform.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('offered_value') }"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-right text-uppercase">Net Payable</th>
                                                <td style="width: 100px;"><input type="text" v-model="aform.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('total') }"></td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                   
                                </div>
                                <div class="col-4">
                                    <label class="control-label">Discount by SST</label>
                                    <hr class="m-b-5 m-t-5">
                                    <span v-if="aform.insured == 1">
                                        <em class="text-danger">Discount can not be applied with insurance.</em>
                                    </span>
                                    <span v-else>
                                        <p>
                                            <span class="input-checkbox">
                                                <label>
                                                    <button @click="addOffers" v-show="aform.offered != 1" type="button" class="btn btn-sm btn-success m-r-15 wf-30"> <i class="fas fa-plus"></i> </button>
                                                     {{ discount.title }}
                                                    <span class="m-l-5" v-if="checkPercent(discount.value)">
                                                        <b>({{ discount.value }})</b>
                                                    </span>
                                                    <span class="m-l-5" v-else>
                                                        <b>({{ discount.value | formatOMR }})</b>
                                                    </span>
                                                    <button @click="removeADiscount" type="button" class="btn btn-sm btn-danger float-right wf-30" v-show="aform.offered == 1"> <i class="fas fa-minus"></i> </button>
                                                </label>
                                            </span>

                                        </p>
                                    </span>

                                </div>
                                <div class="col-4">
                                    <div>
                                        <label for="payment_type" class="control-label">Payment Type</label>
                                        <hr class="m-b-5 m-t-5">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-control" :class="{ 'is-invalid': aform.errors.has('payment_type') }" v-model="aform.payment_type" name="payment_type" @change="setPaying">
                                                    <option value="2" v-show="aform.listlen >= 5">Partial Payment</option>
                                                    <option value="3">Full Payment</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" v-model="aform.paying_now" placeholder="amount paying now" v-show="aform.payment_type == 2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-t-15">
                                <div class="form-group col-md-3">
                                    <label for="payment_mode" class="control-label">Payment Mode</label>
                                    <select class="form-control" :class="{ 'is-invalid': aform.errors.has('payment_mode') }" v-model="aform.payment_mode" name="payment_mode">
                                        <option value="cash">Cash</option>
                                        <option value="credit">Credit</option>
                                        <option value="epay">E-Payment</option>
                                        <option value="visa">VISA Card</option>
                                        <option value="cash+visa">Cash + VISA</option>
                                    </select>
                                    <div class="p-t-15">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea class="form-control" v-model="aform.remark" name="remark"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div v-show="(aform.payment_mode == 'visa') || (aform.payment_mode == 'cash+visa')" class="m-b-15">
                                        <label for="txn_id" class="control-label">Transaction Number</label>
                                        <input type="text" class="form-control" :class="{ 'is-invalid': aform.errors.has('txn_id') }" v-model="aform.txn_id" name="txn_id">
                                    </div>
                                    <div v-show="(aform.payment_mode == 'cash+visa')" class="m-b-15 row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cash_amount" class="control-label">Paid in cash </label>
                                                <input type="text" v-model="aform.cash_amount" name="cash_amount" class="form-control" @keyup="getCCashVisa">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="visa_amount" class="control-label">Paid By Visa Card </label>
                                                <input type="text" v-model="aform.visa_amount" name="visa_amount" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-2"></div>

                                <div class="form-group col-md-4">
                                    <table class="table table-bordered" v-show="(aform.payment_mode == 'cash') || (aform.payment_mode == 'cash+visa')">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="text-center text-uppercase">Calculate Returnable Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label for="received">Received</label>
                                                    <input type="text" class="form-control" v-model="aform.received" placeholder="enter received value">
                                                </td>
                                                <td> <label for="received">--</label><br>
                                                <button class="btn btn-sm btn-dark" type="button" @click="calculate" >Calculate</button> </td>
                                                <td>
                                                    <label for="received">Returnable</label>
                                                    <input type="text" class="form-control" v-model="aform.returnable" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><i class="text-danger">{{ calcmsg}}</i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 1">
                            <button v-show="!editmode" type="submit" class="btn btn-wide btn-success" v-else> Submit </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 2">
                            <button v-show="editmode" type="submit" class="btn btn-wide btn-success" v-else> Update </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="addApponitModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addApponitModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createAppointment()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAppointModalTitle">Create Appointment</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row border-bottom m-b-10 p-b-15 m-l-0 m-r-0">
                                        <div class="col-12">
                                            <div class="alert alert-danger font-weight-bold text-uppercase" v-show="catchmessage">
                                                {{ catchmessage }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="date" class="control-label">Select Date</label>
                                                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date"  :auto-submit="true"></vp-date-picker>
                                                </div>
                                                <has-error :form="form" field="date"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="treatment_id" class="control-label">Treatments</label>
                                                <select v-model="form.treatment_id" name="treatment_id" id="treatment_id"  @change="showType" class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Treatments</option>
                                                    <option v-for="(treatment, key) in treatments" :key="key" v-bind:value="treatment.value">
                                                        {{ treatment.text}}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="treatment_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label">Default Time Required</label>
                                            <p>{{ timetaken }}</p>
                                        </div>
                                        <div class="col-3" v-if="form.appointment_type_id == 1">
                                            <label for="doctor_id" class="control-label">Doctors</label>
                                            <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(2, form.doctor_id)">
                                                <option disabled value="">Select Doctors</option>
                                                <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="doctor_id"></has-error>
                                        </div>
                                        <div class="col-3" v-else>
                                            <label for="doctor_id" class="control-label">Therapists</label>
                                            <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(2, form.doctor_id)">
                                                <option disabled value="">Select Therapists</option>
                                                <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="doctor_id"></has-error>
                                        </div>

                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-3">
                                            <label for="start_time" class="control-label">Start Time</label>
                                            <select v-model="form.start_time" name="start_time" id="start_time" class="form-control" :class="{ 'is-invalid': form.errors.has('start_time') }" @change="getClosings(form.start_time)">
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
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 3">
                            <button type="submit" class="btn btn-wide btn-success" v-else>Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="patientModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="patientModalTitle" aria-hidden="true">
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
                                        <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name }} {{ customer.last_name }}</p></li>
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
                                                            v-model="eform.user_id"
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
                                        <div class="col-2">
                                            <label for="date" class="control-label">Change Date</label>
                                            <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date" :auto-submit="true" @change="onDateSelect"></vp-date-picker>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">
                                        <div class="col-3">
                                            <label for="treatment_id" class="control-label">Treatments</label>
                                            <select v-model="form.treatment_id" name="treatment_id" id="treatment_id"  @change="showType" class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                <option disabled value="">Select Treatments</option>
                                                <option v-for="(treatment, key) in treatments" :key="key" v-bind:value="treatment.value">
                                                    {{ treatment.text}}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="treatment_id"></has-error>
                                        </div>
                                        <div class="col-3" v-if="form.appointment_type_id == 1">
                                            <label for="doctor_id" class="control-label">Doctors</label>
                                            <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(2, form.doctor_id)">
                                                <option disabled value="">Select Doctors</option>
                                                <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="doctor_id"></has-error>
                                        </div>
                                        <div class="col-3" v-else>
                                            <label for="doctor_id" class="control-label">Therapists</label>
                                            <select v-model="form.doctor_id" name="doctor_id" id="doctor_id" class="form-control" :class="{ 'is-invalid': form.errors.has('doctor_id') }" @change="getTimings(2, form.doctor_id)">
                                                <option disabled value="">Select Therapists</option>
                                                <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                    {{ doctor.name | capitalize }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="doctor_id"></has-error>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label">Default Time Required</label>
                                            <p>{{ timetaken }}</p>
                                        </div>
                                    </div>
                                    <div class="row border-bottom m-b-10 p-b-5 m-l-0 m-r-0">

                                        <div class="col-3">
                                            <label for="start_time" class="control-label">Start Time</label>
                                            <select v-model="form.start_time" name="start_time" id="start_time" class="form-control" :class="{ 'is-invalid': form.errors.has('start_time') }" @change="getClosings(form.start_time)">
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
import moment from "moment";
export default {
    data() {
        return {
            editmode: false,
            svgurl: "/svg/",
            docurl: "/files/docs/",
            course: "",
            therapy_package: "",
            doctors: "",
            catchmessage: "",
            consultation: "",
            currentYear: new Date().getFullYear(),
            activeID: "",
            activeYear: "2020",
            active_location: "",
            appointment: "",
            appointments: {},
            listSlots: [],
            clistSlots: [],
            customer: {},
            treatment: "",
            insurance: "",
            options: "",
            countlist: [],
            medicines: [],
            stocks: [],
            invoice: {},
            invoices: {},
            acountlist: [],
            calcmsg: "",
            discounts: [],
            discount: { title: "", value: 0 },
            aform: new Form({
                sub_total: "",
                type: 6,
                total: "",
                payment_mode: "cash",
                txn_id: "",
                remark: "",
                returnable: "",
                received: "",
                appointments: "",
                course_code: "",
                id: "",
                user_id: "",
                invoice_number: "",
                apt__reason: [],
                apt__apcode: [],
                apt__datetime: [],
                apt__cost: [],
                insurance_id: "",
                insured: 0,
                sub_total: "",
                insured_deduction: 0,
                insured_deduction_reason: "",
                offered: 0,
                offered_reason: "",
                offered_value: 0,
                cash_amount: "",
                visa_amount: "",
                payment_type: 3,
                listlen: "",
                paying_now: "",
            }),
            start_times: {},
            end_times: {},
            stherapists: [],
            treatments: {},
            timetaken: "",
            ttype: "",
            tdual: 0,
            rooms: "",
            aval_slots: {},
            timeslots: "",
            naval_slots: [],
            nblockslots: [],
            nfixedslots: [],
            therapists: "",
            fixedslots: "",
            loader: false,
            loaderurl: "/svg/loop.gif",
            form: new Form({
                id: "",
                appointment_code: "",
                location_id: "",
                user_id: "",
                room_id: "",
                course_id: "",
                doctor_id: "",
                appointment_type_id: "",
                treatment_id: "",
                second_doctor_id: "",
                date: "",
                start_time: "",
                end_time: "",
                visit_type_id: 1,
                status_id: 4,
                followup_appointment: "",
                followup_verified: "",
            }),
            customers: [],
            eform: new Form({
                appointment_code: "",
                user_id: "",
                room_id: "",
                doctor_id: "",
                treatment_id: "",
                second_doctor_id: "",
                date: "",
                start_time: "",
                end_time: "",
            }),
        };
    },
    methods: {
        getIndex(list, id) {
            return id;
        },
        getAge(date) {
            let today = new Date();
            let ndate = new Date(date);
            var a = moment([
                today.getFullYear(),
                today.getMonth(),
                today.getDate(),
            ]);
            var b = moment([
                ndate.getFullYear(),
                ndate.getMonth(),
                ndate.getDate(),
            ]);
            var diffDuration = moment.duration(a.diff(b));
            return "(" + diffDuration.years() + " yrs)";
        },
        checkPercent(val) {
            if (val == "" || val == null) {
                return false;
            } else {
                return val.includes("%");
            }
        },
        viewCustomer(id) {
            $("#userModal").modal("show");
        },
        setpayType() {
            let ptype = this.aform.payment_type;
            if (ptype == 3) {
                this.aform.being_paid = this.aform.total;
            } else {
                this.aform.being_paid = "";
            }
            this.aform.received = "";
            this.aform.returnable = "";
        },
        setPaying() {
            if (this.aform.payment_type == 2) {
                this.aform.paying_now = "";
                if (this.aform.payment_mode == "cash+visa") {
                    this.aform.cash_amount = "";
                    this.aform.visa_amount = "";
                }
            } else {
                this.aform.paying_now = this.makeNumber(this.aform.total);
            }
        },
        calculate() {
            let tc, nt, rc;
            if (this.aform.visa_amount >= 1) {
                tc =
                    this.makeNumber(this.aform.paying_now) -
                    this.makeNumber(this.aform.visa_amount);
            } else {
                tc = this.makeNumber(this.aform.paying_now);
            }
            if (this.aform.received < tc) {
                this.calcmsg =
                    "Received amount must be greater than or equal to payable by customer in cash.";
                this.aform.received = "";
                this.aform.returnable = "";
                return;
            } else {
                this.calcmsg = "";
            }
            let returnable = this.makeNumber(this.aform.received) - tc;
            this.aform.returnable = returnable.toFixed(3);
        },
        getAllAssets() {
            axios.get("/api/get-time-slots").then(({ data }) => {
                this.timeslots = data;
            });
            axios
                .get("/api/getBothSlotsList")
                .then((data) => {
                    this.listSlots = data.data.starts;
                    this.clistSlots = data.data.ends;
                })
                .catch();
            axios
                .get("/api/getOnlyTherapistsList")
                .then((data) => {
                    this.therapists = data.data;
                })
                .catch();
            axios
                .get("/api/getOnlyDoctorsList")
                .then((data) => {
                    this.doctors = data.data;
                })
                .catch();
            axios
                .get("/api/get-active-user")
                .then((response) => {
                    this.active_location = response.data[0].location_id;
                })
                .catch();
            axios.get("/api/get-course-discounts").then((response) => {
                this.discounts = response.data;
            });
            axios.get("/api/getPresTreatmentsSelectList").then((response) => {
                this.treatments = response.data;
            });
        },
        showCourse() {
            this.$Progress.start();
            axios
                .get("/api/courses/view-direct-course/" + this.activeID)
                .then(({ data }) => {
                    this.course = data;
                    if (data.invoices.length >= 1) {
                        axios
                            .post("/api/get-invoices-bycourses", {
                                invoices: data.invoices,
                            })
                            .then((data5) => {
                                this.invoices = data5.data;
                            })
                            .catch();
                    }
                    axios
                        .get("/api/customers/" + this.course.user_id)
                        .then((data3) => {
                            this.customer = data3.data[0];
                        })
                        .catch();
                    axios
                        .post("/api/appointments/get-bulk-appointments", {
                            appointments: this.course.appointments,
                        })
                        .then((data2) => {
                            this.appointments = data2.data;
                            this.setInvoiceData();
                        })
                        .catch();
                    this.$Progress.finish();
                });
        },
        isExist(arr, cid) {
            for (let i = 0; i < arr.length; i++) {
                if (cid == parseInt(arr[i])) {
                    return true;
                }
            }
            return false;
        },
        setInvoice() {
            this.checkDiscount();
            $("#setInvocieModal").modal("show");
        },
        checkDiscount() {
            let listlen = this.aform.apt__reason.length;
            this.aform.listlen = listlen;
            if (listlen >= 5) {
                this.discount = this.discounts[1];
            } else if (listlen == 3 || listlen == 4) {
                this.discount = this.discounts[0];
            } else {
                this.aform.offered = 0;
                this.discount = { title: "No discount available", value: 0 };
            }
        },
        setInvoiceData() {
            let $ye = this;
            let $aform = this.aform;
            let subtotal = 0;
            this.aform.user_id = this.course.user_id;
            this.aform.course_code = this.course.course_code;
            this.aform.insurance_id = this.course.insurance_id;
            if (this.course.insurance_id == 1) {
                axios
                    .post("/api/appointments/get-bulk-cash-invoices", {
                        appointments: this.course.appointments,
                    })
                    .then((data) => {
                        $ye.acountlist = data.data.countlist;
                        $aform.listlen = data.data.countlist.length;
                        data.data.appointments.forEach((element, key) => {
                            $aform.apt__reason.push(element.reason);
                            $aform.apt__apcode.push(element.appointment_code);
                            $aform.apt__datetime.push(
                                element.date + " " + element.time
                            );
                            $aform.apt__cost.push(element.cost);
                            subtotal =
                                this.makeNumber(subtotal) +
                                this.makeNumber(element.cost);
                        });
                        $aform.sub_total = subtotal;
                        $aform.total = subtotal;
                        $aform.paying_now = subtotal;
                    });
            } else {
                this.discount = { title: "No discount available", value: 0 };
                axios
                    .post("/api/appointments/get-bulk-insurance-invoices", {
                        appointments: this.course.appointments,
                        insurance_id: this.course.insurance_id,
                    })
                    .then((data) => {});
            }
        },
        makeNumber(val) {
            if (isNaN(val)) {
                return 0;
            } else {
                return parseFloat(val);
            }
        },
        getCCashVisa() {
            let visa_amount =
                this.makeNumber(this.aform.paying_now) -
                this.makeNumber(this.aform.cash_amount);
            this.aform.visa_amount = visa_amount.toFixed(3);
        },
        createInvoice() {
            if (this.aform.payment_mode == "visa") {
                if (
                    this.aform.txn_id === null ||
                    this.aform.txn_id === "" ||
                    this.aform.txn_id === 0
                ) {
                    swal.fire(
                        "Missing Values",
                        "Transaction number is required for VISA payment mode.",
                        "error"
                    );
                    return false;
                }
            } else if (this.aform.payment_mode == "epay") {
                if (
                    this.aform.txn_id === null ||
                    this.aform.txn_id === "" ||
                    this.aform.txn_id === 0 ||
                    this.aform.remark === ""
                ) {
                    swal.fire(
                        "Missing Values",
                        "Transaction number and Remark are required for E-Payment mode.",
                        "error"
                    );
                    return false;
                }
            } else if (this.aform.payment_mode == "cash+visa") {
                if (this.aform.txn_id === null || this.aform.visa_amount <= 0) {
                    swal.fire(
                        "Missing Values",
                        "Transaction number and amount paid by VISA is required for this payment mode.",
                        "error"
                    );
                    return false;
                }
            }
            this.loader = 1;
            this.aform
                .post("/api/invoices/create-schedule-course-invoice")
                .then((data) => {
                    $("#setInvocieModal").modal("hide");
                    Fire.$emit("AfterCreate");
                    swal.fire({
                        title: "Invoice has been created successfully",
                        type: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Print Invoice",
                        cancelButtonText: "Stay On The Page",
                    })
                        .then((result) => {
                            if (result.value) {
                                this.loader = false;
                                this.$router.push(
                                    "/appointments/print-schcourse-invoice/" +
                                        data.data.invoice
                                );
                            }
                        })
                        .catch(() => {
                            this.loader = false;
                            swal.showValidationMessage(
                                `Request failed: ${error}`
                            );
                        });
                })
                .catch(() => {
                    this.loader = false;
                    swal.showValidationMessage(`Request failed: ${error}`);
                });
        },
        makeNumber(val) {
            if (isNaN(val)) {
                return 0;
            } else {
                return parseFloat(val);
            }
        },
        InsuranceExpired() {
            if (this.customer.insurance_id == 1) {
                this.expirynote = "Cash Customer";
                return true;
            } else {
                let today = new Date();
                let ndate = new Date(this.customer.expiry_date);
                var a = moment([
                    today.getFullYear(),
                    today.getMonth(),
                    today.getDate(),
                ]);
                var b = moment([
                    ndate.getFullYear(),
                    ndate.getMonth(),
                    ndate.getDate(),
                ]);
                var diffDuration = moment.duration(b.diff(a));
                var days = diffDuration.as("days");
                if (Math.sign(days) == "-1") {
                    this.expirynote =
                        "Insurance Policy has been expired. Please update the details if insurance has been renewed.";
                    return true;
                } else if (days == 0) {
                    this.expirynote =
                        "Insurance Policy is expiring today. Inform Customer to renew it on time.";
                    return false;
                } else if (15 >= days > 0) {
                    this.expirynote =
                        days + " day(s) left to expire the Insurance.";
                    return false;
                } else {
                    this.expirynote = "";
                    return false;
                }
            }
        },
        removeADiscount() {
            this.aform.discount_id = "";
            this.aform.offered_value = 0;
            this.aform.offered = 0;
            this.aform.offered_reason = "";
            this.aform.paying_now = this.aform.total;
            this.aform.payment_type = 3;
            this.aform.cash_amount = "";
            this.aform.visa_amount = "";
            this.reTotal();
        },
        removeBar(barkey) {
            this.acountlist.splice(barkey, 1);
            this.aform.sub_total =
                this.aform.sub_total - this.aform.apt__cost[barkey];
            this.aform.apt__reason.splice(barkey, 1);
            this.aform.apt__apcode.splice(barkey, 1);
            this.aform.apt__datetime.splice(barkey, 1);
            this.aform.apt__cost.splice(barkey, 1);
            this.checkDiscount();
            this.removeADiscount();
        },
        addInsurance() {
            this.aform.offered = 0;
            this.aform.offered_value = "";
            if (this.aform.insured == 1) {
                if (this.appointment.appointment_type_id == 1) {
                    this.aform.insured_discount_reason =
                        this.insurance.discount + " discount";
                    this.aform.insured_deduction_reason =
                        this.customer.consult_deductable + " deductable";
                    if (this.insurance.discount.indexOf("%") >= 1) {
                        this.aform.insured_discount = this.makeNumber(
                            (this.makeNumber(this.aform.sub_total) *
                                this.makeNumber(
                                    this.insurance.discount.replace("%", "")
                                )) /
                                100
                        ).toFixed(3);
                    } else {
                        this.aform.insured_discount = this.makeNumber(
                            this.insurance.discount
                        ).toFixed(3);
                    }

                    if (this.customer.consult_deductable === null) {
                        if (this.customer.co_insurance === null) {
                            //this.aform.total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                            this.aform.total = 0.0;
                        } else if (
                            this.customer.co_insurance.indexOf("%") >= 1
                        ) {
                            this.aform.total = this.makeNumber(
                                ((this.makeNumber(this.aform.sub_total) -
                                    this.makeNumber(
                                        this.aform.insured_discount
                                    )) *
                                    this.makeNumber(
                                        this.customer.co_insurance.replace(
                                            "%",
                                            ""
                                        )
                                    )) /
                                    100
                            ).toFixed(3);
                        } else if (this.customer.co_insurance >= 1) {
                            this.aform.total = this.makeNumber(
                                this.customer.co_insurance
                            ).toFixed(3);
                        } else {
                            this.aform.total =
                                this.makeNumber(this.aform.sub_total) -
                                this.makeNumber(this.aform.insured_discount);
                            this.aform.total = this.aform.total.toFixed(3);
                        }
                    } else if (
                        this.customer.consult_deductable.indexOf("%") >= 1
                    ) {
                        this.aform.total = this.makeNumber(
                            ((this.makeNumber(this.aform.sub_total) -
                                this.makeNumber(this.aform.insured_discount)) *
                                this.makeNumber(
                                    this.customer.consult_deductable.replace(
                                        "%",
                                        ""
                                    )
                                )) /
                                100
                        ).toFixed(3);
                    } else {
                        this.aform.total = this.makeNumber(
                            this.customer.consult_deductable
                        ).toFixed(3);
                    }
                } else if (this.appointment.appointment_type_id == 2) {
                    this.aform.insured_discount_reason =
                        this.insurance.discount + " discount";
                    this.aform.insured_deduction_reason =
                        this.customer.treatment_deductable + " deductable";

                    if (this.insurance.discount.indexOf("%") >= 1) {
                        this.aform.insured_discount = this.makeNumber(
                            (this.makeNumber(this.aform.sub_total) *
                                this.makeNumber(
                                    this.insurance.discount.replace("%", "")
                                )) /
                                100
                        ).toFixed(3);
                    } else {
                        this.aform.insured_discount = this.makeNumber(
                            this.insurance.discount
                        ).toFixed(3);
                        this.aform.total = this.aform.total.toFixed(3);
                    }
                    if (this.customer.co_insurance === null) {
                        this.aform.total = 0.0; //this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                    } else if (this.customer.co_insurance.indexOf("%") >= 1) {
                        this.aform.total = this.makeNumber(
                            ((this.makeNumber(this.aform.sub_total) -
                                this.makeNumber(this.aform.insured_discount)) *
                                this.makeNumber(
                                    this.customer.co_insurance.replace("%", "")
                                )) /
                                100
                        ).toFixed(3);
                    } else if (this.customer.co_insurance >= 1) {
                        this.aform.total = this.makeNumber(
                            this.customer.co_insurance
                        ).toFixed(3);
                    } else {
                        this.aform.total =
                            this.makeNumber(this.aform.sub_total) -
                            this.makeNumber(this.aform.insured_discount);
                        this.aform.total = this.aform.total.toFixed(3);
                    }
                } else {
                    this.aform.insured_discount = 0;
                    this.aform.insured_deduction = 0;
                    this.aform.insured_discount_value = "";
                    this.aform.insured_deduction_value = "";
                }
            } else {
                this.aform.insured_discount = 0;
                this.aform.insured_deduction = 0;
                this.aform.insured_discount_value = "";
                this.aform.insured_deduction_value = "";
            }
        },
        addOffers() {
            let reason = "";
            let offer = 0;
            this.aform.offered = 1;
            let adiscount = this.discount.value;
            if (adiscount.indexOf("%") >= 1) {
                reason = this.discount.title;
                offer = this.makeNumber(
                    (this.makeNumber(this.aform.sub_total) *
                        this.makeNumber(adiscount.replace("%", ""))) /
                        100
                );
            } else {
                reason = this.discount.title;
                offer = this.makeNumber(discount.value);
            }
            this.aform.offered_reason = reason;
            this.aform.offered_value = offer.toFixed(3);
            this.reTotal();
        },
        reTotal() {
            this.aform.returnable = "";
            this.aform.received = "";
            if (this.aform.insured == 1) {
                //  this.addInsurance();
                let insured_deduction =
                    this.makeNumber(this.aform.sub_total) -
                    this.makeNumber(this.aform.total);
                this.aform.insured_deduction =
                    this.makeNumber(insured_deduction) >= 0
                        ? this.makeNumber(insured_deduction).toFixed(3)
                        : 0;
            } else if (this.aform.offered == 1) {
                this.aform.insured = "";
                //    this.addOffers();
                let total =
                    this.makeNumber(this.aform.sub_total) -
                    this.makeNumber(this.aform.offered_value);
                this.aform.total =
                    this.makeNumber(total) >= 0
                        ? this.makeNumber(total).toFixed(3)
                        : 0;
            } else {
                this.aform.insured_deduction = 0;
                this.aform.total = this.makeNumber(
                    this.aform.sub_total
                ).toFixed(3);
            }
            if (this.aform.payment_type == 2) {
                if (this.aform.paying_now > this.aform.total) {
                    this.aform.paying_now = this.aform.total;
                }
            } else {
                this.aform.paying_now = this.aform.total;
            }
        },
        isNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },
        showType() {
            let treat = this.form.treatment_id;
            this.form.second_doctor_id = "";
            axios
                .get("/api/treatments/" + treat)
                .then((data) => {
                    this.timetaken = data.data.timing + " mins";
                    this.form.appointment_type_id =
                        data.data.appointment_type_id;
                    if (data.data.is_it_dual >= 1) {
                        this.ttype = 1;
                        this.tdual = data.data.is_it_dual;
                    } else {
                        this.ttype = 0;
                        this.tdual = 0;
                    }
                })
                .catch();
        },
        getTimings(atype, did) {
            axios
                .get(
                    "/api/appointments/get-doctor-appointment-timings?q=" +
                        this.form.date +
                        "&at=" +
                        atype +
                        "&lid=" +
                        this.form.location_id +
                        "&did=" +
                        did
                )
                .then(({ data }) => {
                    this.blockslots = data["blocked"];
                    this.fixedslots = data["fixed"];
                    this.start_times = data["start_slots"];
                    this.aval_slots = data["aval_slots"];
                });
        },
        getClosings(st) {
            axios
                .get(
                    "/api/appointments/get-end-timings?q=" +
                        this.form.date +
                        "&at=" +
                        this.form.appointment_type_id +
                        "&lid=" +
                        this.form.location_id +
                        "&did=" +
                        this.form.doctor_id +
                        "&st=" +
                        st
                )
                .then(({ data }) => {
                    this.end_times = data;
                });
        },
        getRooms(et) {
            axios
                .get(
                    "/api/appointments/get-rooms?q=" +
                        this.form.date +
                        "&lid=" +
                        this.form.location_id +
                        "&st=" +
                        this.form.start_time +
                        "&et=" +
                        et +
                        "&apid=" +
                        this.form.appointment_code
                )
                .then(({ data }) => {
                    this.rooms = data;
                });
            if (this.ttype == 1) {
                axios
                    .get(
                        "/api/appointments/get-second-therapist?q=" +
                            this.form.date +
                            "&lid=" +
                            this.form.location_id +
                            "&did=" +
                            this.form.doctor_id +
                            "&st=" +
                            this.form.start_time +
                            "&et=" +
                            et +
                            "&dtype=" +
                            this.tdual +
                            "&apid=" +
                            this.form.appointment_code
                    )
                    .then(({ data }) => {
                        this.stherapists = data;
                    });
            }
        },
        makeAppointment() {
            this.catchmessage = "";
            this.form.reset();
            this.blockslots = [];
            this.fixedslots = [];
            this.start_times = [];
            this.form.location_id = this.active_location;
            this.form.course_id = this.course.course_code;
            this.form.user_id = this.course.user_id;
            //console.log(this.form.date);
            $("#addApponitModal").modal("show");
        },
        createAppointment() {
            this.loader = 3;
            this.form
                .post("/api/courses/make-course-appointment")
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    this.loader = false;
                    $("#addApponitModal").modal("hide");
                    toast.fire({
                        type: "success",
                        title: "Appointment created successfully.",
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                    this.loader = false;
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
        cancelAppointment(id) {
            swal.fire({
                title: "Are you sure?",
                text: "Please enter the reason before cancelling the appointment.",
                footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                type: "question",
                input: "text",
                inputAttributes: { autocapitalize: "off" },
                showCancelButton: true,
                confirmButtonColor: "#C70039",
                cancelButtonColor: "#0DC957",
                confirmButtonText: "Yes, Cancel it!",
                cancelButtonText: "Not Now",
                preConfirm: (reason) => {
                    return axios
                        .post("/api/cancel-appointment", {
                            appointment_code: id,
                            reason: reason,
                        })
                        .then((response) => {
                            Fire.$emit("AfterCreate");
                            swal.fire(
                                "Cancelled!",
                                "Appointment has been cancelled successfully.",
                                "success"
                            );
                        })
                        .catch((error) => {
                            swal.showValidationMessage(`${error}`);
                        });
                },
            });
        },
        onDateSelect() {
            let apid = this.form.appointment_type_id;
            let did = this.form.doctor_id;
            let st = this.form.start_time;
            let et = this.form.end_time;
            if (this.form.appointment_code) {
                this.getTimings(apid, did);
                this.getClosings(st);
                this.getRooms(et);
            }
        },
        patientBox(apid) {
            this.eform.appointment_code = apid.appointment_code;
            axios
                .get("/api/getCustomerSelectList")
                .then((data) => {
                    this.customers = data.data;
                })
                .catch();
            $("#patientModal").modal("show");
        },
        doctorBox(apid) {
            let pi = this.form;
            this.form.appointment_code = apid.appointment_code;
            axios
                .get("/api/appointments/form-view/" + apid.appointment_code)
                .then((data) => {
                    axios
                        .get("/api/treatments/" + data.data.treatment_id)
                        .then((data2) => {
                            this.timetaken = data2.data.timing + " mins";
                            if (data2.data.is_it_dual == 1) {
                                this.ttype = 1;
                            } else {
                                this.ttype = 0;
                            }
                        })
                        .catch();
                    axios
                        .get(
                            "/api/appointments/get-doctor-appointment-timings?q=" +
                                data.data.date +
                                "&at=" +
                                data.data.appointment_type_id +
                                "&lid=" +
                                data.data.location_id +
                                "&did=" +
                                data.data.doctor_id +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data3) => {
                            this.nblockslots = data3.data["blocked"];
                            this.nfixedslots = data3.data["fixed"];
                            this.start_times = data3.data["start_slots"];
                            this.naval_slots = data3.data["aval_slots"];
                        });
                    axios
                        .get(
                            "/api/appointments/get-end-timings?q=" +
                                data.data.date +
                                "&at=" +
                                data.data.appointment_type_id +
                                "&lid=" +
                                data.data.location_id +
                                "&did=" +
                                data.data.doctor_id +
                                "&st=" +
                                data.data.start_timeslot +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data4) => {
                            this.end_times = data4.data;
                        });
                    axios
                        .get(
                            "/api/appointments/get-rooms?q=" +
                                data.data.date +
                                "&lid=" +
                                data.data.location_id +
                                "&st=" +
                                data.data.start_timeslot +
                                "&et=" +
                                data.data.end_timeslot +
                                "&apid=" +
                                data.data.appointment_code
                        )
                        .then((data5) => {
                            this.rooms = data5.data;
                        });
                    pi.fill(data.data);
                    this.form.start_time = data.data.start_timeslot;
                    this.form.end_time = data.data.end_timeslot;
                })
                .catch();
            $("#doctorModal").modal("show");
        },
        updatePatient() {
            this.eform
                .post("/api/appointments/change-patient")
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    $("#patientModal").modal("hide");
                    toast.fire({
                        type: "success",
                        title: "Patient has been updated successfully.",
                    });
                    this.$Progress.finish();
                })
                .catch((response) => {
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
        updateDoctor() {
            this.form
                .post("/api/appointments/update-appointment")
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit("AfterCreate");
                    $("#doctorModal").modal("hide");
                    toast.fire({
                        type: "success",
                        title: "Appointment has been updated successfully.",
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch((response) => {
                    // this.catchmessage = response.message;
                    //this.flash(response.message, 'error');
                });
        },
    },
    beforeMount() {
        let activeId = this.$route.path.split("/");
        this.activeID = activeId[4];
    },
    mounted() {
        this.getAllAssets();
        this.showCourse();
        Fire.$on("AfterCreate", () => {
            this.showCourse();
        });
    },
};
</script>
