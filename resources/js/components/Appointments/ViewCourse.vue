<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="my-2">
                    <div class="button-group">
                        <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/print-acknowledgement/'+course.course_code" >Print Acknowledgement </a>
                        <a class="btn btn-sm btn-success" target="_blank" :href="'/appointments/complete-invoice/'+course.primary_invoice" v-show="(course.primary_invoice != '') && (course.insurance_id != 1) && (course.status_id < 5)">Complete Invoice Preview </a>
                        <a class="btn btn-sm btn-success" :href="'/appointments/complete-cash-invoice/'+course.course_code" v-show="(course.invoice_count == 1) && (course.insurance_id == 1) && ((course.status_id != 5) && (course.status_id != 9))">Complete Cash Invoice </a>
                        <!-- <a class="btn btn-sm btn-success" :href="'/appointments/set-cash-invoice/'+course.course_code" v-show="((course.primary_invoice == '') && (course.insurance_id == 1) && (course.status_id != 5))">Set Cash Invoice </a> -->
                        <button class="btn btn-sm btn-danger" type="button" v-if="course.status_id != 5" @click="closeCourse" >Close Course </button>
                        <button class="btn btn-sm btn-danger" type="button" v-else v-show="profile.admin_type_id == 1" @click="closeCourse" >Reopen Course </button>
                        <span class="float-right">
                            <a class="btn btn-sm btn-dark" :href="'/appointments/courses/'+activeYear"> <i class="fas fa-arrow-left"></i> Courses List </a>
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
                        <div class="tab-pane fade show active pt-1" id="pills-dstab1" role="tabpanel" aria-labelledby="pills-dstab1-tab">
                            <div class="row alert alert-danger my-2 text-uppercase mx-0 p-2 text-center" v-show="course.insurance_id > 1 && course.ins_approval == 2">
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Code -- {{ course.approval_code }} </h5>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0" v-if="course.reapproval_code">Reapproval Code -- {{ course.reapproval_code }} </h5>
                                    <button class="btn btn-sm btn-green-theme" type="button" @click="approvalInvoiceCourse(course.course_code, course.insurance_id)" v-else-if="course.show_reapproval == 1">Got Reapproval</button>
                                    <span v-else></span>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Expiry Date -- {{ course.end_date | setdate }} </h5>
                                </div>
                            </div>
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
                                                    <td>{{ course.appointments_count | freeNumber }} / {{ course.appointments_total | freeNumber }}</td>
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
                                                    <th class="wf-175">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(invoice, ikey) in invoices" :key="ikey">
                                                    <td>{{ invoice.invoice_number }}</td>
                                                    <td>{{ invoice.created_at | setfulldate}}</td>
                                                    <td>{{ (invoice.insurance_id == 1) ? 'CASH' : 'Insurance' }}</td>
                                                    <td>{{ invoice.amount | formatOMR }}</td>
                                                    <td>{{ invoice.payable_amount | formatOMR }}</td>

                                                    <td v-if="invoice.insurance_id != 1">
                                                        <a target="_blank" class="btn btn-sm btn-dark" :href="'/invoices/print-course-invoice/'+invoice.invoice_number" v-if="course.ins_approval == 2">
                                                        <i class="fas fa-print"></i> Print</a>
                                                        <a target="_blank" class="btn btn-sm btn-danger" :href="'/appointments/preapproval-course-invoice/'+invoice.invoice_number" v-else>
                                                        <i class="fas fa-print"></i> Preapproval</a>
                                                    </td>
                                                    <td v-else>
                                                        <a target="_blank" class="btn btn-sm btn-dark" :href="'/invoices/print-course-finvoice/'+invoice.invoice_number" v-if="course.status_id == 9">
                                                        <i class="fas fa-print"></i> Print</a>
                                                        <a target="_blank" class="btn btn-sm btn-dark" :href="'/invoices/print-coursep-invoice/'+invoice.invoice_number" v-else>
                                                        <i class="fas fa-print"></i> Print</a>
                                                        <button class="btn btn-sm btn-success" @click="AddPayment(invoice)" v-show="invoice.payment_status == 2"> <i class="fa fa-plus"></i>  Payment</button>
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
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <h5>Doctor Consultation Details</h5>
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Visit Type</th>
                                                    <th>Type</th>
                                                    <th>Consulatation</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ consultation.appointment_code }}</td>
                                                    <td>{{ consultation.visit_type }}</td>
                                                    <td>{{ consultation.appointment_type }}</td>
                                                    <td>{{ consultation.reason }}</td>
                                                    <td>{{ consultation.date | setdate }}</td>
                                                    <td>{{ listSlots[consultation.start_timeslot]+' - '+clistSlots[consultation.end_timeslot] }}</td>
                                                    <td> <button class="btn btn-sm " :class="consultation.status_css">{{ consultation.status }} </button> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Therapies</th>
                                                <th>Count</th>
                                                <th>Approved</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(therapy, tkey) in treatments" :key="'t-'+tkey">
                                                <td>{{ therapy.name }}</td>
                                                <td>{{ therapy.days }}</td>
                                                <td>{{ (therapy.approved)?therapy.approved:therapy.days }}</td>
                                                <td>{{ therapy.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade pt-1" id="pills-dstab2" role="tabpanel" aria-labelledby="pills-dstab2-tab">
                            <div class="row alert alert-danger my-2 text-uppercase mx-0 p-2 text-center" v-show="course.insurance_id > 1 && course.ins_approval == 2">
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Code -- {{ course.approval_code }} </h5>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0" v-if="course.reapproval_code">Reapproval Code -- {{ course.reapproval_code }} </h5>
                                    <button class="btn btn-sm btn-green-theme" type="button" @click="approvalInvoiceCourse(course.course_code, course.insurance_id)" v-else-if="course.show_reapproval == 1">Got Reapproval</button>
                                    <span v-else></span>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Expiry Date -- {{ course.end_date | setdate }} </h5>
                                </div>
                            </div>
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
                                                <span v-if="customer.identity_copy && (customer.identity_copy != 'undefined')">
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
                        <div class="tab-pane fade pt-1" id="pills-dstab3" role="tabpane3" aria-labelledby="pills-dstab3-tab">
                            <div class="row alert alert-danger my-2 text-uppercase mx-0 p-2 text-center" v-show="course.insurance_id > 1 && course.ins_approval == 2">
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Code -- {{ course.approval_code }} </h5>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0" v-if="course.reapproval_code">Reapproval Code -- {{ course.reapproval_code }} </h5>
                                    <button class="btn btn-sm btn-green-theme" type="button" @click="approvalInvoiceCourse(course.course_code, course.insurance_id)" v-else-if="course.show_reapproval == 1">Got Reapproval</button>
                                    <span v-else></span>
                                </div>
                                <div class="col-4 col-md-4 p-0">
                                    <h5 class="m-0">Approval Expiry Date -- {{ course.end_date | setdate }} </h5>
                                </div>
                            </div>
                            <div class="form-group">
                                <h5 class="m-b-15">Appointment Details
                                    <span class="float-right">
                                        <button class="btn btn-sm btn-primary" v-show="(course.status_id < 5) && (course.insurance_id > 1)" @click="setCashInvoice()">Set Cash Invoice </button>
                                        <button class="btn btn-sm btn-danger" v-show="course.status_id < 5" @click="setInvoice()">Set Invoice </button>
                                        <button class="btn btn-sm btn-dark" type="button" @click="makeAppointment" v-show="((course.ins_approval == 2) || (course.insurance_id == 1))"> <i class="fas fa-plus"></i> New Appointment</button>
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
                                                <span class="btn-group editbox" role="group" v-show="(appointment.status_id != 5) && (activeEdit(appointment.date))">
                                                    <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <span class="dropdown-menu dropdown-menu-right m-0 p-0" aria-labelledby="btnGroupDrop1">
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="patientBox(appointment)">Change Patient</button>
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="doctorBox(appointment)">Modify Appointment</button>
                                                    </span>
                                                </span>
                                                <span class="btn-group editbox" role="group" v-show="appointment.status_id != 5">
                                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fas fa-radiation-alt"></i>
                                                    </button>
                                                    <span class="dropdown-menu dropdown-menu-right m-0 p-0" aria-labelledby="btnGroupDrop1">
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="completeAppointment(appointment.appointment_code)">Change Status To Complete</button>
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="cancelAppointment(appointment.appointment_code)" v-show="activeEdit(appointment.date)">Cancel Appointment</button>
                                                    </span>
                                                </span>
                                                <button class="btn btn-primary" @click="viewComment(appointment.comment)" v-show="appointment.comment"> <i class="fa fa-envelope"></i> </button>
                                                <button class="btn btn-purple" @click="TransferInInsurance(appointment.appointment_code)" v-if="(course.insurance_id >= 2) && (appointment.invoice == '' || appointment.invoice == null) && (checkTransferStatus(appointment.treatment_id) == false)"> 
                                                    <i class="fas fa-exchange-alt"></i> 
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                <div class="col-4" v-if="aform.insurance_id != 1">
                                    <label class="control-label">Insurance Details</label><hr class="m-b-5 m-t-5">
                                    <span v-if="InsuranceExpired()">
                                        <p class="alert-danger p-l-5 p-r-5">
                                            <span v-if="expirynote">
                                                {{ expirynote }}
                                            </span>
                                            <span v-else>
                                                Insurance has been expired on {{ customer.expiry_date | setdate }}.
                                            </span>
                                        </p>
                                    </span>
                                    <span v-else>
                                        <p>
                                            <span v-show="insurance.name">
                                                <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="aform.insured" @change="reTotal">
                                                <b>{{ insurance.name }}</b> (Co-Insurance: {{ customer.co_insurance }})</p-check>
                                                <span class="alert-danger p-l-5 p-r-5" v-show="expirynote">{{ expirynote }}</span>
                                            </span>
                                            <!-- <span v-else class="text-danger">
                                                <i>{{customer.insurancer}} does not cover this treatment.</i>
                                            </span> -->
                                        </p>
                                    </span>
                                </div>
                                <div class="col-4" v-else>
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
                                                    <option value="1">Full Payment</option>
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
                                    <div v-show="(aform.payment_mode == 'visa') || (aform.payment_mode == 'cash+visa') || (aform.payment_mode == 'epay')" class="m-b-15">
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
                            <button type="submit" class="btn btn-wide btn-success" v-else>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setInsInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createInsInvoice()">
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
                                                <th>Price (RO)</th>
                                                <th>Discount (RO)</th>
                                                <th>Total (RO)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(treatment, akey) in aiform.appointments" :key="akey">
                                                <td>
                                                    <button type="button" @click="removeIBar(akey)" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </td>
                                                <td>{{ akey+1 }}</td>
                                                <td>{{ treatment.treatment }} ({{ treatment.apcode }})</td>
                                                <td style="text-align:right">{{ treatment.cost | formatOMR }}</td>
                                                <td style="text-align:right">{{ treatment.discount | formatOMR }}</td>
                                                <td style="text-align:right">{{ treatment.total | formatOMR }}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Sub Total</th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input type="text" v-model="aiform.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Net Payable</th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input type="text" v-model="aiform.total" name="total" id="total" readonly="readonly" class="form-control">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Insurance Approved Amount</th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input type="text" v-model="aiform.ins_amount" name="total" id="ins_amount" class="form-control">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 2">
                            <button type="submit" class="btn btn-wide btn-success" v-else>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="addApponitModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addApponitModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                     <form @submit.prevent="editmode ? updateAppointment() : createAppointment()">
                        <div class="modal-header">
                             <h5 class="modal-title" id="createAppointModalTitle" v-if="editmode == true">Edit Appointment</h5>
                            <h5 class="modal-title" id="createAppointModalTitle" v-else>Create Appointment</h5>
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
                                                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date"  :auto-submit="true" :max="course.end_date | validdate" v-if="course.end_date"></vp-date-picker>
                                                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date"  :auto-submit="true" v-else></vp-date-picker>
                                                </div>
                                                <has-error :form="form" field="date"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="treatment_id" class="control-label">Treatments</label>
                                                <select v-model="form.treatment_id" name="treatment_id" id="treatment_id"  @click="showType" class="form-control" :class="{ 'is-invalid': form.errors.has('treatment_id') }">
                                                    <option disabled value="">Select Treatments</option>
                                                    <option v-for="(treatment, key) in treatments" :key="key" v-bind:value="treatment.id">
                                                        {{ treatment.name}}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="treatment_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label class="control-label">Default Time Required</label>
                                            <p>{{ timetaken }}</p>
                                        </div>
                                        <div class="col-3">
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
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 4">
                            <button type="submit" class="btn btn-wide btn-success" v-else>
                                  <span v-if="editmode == true"> Update </span> <span v-else>  Create </span></button>
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
        <div class="modal fullbarmodal fade" id="paymentModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="paymentModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="Submitpayment()">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Payment</h5>
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
                                                <th>Invoice No</th>
                                                <th>Net Payable (RO)</th>
                                                <th>Paid Amount (RO)</th>
                                                <th width="180px">Remaining Balance (RO)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ payment_invoice.invoice_number }}</td>
                                                <td>{{ payment_invoice.payable_amount | formatOMR }}</td>
                                                <td>{{ payment_invoice.paid | formatOMR }}</td>
                                                <td>{{ payment_invoice.remain_balance | formatOMR  }}</td>
                                            </tr>
                                            <tr>
                                                <th  class="text-right text-uppercase" colspan="3">Payment Type</th>
                                                <td>
                                                    <select class="form-control" :class="{ 'is-invalid': pform.errors.has('payment_type') }" v-model="pform.payment_type" name="payment_type" @change="setPPaying">
                                                        <option value="2">Partial Payment</option>
                                                        <option value="3">Full Payment</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right text-uppercase">
                                                    Paying Now
                                                </th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input type="text" class="form-control" v-model="pform.paying_now" placeholder="amount paying now" v-if="pform.payment_type == 2">
                                                        <input type="text" class="form-control" v-model="pform.paying_now" readonly="readonly" v-else>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row m-t-15">
                                <div class="form-group col-md-3">
                                    <div class="form-group">
                                        <label for="date" class="control-label d-inline-block">Select Date</label>
                                        <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="pform.date"  :auto-submit="true"></vp-date-picker>
                                        <has-error :form="pform" field="date"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_mode" class="control-label">Payment Mode</label>
                                        <select class="form-control" :class="{ 'is-invalid': pform.errors.has('payment_mode') }" v-model="pform.payment_mode" name="payment_mode">
                                            <option value="cash">Cash</option>
                                            <option value="credit">Credit</option>
                                            <option value="epay">E-Payment</option>
                                            <option value="visa">VISA Card</option>
                                            <option value="cash+visa">Cash + VISA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea class="form-control" v-model="pform.remark" name="remark"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div v-show="(pform.payment_mode == 'visa') || (pform.payment_mode == 'cash+visa') || (pform.payment_mode == 'epay')" class="m-b-15">
                                        <label for="txn_id" class="control-label">Transaction Number</label>
                                        <input type="text" class="form-control" :class="{ 'is-invalid': pform.errors.has('txn_id') }" v-model="pform.txn_id" name="txn_id">
                                    </div>
                                    <div v-show="(pform.payment_mode == 'cash+visa')" class="m-b-15 row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cash_amount" class="control-label">Paid in cash </label>
                                                <input type="text" v-model="pform.cash_amount" name="cash_amount" class="form-control" @keyup="getPCCashVisa">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="visa_amount" class="control-label">Paid By Visa Card </label>
                                                <input type="text" v-model="pform.visa_amount" name="visa_amount" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-2"></div>

                                <div class="form-group col-md-4">
                                    <table class="table table-bordered" v-show="(pform.payment_mode == 'cash') || (pform.payment_mode == 'cash+visa')">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="text-center text-uppercase">Calculate Returnable Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label for="received">Received</label>
                                                    <input type="text" class="form-control" v-model="pform.received" placeholder="enter received value">
                                                </td>
                                                <td> <label for="received">--</label><br>
                                                <button class="btn btn-sm btn-dark" type="button" @click="pcalculate" >Calculate</button> </td>
                                                <td>
                                                    <label for="received">Returnable</label>
                                                    <input type="text" class="form-control" v-model="pform.returnable" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><i class="text-danger">{{ pcalcmsg}}</i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 5">
                            <button type="submit" class="btn btn-wide btn-success" v-else>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="cformModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="cformModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateComment">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addxrayModalTitle">Add Your comment and Close Appointment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times text-white"></i>
                            </button>
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
        <div class="modal fade" id="msgModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addxrayModalTitle">Remark By Therapist</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ comment }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-wide btn-danger m-0 btn-sm" data-dismiss="modal"> Close </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import TransferButton from './TransferButton.vue';
    export default {
        components:{TransferButton},
        data() {
            return {
                editmode: false,
                svgurl: '/svg/',
                docurl: '/files/docs/',
                loader: false,
                loaderurl: '/svg/loop.gif',
                course: [],
                therapy_package:'',
                catchmessage: '',
                consultation:'',
                pcalcmsg:'',
                currentYear: new Date().getFullYear(),
                activeID: '',
                activeYear:'',
                active_location: '',
                appointment:'',
                appointments:[],
                listSlots: [],
                clistSlots: [],
                customer: {},
                treatment:'',
                insurance:'',
                options:'',
                countlist:[],
                medicines:[],
                stocks:[],
                invoice:{},
                invoices:{},
                acountlist:[],
                calcmsg:'',
                discounts:[],
                discount:{title:'',value:0},
                statuses:[2,4,9],
                profile:'',
                aform: new Form({
                    type:6,
                    total:'',
                    payment_mode:'cash',
                    txn_id:'',
                    remark:'',
                    returnable:'',
                    received:'',
                    appointments:'',
                    course_code:'',
                    id:'',
                    user_id:'',
                    invoice_number:'',
                    apt__reason:[],
                    apt__apcode:[],
                    apt__datetime:[],
                    apt__cost:[],
                    apt__tid:[],
                    insurance_id:'',
                    insured:0,
                    sub_total:'',
                    insured_deduction:0,
                    insured_deduction_reason:'',
                    offered:0,
                    offered_reason:'',
                    offered_value:0,
                    cash_amount:'',
                    visa_amount:'',
                    payment_type:1,
                    listlen:'',
                    paying_now:''
                }),
                aiform: new Form({
                    sub_total:'',
                    approved:1,
                    invoice_number:'',
                    total:'',
                    ins_amount:'',
                    ins_discount:'',
                    appointments:''
                }),
                start_times: {},
                end_times: {},
                stherapists: [],
                treatments:[],
                timetaken:'',
                ttype:'',
                tdual:0,
                rooms:'',
                aval_slots:{},
                timeslots:'',
                naval_slots:[],
                nblockslots: [],
                nfixedslots: [],
                therapists:'',
                fixedslots:'',
                form: new Form({
                    id:'',
                    appointment_code:'',
                    location_id:'',
                    user_id:'',
                    room_id:'',
                    course_id:'',
                    doctor_id: '',
                    appointment_type_id:2,
                    treatment_id:'',
                    second_doctor_id:'',
                    date:'',
                    start_time:'',
                    end_time:'',
                    visit_type_id:1,
                    status_id:4,
                    followup_appointment:'',
                    followup_verified:'',
                }),
                customers: [],
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
                pform: new Form({
                    payment_type:2,
                    course_code:'',
                    invoice_number:'',
                    paying_now:'',
                    payment_mode:'',
                    date:'',
                    txn_id:'',
                    remark:'',
                    received:'',
                    returnable:'',
                    visa_amount:'',
                    cash_amount:''
                }),
                payment_invoice:{},
                comments:{},
                comment:'',
                cform: new Form({
                    appointment_code:'',
                    comment:'',
                    other:''
                }),
            }
        },
        methods: {
            viewComment(msg) {
                this.comment = msg;
                $('#msgModal').modal('show');
            },
            activeEdit(adate) {
                let today = new Date();
                let ndate = new Date(adate);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration =moment.duration(b.diff(a));
                return (diffDuration.days() >= 0)?true:false;
            },
            getIndex(list, id) {
                return id;
            },
            statusActive(sid) {
                return this.statuses.includes(sid);
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return '('+diffDuration.years()+' yrs)';
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
            },
            setpayType() {
                let ptype = this.aform.payment_type;
                if(ptype == 3){
                    this.aform.being_paid = this.aform.total;
                } else {
                    this.aform.being_paid = '';
                }
                this.aform.received = '';
                this.aform.returnable = '';
            },
            setPaying() {
                if(this.aform.payment_type == 2){
                    this.aform.paying_now = '';
                    if(this.aform.payment_mode == 'cash+visa') {
                        this.aform.cash_amount = '';
                        this.aform.visa_amount = '';
                    }
                } else {
                    this.aform.paying_now = this.makeNumber(this.aform.total);
                }
            },
            calculate(){
                let tc, nt, rc;
                if(this.aform.visa_amount >= 1){
                    tc = this.makeNumber(this.aform.paying_now) - this.makeNumber(this.aform.visa_amount);
                } else {
                    tc = this.makeNumber(this.aform.paying_now);
                }
                if(this.aform.received < tc) {
                    this.calcmsg = 'Received amount must be greater than or equal to payable by customer in cash.';
                    this.aform.received = '';
                    this.aform.returnable = '';
                    return;
                }
                else {
                    this.calcmsg = '';
                }
                let returnable = this.makeNumber(this.aform.received) - tc;
                this.aform.returnable = returnable.toFixed(3);
            },
            getAllAssets() {
                let activeId = this.$route.path.split("/");
                this.activeID  = activeId[4];
                axios.get('/api/get-time-slots').then(({ data }) => { this.timeslots = data; });
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/get-active-user').then((response) => {this.active_location = response.data[0].location_id;}).catch();
                axios.get('/api/get-course-discounts').then(response => { this.discounts = response.data; });
            },
            showCourse() {
                this.$Progress.start();
                axios.get('/api/courses/'+this.activeID).then(({ data }) => {
                    this.course = data;
                    this.activeYear = data.year;
                    if(data.invoice_count >= 1) {
                        axios.post('/api/get-invoices-bycourses', { invoices:data.invoices })
                        .then((data5) => { this.invoices = data5.data; }).catch();
                    }
                    axios.get('/api/customers/'+this.course.user_id).then((data1) => { this.customer = data1.data[0];}).catch();
                    axios.get('/api/appointments/view/'+this.course.consult_code)
                    .then((data3) => { this.consultation = data3.data[0]; this.treatments = JSON.parse(data3.data[0].therapies);
                        this.course.appointments_total = data3.data[0].total_treatments; })
                    .catch();
                    axios.post('/api/appointments/get-bulk-appointments', { appointments: this.course.appointments })
                    .then((data2) => { this.appointments = data2.data; this.setInvoiceData(); this.course.appointments_count = data2.data.length; })
                    .catch();
                    this.$Progress.finish();
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
            setInvoice() {
                if(this.course.insurance_id == 1) {
                    this.checkDiscount();
                    $('#setInvocieModal').modal('show');
                } else {
                    let $aiform = this.aiform;
                    this.aiform.invoice_number = this.invoices[0].invoice_number;
                    axios.post('/api/set-appvInsured-treatments', {
                            appointments:this.appointments,
                            treatments:this.treatments,
                            insurance_id:this.course.insurance_id,
                            invoice_number:this.course.invoice
                        }).then((data2) => {
                            $aiform.appointments =  data2.data.treatments;
                            $aiform.discount_value =  data2.data.discount_value;
                            $aiform.total =  data2.data.total;
                            $aiform.sub_total =  data2.data.sub_total;
                            $aiform.ins_amount = data2.data.total;
                    });
                    $('#setInsInvocieModal').modal('show');
                }
            },
            setCashInvoice() {
                if(this.course.insurance_id != 1) {
                    //alert();
                    this.aform.insured = 0;
                    this.aform.insurance_id = 1;
                    let $aform = this.aform;
                    let $ye = this;
                    let subtotal = 0;
                    axios.post('/api/appointments/get-bulk-cash-invoices', {
                        appointments:this.course.appointments
                    }).then((data) => {
                        $ye.acountlist = data.data.countlist;
                        $aform.listlen = data.data.countlist.length;
                        data.data.appointments.forEach((element, key) => {
                            $aform.apt__reason.push(element.reason);
                            $aform.apt__apcode.push(element.appointment_code);
                            $aform.apt__datetime.push(element.date+' '+element.time);
                            $aform.apt__cost.push(element.cost);
                            subtotal = this.makeNumber(subtotal) + this.makeNumber(element.cost);
                        });
                        $aform.sub_total = subtotal;
                        $aform.total = subtotal;
                        $aform.paying_now = subtotal;
                        this.checkDiscount();
                    });
                    $('#setInvocieModal').modal('show');
                } else {
                    swal.fire({
                        title: 'Oops',
                        type: 'error',
                        text: 'Invalid function call',
                    })
                }
            },
            checkDiscount() {
                let listlen = this.aform.apt__reason.length;
                let acount = this.appointments.length;
                this.aform.listlen = listlen;
                if(this.course.insurance_id == 1) {
                    acount = listlen
                }
                if(acount >= 5) {
                    this.discount = this.discounts[1];
                } else if((acount == 3) || (acount == 4)) {
                    this.discount = this.discounts[0];
                } else {
                    this.aform.offered = 0;
                    this.discount = {'title':'No discount available', 'value':0};
                }
            },
            setInvoiceData() {
                let $ye = this; let $aform = this.aform; let subtotal = 0;
                this.aform.user_id = this.course.user_id;
                this.aform.course_code = this.course.course_code;
                this.aform.insurance_id = this.course.insurance_id;
                if(this.course.insurance_id == 1) {
                    axios.post('/api/appointments/get-bulk-cash-invoices', {
                        appointments:this.course.appointments
                    }).then((data) => {
                        $ye.acountlist = data.data.countlist;
                        $aform.listlen = data.data.countlist.length;
                        data.data.appointments.forEach((element, key) => {
                            $aform.apt__reason.push(element.reason);
                            $aform.apt__apcode.push(element.appointment_code);
                            $aform.apt__datetime.push(element.date+' '+element.time);
                            $aform.apt__cost.push(element.cost);
                            $aform.apt__tid.push(element.cost);
                            subtotal = this.makeNumber(subtotal) + this.makeNumber(element.cost);
                        });
                        $aform.sub_total = subtotal;
                        $aform.total = subtotal;
                        $aform.paying_now = subtotal;
                    });
                }
                else {
                    this.discount = {'title':'No discount available', 'value':0};
                    axios.post('/api/appointments/get-bulk-insurance-invoices', {
                        appointments:this.course.appointments,
                        insurance_id: this.course.insurance_id
                    }).then((data) => {

                    })
                }

            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            getCCashVisa() {
                let visa_amount = this.makeNumber(this.aform.paying_now) - this.makeNumber(this.aform.cash_amount);
                this.aform.visa_amount = visa_amount.toFixed(3);
            },
            createInvoice() {
                if(this.aform.payment_mode == 'visa') {
                    if((this.aform.txn_id === null) || (this.aform.txn_id === '') || (this.aform.txn_id === 0)) {
                        swal.fire('Missing Values', 'Transaction number is required for VISA payment mode.', 'error');
                        return false;
                    }
                }
                else if(this.aform.payment_mode == 'epay') {
                    if((this.aform.txn_id === null) || (this.aform.txn_id === '') || (this.aform.txn_id === 0) || (this.aform.remark === '')) {
                        swal.fire('Missing Values', 'Transaction number and Remark are required for E-Payment mode.', 'error');
                        return false;
                    }
                }
                else if(this.aform.payment_mode == 'cash+visa') {
                    if((this.aform.txn_id === null) || (this.aform.visa_amount <= 0)) {
                        swal.fire('Missing Values', 'Transaction number and amount paid by VISA is required for this payment mode.', 'error');
                        return false;
                    }
                }
                this.loader = 1;
                this.aform.post('/api/invoices/create-schedule-course-invoice')
                .then((data) => {
                    this.loader = false;
                    $('#setInvocieModal').modal('hide');
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        title: 'Invoice has been created successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print Invoice',
                        cancelButtonText: 'Stay On The Page',
                    }).then((result) => {
                        if (result.value) {
                            this.$router.push('/appointments/print-schcourse-invoice/'+data.data.invoice);
                        }
                    }).catch(() => {
                        this.loader = false;
                        swal.showValidationMessage(`Request failed: ${error}`);
                    });
                })
                .catch(() => {
                    this.loader = false;
                    swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            createInsInvoice() {
                this.loader = 2;
                this.aiform.post('/api/invoices/create-ischedule-course-invoice')
                .then((data) => {
                    this.loader = false;
                    $('#setInsInvocieModal').modal('hide');
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        title: 'Invoice has been created successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print Invoice',
                        cancelButtonText: 'Stay On The Page',
                    }).then((result) => {
                        if (result.value) {
                            this.$router.push('/appointments/print-ischcourse-invoice/'+data.data.invoice);
                        }
                    }).catch(() => {
                        swal.showValidationMessage(`Request failed: ${error}`);
                    });
                })
                .catch(() => {
                    this.loader = false;
                    swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            InsuranceExpired() {
                if(this.customer.insurance_id == 1) {
                    this.expirynote = 'Cash Customer';
                    return true;
                }
                else {
                    let today = new Date();
                    let ndate = new Date(this.customer.expiry_date);
                    var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                    var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                    var diffDuration = moment.duration(b.diff(a));
                    var days = diffDuration.as('days');
                    if(Math.sign(days) == '-1'){
                        this.expirynote = 'Insurance Policy has been expired. Please update the details if insurance has been renewed.';
                        return true;
                    }
                    else if(days == 0) {
                        this.expirynote = 'Insurance Policy is expiring today. Inform Customer to renew it on time.';
                        return false;
                    } else if(15 >= days > 0) {
                        this.expirynote = days +' day(s) left to expire the Insurance.';
                        return false;
                    } else {
                        this.expirynote = '';
                        return false;
                    }
                }
            },
            removeADiscount() {
                this.aform.discount_id = '';
                this.aform.offered_value = 0;
                this.aform.offered = 0;
                this.aform.offered_reason = '';
                this.aform.paying_now = this.aform.total;
                this.aform.payment_type = 1;
                this.aform.cash_amount = '';
                this.aform.visa_amount = '';
                this.reTotal();
            },
            removeBar(barkey){
                this.acountlist.splice(barkey, 1);
                this.aform.sub_total = this.aform.sub_total - this.aform.apt__cost[barkey];
                this.aform.apt__reason.splice(barkey, 1);
                this.aform.apt__apcode.splice(barkey, 1);
                this.aform.apt__datetime.splice(barkey, 1);
                this.aform.apt__cost.splice(barkey, 1);
                this.checkDiscount();
                this.removeADiscount();
            },
            removeIBar(barkey){
                let $aiform = this.aiform;
                let sub_total = 0;
                $aiform.appointments.splice(barkey, 1);
                $aiform.appointments.forEach((element, key) => {
                    sub_total = this.makeNumber(sub_total) + this.makeNumber(element.total);
                });

                $aiform.total =  sub_total.toFixed(3);
                $aiform.sub_total =  sub_total.toFixed(3);
                $aiform.ins_amount = sub_total.toFixed(3);
            },
            addInsurance() {
                this.aform.offered = 0; this.aform.offered_value = '';
                if(this.aform.insured == 1){
                    if(this.appointment.appointment_type_id == 1) {
                        this.aform.insured_discount_reason = this.insurance.discount+' discount';
                        this.aform.insured_deduction_reason =this.customer.consult_deductable+' deductable';
                        if(this.insurance.discount.indexOf('%') >= 1) {
                            this.aform.insured_discount = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.discount.replace('%', ''))/100).toFixed(3);
                        }
                        else {
                            this.aform.insured_discount = this.makeNumber(this.insurance.discount).toFixed(3);
                        }

                        if(this.customer.consult_deductable === null) {
                            if(this.customer.co_insurance === null) {
                                //this.aform.total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                                this.aform.total = 0.000;
                            }
                            else if(this.customer.co_insurance.indexOf('%') >= 1) {
                                this.aform.total = this.makeNumber((this.makeNumber(this.aform.sub_total)  - this.makeNumber(this.aform.insured_discount))*this.makeNumber(this.customer.co_insurance.replace('%', ''))/100).toFixed(3);
                            }
                            else if(this.customer.co_insurance >= 1) {
                                this.aform.total = this.makeNumber(this.customer.co_insurance).toFixed(3);
                            }
                            else {
                                this.aform.total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                                this.aform.total = this.aform.total.toFixed(3);
                            }
                        }
                        else if(this.customer.consult_deductable.indexOf('%') >= 1) {
                            this.aform.total = this.makeNumber((this.makeNumber(this.aform.sub_total)  - this.makeNumber(this.aform.insured_discount))*this.makeNumber(this.customer.consult_deductable.replace('%', ''))/100).toFixed(3);
                        }
                        else {
                            this.aform.total = this.makeNumber(this.customer.consult_deductable).toFixed(3);
                        }


                    }
                    else if(this.appointment.appointment_type_id == 2) {
                        this.aform.insured_discount_reason = this.insurance.discount+' discount';
                        this.aform.insured_deduction_reason = this.customer.treatment_deductable+' deductable';

                        if(this.insurance.discount.indexOf('%') >= 1) {
                            this.aform.insured_discount = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.discount.replace('%', ''))/100).toFixed(3);
                        }
                        else {
                            this.aform.insured_discount = this.makeNumber(this.insurance.discount).toFixed(3);
                            this.aform.total = this.aform.total.toFixed(3);
                        }
                        if(this.customer.co_insurance === null) {
                            this.aform.total = 0.000; //this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                        }
                        else if(this.customer.co_insurance.indexOf('%') >= 1) {
                            this.aform.total = this.makeNumber((this.makeNumber(this.aform.sub_total)  - this.makeNumber(this.aform.insured_discount))*this.makeNumber(this.customer.co_insurance.replace('%', ''))/100).toFixed(3);
                        }
                        else if(this.customer.co_insurance >= 1) {
                            this.aform.total = this.makeNumber(this.customer.co_insurance).toFixed(3);
                        }
                        else {
                            this.aform.total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount);
                            this.aform.total = this.aform.total.toFixed(3);
                        }
                    }
                    else {
                        this.aform.insured_discount = 0;
                        this.aform.insured_deduction = 0;
                        this.aform.insured_discount_value = '';
                        this.aform.insured_deduction_value = '';
                    }
                } else {
                    this.aform.insured_discount = 0;
                    this.aform.insured_deduction = 0;
                    this.aform.insured_discount_value = '';
                    this.aform.insured_deduction_value = '';
                }
            },
            addOffers() {
                let reason = ''; let offer = 0;
                this.aform.offered = 1;
                let adiscount = this.discount.value;
                if(adiscount.indexOf('%') >= 1) {
                    reason = adiscount+' discount';
                    offer = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(adiscount.replace('%', ''))/100);
                }
                else {
                    reason ='Fixed Discount';
                    offer = this.makeNumber(discount.value);
                }
                this.aform.offered_reason = reason;
                this.aform.offered_value = offer.toFixed(3);
                this.reTotal();
            },
            reTotal(){
                this.aform.returnable = ''; this.aform.received = '';
                if(this.aform.insured == 1) {
                 //  this.addInsurance();
                    let insured_deduction = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.total);
                    this.aform.insured_deduction = (this.makeNumber(insured_deduction) >= 0) ? this.makeNumber(insured_deduction).toFixed(3) : 0;
                } else if(this.aform.offered == 1) {
                    this.aform.insured = '';
                //    this.addOffers();
                    let total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.offered_value);
                    this.aform.total = (this.makeNumber(total) >= 0) ? this.makeNumber(total).toFixed(3) : 0;
                } else {
                    this.aform.insured_deduction = 0;
                    this.aform.total = this.makeNumber(this.aform.sub_total).toFixed(3);
                }
                if(this.aform.payment_type == 2) {
                    if(this.aform.paying_now > this.aform.total) {
                        this.aform.paying_now = this.aform.total;
                    }
                } else {
                    this.aform.paying_now = this.aform.total;
                }
            },
            isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
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
            getTimings(atype, did){
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+this.form.date+'&at='+atype+'&lid='+this.form.location_id+'&did='+did).then(({ data }) => { this.blockslots = data['blocked']; this.fixedslots = data['fixed'];  this.start_times = data['start_slots']; this.aval_slots = data['aval_slots']; });
            },
            getClosings(st){
                axios.get('/api/appointments/get-end-timings?q='+this.form.date+'&at='+this.form.appointment_type_id+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+st).then(({ data }) => {
                    this.end_times = data;
                });
            },
            getRooms(et){
                axios.get('/api/appointments/get-rooms?q='+this.form.date+'&lid='+this.form.location_id+'&st='+this.form.start_time+'&et='+et+'&apid='+this.form.appointment_code).then(({ data }) => {
                    this.rooms = data;
                });
                if(this.ttype == 1){
                   axios.get('/api/appointments/get-second-therapist?q='+this.form.date+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+this.form.start_time+'&et='+et+'&dtype='+this.tdual+'&apid='+this.form.appointment_code).then(({ data }) => {
                    this.stherapists = data;
                });
                }
            },
            makeAppointment() {
                this.catchmessage = '';
                this.form.reset();
                this.blockslots = [];
                this.fixedslots = [];
                this.start_times = [];
                this.form.location_id = this.active_location;
                this.form.course_id = this.course.course_code;
                this.form.user_id = this.course.user_id;
                //console.log(this.form.date);
                $('#addApponitModal').modal('show');
            },
            createAppointment() {
                this.loader = 4;
                this.form.post('/api/courses/make-course-appointment')
                .then(() => {
                    this.loader = false;
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addApponitModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Appointment created successfully.'
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
            patientBox(apid) {
                this.eform.appointment_code = apid.appointment_code;
                 axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                $('#patientModal').modal('show');
            },
            doctorBox(apid) {
                let pi = this.form;
                 this.editmode = true;
                pi.appointment_code = apid.appointment_code;
                axios.get('/api/appointments/form-view/'+apid.appointment_code)
                    .then((data) => {
                        pi.fill(data.data);
                        axios.get('/api/treatments/'+data.data.treatment_id).then((data2) => {
                            this.timetaken = data2.data.timing+' mins';
                            if(data2.data.is_it_dual == 1){ this.ttype = 1;
                                axios.get('/api/appointments/get-second-therapist?q='+data.data.date+'&lid='+data.data.location_id+'&st='  +data.data.start_timeslot+'&et='+data.data.end_timeslot+'&apid='+data.data.appointment_code+'&did='+this.form.doctor_id+'&dtype='+this.tdual+'&apid='+this.form.appointment_code).then(({ data }) => {  this.stherapists = data;  });
                            }
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

                        pi.start_time = data.data.start_timeslot;
                        pi.end_time = data.data.end_timeslot;
                    })
                    .catch();
                $('#addApponitModal').modal('show');
            },
            updatePatient(){
                this.eform.post('/api/appointments/change-patient')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#patientModal').modal('hide');
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
            approvalInvoiceCourse(id, iid) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You have received re approval code from insurance company",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'off', placeholder: 'Enter Repproval Code'  },
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Approve it!',
                    cancelButtonText: 'Not Now',
                     showLoaderOnConfirm: true,
                    preConfirm: (appcode) => {
                       return axios.post('/api/courses/reapprove-invoice', {
                            course_code:id,
                            approval_code:appcode,
                            insurance_id:iid
                        })
                        .then(response => {
                            //console.log(response.data.status);
                            if(response.data.status == 'error') {
                                throw new Error(response.data.message);
                            } else {
                                swal.fire('Approved!', 'Invoice has been approved.', 'success');
                                this.showCourse();
                            }
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            },
            updateAppointment() {
                this.form.post('/api/appointments/update-appointment')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                     $('#addApponitModal').modal('hide');
                     this.editmode = false;
                    toast.fire({
                        type:'success',
                        title:'Appointment has been updated successfully.'
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                });
            },
            AddPayment(invoice) {
                this.payment_invoice = invoice;
                this.pform.payment_mode = 2;
                this.pform.course_code = invoice.course_code;
                this.pform.invoice_number = invoice.invoice_number;
                $('#paymentModal').modal('show');
            },
            setPPaying() {
                if(this.pform.payment_type == 2){
                    this.pform.paying_now = '';
                    if(this.pform.payment_mode == 'cash+visa') {
                        this.pform.cash_amount = '';
                        this.pform.visa_amount = '';
                    }
                } else {
                    this.pform.paying_now = this.makeNumber(this.payment_invoice.remain_balance);
                }
            },
            getPCCashVisa() {
                let visa_amount = this.makeNumber(this.pform.paying_now) - this.makeNumber(this.pform.cash_amount);
                this.pform.visa_amount = visa_amount.toFixed(3);
            },
            pcalculate(){
                let tc, nt, rc;
                if(this.pform.visa_amount >= 1){
                    tc = this.makeNumber(this.pform.paying_now) - this.makeNumber(this.pform.visa_amount);
                } else {
                    tc = this.makeNumber(this.pform.paying_now);
                }
                if(this.pform.received < tc) {
                    this.pcalcmsg = 'Received amount must be greater than or equal to payable by customer in cash.';
                    this.pform.received = '';
                    this.pform.returnable = '';
                    return;
                }
                else {
                    this.pcalcmsg = '';
                }
                let returnable = this.makeNumber(this.pform.received) - tc;
                this.pform.returnable = returnable.toFixed(3);
            },
            Submitpayment() {
                if(this.pform.paying_now > this.payment_invoice.remain_balance) {
                    swal.fire('Damm!!', 'The paying amount is greater than remaining balance.', 'error');
                    return false;
                }
                this.loader = 5;
                this.pform.post('/api/submit-invoice-payment')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#paymentModal').modal('hide');
                    this.payment_invoice = {};
                    toast.fire({
                        type:'success',
                        title:'Your payment has been added successfully.'
                    });
                    this.pform.reset();
                    this.$Progress.finish();
                })
                .catch((response) => {
                   // this.catchmessage = response.message;
                   //this.flash(response.message, 'error');
                });
            },
            TransferInInsurance(apid) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You want to transfer this appointment in insurance?",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Do it!',
                    cancelButtonText: 'Not Now',
                    showLoaderOnConfirm: true,
                    preConfirm: (appcode) => {
                        return axios.post('/api/transfer-appointment-in-invoice', {
                            appointment_code:apid,
                            invoice_number:this.course.primary_invoice
                        })
                        .then(response => {
                            //console.log(response.data.status);
                            if(response.data.status == 'error') {
                                throw new Error(response.data.message);
                            } else {
                                swal.fire('Approved!', 'Invoice has been approved.', 'success');
                                this.showCourse();
                            }
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            },
            checkTransferStatus(tid) { 
                let t1, t2;
                let inv = this.course.primary_invoice;
                t2 = this.appointments.filter((apt) => { return (apt.treatment_id == tid && apt.invoice == inv) });
                t1 = this.treatments.filter((treat) => { return (treat.id == tid) })
                if(t1.length >= 1) {
                    t1 = parseInt((t1[0].approved)?t1[0].approved:t1[0].days)    
                } else {
                    t1 = 0;
                }
                
                t2 = parseInt(t2.length);
                if(t1 == t2) { return true; }
                if(t2 > t1) {
                    return true
                } else {
                    return false
                }
            },
            closeCourse() {
                swal.fire({
                        title: 'Complete Course',
                        type: 'question',
                        text: 'Are you sure, You want to close this course ? You will not be able to revert it.',
                        showCancelButton: true,
                        confirmButtonColor: '#06A23F',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Close Now',
                        cancelButtonText: 'Do Not Close',
                    }).then((result) => {
                        if (result.value) {
                            axios.post('/api/courses/close-course', {
                                course_code:this.course.course_code
                            }).then((data) => {
                                this.showCourse();
                                swal.fire('Completed', 'Course has been closed successfully.', 'success');
                            }).catch(() => {
                                swal.showValidationMessage(`Request failed: ${error}`);
                            });
                        }
                    });
            }
        },
        beforeMount() {
            axios.get('/api/get-active-user').then(response => {this.profile = response.data;});
        },
        mounted() {
            this.getAllAssets();
            this.showCourse();
            Fire.$on('AfterCreate', () => {
                this.showCourse();
            });
        }
    }
</script>
