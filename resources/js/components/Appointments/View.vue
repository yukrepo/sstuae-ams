<template>
    <div>
        <div class="content">
            <div class="m-t-5 mx-2">
                <div class="m-b-5" v-if="appointment.is_dummy == 0">
                    <div class="button-group">
                        <span v-if="appointment.appointment_type_id == 1">
                            <a class="btn btn-sm btn-primary" :href="'/appointments/'+appointment.clink+appointment.course_id" v-if="appointment.course_id">View Course</a>
                            <button  v-else class="btn btn-sm btn-primary" @click="createCourse()">Create Course</button>

                            <a class="btn btn-sm btn-success" target="_blank" :href="'/appointments/print-perscription/'+activeID">Print Prescription</a>

                            <span class="btn-group editbox" role="group" v-if="appointment.ainvoice">
                                <a target="_blank" class="btn btn-sm btn-dark" :href="'/invoices/print/'+appointment.ainvoice">
                                    <i class="fas fa-print"></i> Print Invoice
                                </a>
                            </span>
                            <button class="btn btn-sm btn-dark" @click="setInvoice()" v-else>Set Invoice </button>
                        </span>

                        <span v-else>
                            <span v-if="appointment.course_id">
                                <a class="btn btn-sm btn-primary" :href="'/appointments/'+appointment.clink+appointment.course_id" v-if="appointment.course_id">View Course </a>
                            </span>
                            <span v-else>
                                <button class="btn btn-sm btn-warning" @click="MoveInCourse()">Move In Course</button>
                                <a class="btn btn-sm btn-dark" target="_blank" :href="'/invoices/print/'+appointment.ainvoice"  v-if="appointment.ainvoice">Print Invoice </a>
                                <button class="btn btn-sm btn-dark" @click="setInvoice()" v-else>Set Invoice </button>
                            </span>
                        </span>


                        <span class="float-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary wf-200 dropdown-toggle" data-toggle="dropdown">
                                Return To Page </button>
                                <div class="dropdown-menu rdroplink wf-200">
                                    <a class="dropdown-item text-outline-success" href="/appointments/day-schedule">Todays Appointment </a>
                                    <a class="dropdown-item text-outline-primary" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                                    <a class="dropdown-item text-outline-secondary" :href="'/appointments/history/'+currentYear">History </a>
                                </div>
                            </div>
                        </span>

                    </div>
                </div>
                <div class="m-b-5" v-else>
                    <div class="button-group">
                        <span v-if="appointment.appointment_type_id == 1">
                            <a class="btn btn-sm btn-success" target="_blank" :href="'/appointments/print-perscription/'+activeID">Print Prescription</a>
                            <span class="btn-group editbox" role="group" v-if="appointment.ainvoice">
                                <a target="_blank" class="dropdown-item text-left bb-1 p-2" :href="'/invoices/print/'+appointment.ainvoice">
                                    <i class="fas fa-print"></i> Print Invoice
                                </a>
                            </span>
                        </span>

                        <span v-else>
                            <span v-if="appointment.course_id">
                                <a class="btn btn-sm btn-primary" :href="'/appointments/'+appointment.clink+appointment.course_id" v-if="appointment.course_id">View Course </a>
                            </span>
                            <span v-else>
                                <a target="_blank" class="dropdown-item text-left bb-1 p-2" :href="'/invoices/print/'+appointment.ainvoice">
                                    <i class="fas fa-print"></i> Print Invoice
                                </a>
                            </span>
                        </span>


                        <span class="float-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary wf-200 dropdown-toggle" data-toggle="dropdown">
                                Return To Page </button>
                                <div class="dropdown-menu rdroplink wf-200">
                                    <a class="dropdown-item text-outline-success" href="/appointments/day-schedule">Todays Appointment </a>
                                    <a class="dropdown-item text-outline-primary" :href="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </a>
                                    <a class="dropdown-item text-outline-secondary" :href="'/appointments/history/'+currentYear">History </a>
                                </div>
                            </div>
                        </span>

                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Appointment View</h3>
                    </div>
                    <div class="card-body p-t-15 p-b-15 p-r-15 p-l-15">
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
                                        <td>{{ customer.first_name }} {{ customer.last_name }}</td>
                                        <td>{{ customer.mobile }}</td>
                                        <td>{{ (customer.email)?customer.email:'--' }}</td>
                                        <td>{{ customer.married | capitalize }}</td>
                                        <td>{{ customer.date_of_birth | setdate }} {{ getAge(customer.date_of_birth) }}</td>
                                        <td>{{ customer.address }}, {{ customer.city }}</td>
                                        <td>
                                            <span v-if="customer.insurance_id >= 2">
                                                <img class="btn-img" v-img:docurl+customer.insurance_copy :src="this.docurl+customer.insurance_copy">
                                            </span> {{ customer.insurancer | capitalize }}
                                            <span v-show="customer.insurance_id > 1">({{ customer.policy_number }})</span><br>
                                            <em v-show="customer.insurance_id > 1"> {{ customer.expiry_date | setdate }}</em>
                                        </td>
                                        <td>
                                            <span v-if="customer.identity_copy">
                                                <img class="btn-img" v-img:docurl+customer.identity_copy :src="docurl+customer.identity_copy">
                                            </span> {{ customer.id_type | capitalize }} ({{ customer.verification_number }})</td>
                                        <td>
                                            <button class="btn btn-sm" :class="customer.css">{{ customer.status  }} </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <h5>Primary Appointment Details</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Visit Type</th>
                                        <th>Type</th>
                                        <th>Consulatation/Treatment</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ appointment.appointment_code }}</td>
                                        <td>{{ appointment.visit_type }}</td>
                                        <td>{{ appointment.appointment_type }}</td>
                                        <td>{{ appointment.reason }}</td>
                                        <td>{{ appointment.date | setdate }}</td>
                                        <td>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                        <td> <button class="btn btn-sm " :class="appointment.status_css">{{ appointment.status }} </button> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group" v-if="appointment.comment">
                            <h5>Closing Remark By Therapist</h5>
                            <p class="alert alert-info">
                                {{ appointment.comment }}
                            </p>
                        </div>

                        <span v-if="appointment.appointment_type_id == 1">
                            <div class="form-group">
                                <h5>Prescription Details</h5>
                                <div class="row">
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed" >
                                            <thead>
                                                <tr><th colspan="2">General Examination</th></tr>
                                            </thead>
                                            <tbody v-if="appointment.oe_categories">
                                                <tr v-for="(oe_category, okey) in JSON.parse(appointment.oe_categories)" :key="'o-'+okey">
                                                    <td>{{ oe_category.name }}</td>
                                                    <td>{{ oe_category.result }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="2">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr><th colspan="2">Diagnosis</th></tr>
                                            </thead>
                                            <tbody  v-if="appointment.diagnosis">
                                                <tr v-for="(diag, dkey) in JSON.parse(appointment.diagnosis)" :key="'d-'+dkey">
                                                    <td>{{ diag.code }}</td>
                                                    <td>{{ diag.remark }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="2">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed" >
                                            <thead>
                                                <tr><th colspan="2">Symptoms</th></tr>
                                            </thead>
                                            <tbody v-if="appointment.symptoms">
                                                <tr v-for="(symptom, skey) in JSON.parse(appointment.symptoms)" :key="'s-'+skey">
                                                    <td>{{ symptom.name }}</td>
                                                    <td>{{ symptom.remark }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="2">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed" >
                                            <thead>
                                                <tr><th colspan="2">Lab Tests</th></tr>
                                            </thead>
                                            <tbody v-if="appointment.tests">
                                                <tr v-for="(test, tekey) in JSON.parse(appointment.tests)" :key="'te-'+tekey">
                                                    <td>{{ test.type }} - {{ test.name }}</td>
                                                    <td>{{ test.remark }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="2">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr><th colspan="2">Drug History</th></tr>
                                            </thead>
                                            <tbody  v-if="appointment.drug_history">
                                                <tr v-for="(dhistory, tkey) in JSON.parse(appointment.drug_history)" :key="'t-'+tkey">
                                                    <td>{{ tkey.replace('_', ' ') | capitalize }}</td>
                                                    <td>{{ dhistory }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="2">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr><th colspan="2">Remarks</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2">{{ (appointment.dr_remark)?appointment.dr_remark:'-' }}</td>
                                                </tr>
                                                <tr v-show="appointment.reminder_days">
                                                    <td>Followup Reminder</td>
                                                    <td>{{ appointment.reminder_date | setdate }} <br>
                                                        {{ (appointment.reminder_days)?appointment.reminder_days+' days':'' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10" >
                                        <table class="table table-bordered table-striped table-condensed" >
                                            <thead>
                                                <tr><th colspan="3">Medicines</th></tr>
                                            </thead>
                                            <tbody v-if="appointment.medicines">
                                                <tr v-for="(medicine, mkey) in JSON.parse(appointment.medicines)" :key="'m-'+mkey">
                                                    <td>{{ medicine.name }}</td>
                                                    <td>{{ medicine.qty | freeNumber }} Packs, {{ medicine.days | freeNumber }} days - {{ medicine.dtab }}</td>
                                                    <td>{{ medicine.remark }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="3">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-3 m-b-10">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr><th colspan="3">Therapies</th></tr>
                                            </thead>
                                            <tbody  v-if="appointment.therapies">
                                                <tr v-for="(therapy, tkey) in JSON.parse(appointment.therapies)" :key="'t-'+tkey">
                                                    <td>{{ therapy.name }}</td>
                                                    <td>{{ therapy.days }}</td>
                                                    <td>{{ therapy.remark }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="3">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateInvoice() : createInvoice()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Create Invoice</h5>
                            <h5 class="modal-title" v-show="editmode">Update Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row m-b-20">
                                <div class="col-12">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th class="wf-150">Appointment ID</th>
                                                <th>Description</th>
                                                <th class="wf-150">Fees ({{ currency }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ activeID }}</td>
                                                <td>
                                                    <h6>{{ appointment.reason }}</h6>
                                                    <p v-show="appointment.visit_type_id == 2">(Free Revisit Followup)</p>
                                                </td>
                                                <td>
                                                    <span v-if="aform.insured == 1">
                                                        <span v-if="aform.showdiscount == 1">
                                                            {{ treatment.cost | formatOMR }}
                                                        </span>
                                                        <span v-else>
                                                            {{ (treatment.cost - aform.insured_discount) | formatOMR }}
                                                        </span>
                                                    </span>
                                                    <span v-else>
                                                         {{ treatment.cost | formatOMR }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row" v-show="appointment.visit_type_id != 2">
                                        <div class="col-6">
                                            <label class="control-label">Enable Tax</label>
                                            <hr class="m-b-5 m-t-5">
                                            <button type="button" @click="disableTax" v-if="aform.tax_enabled" class="btn btn-sm btn-dark wf-175">Disable Taxation</button>
                                            <button type="button" @click="enableTax" v-else class="btn btn-sm btn-outline-dark wf-175">Enable Taxation</button>
                                        </div>
                                        <div class="col-6">
                                            <label class="control-label">Discount by SST</label> 
                                            <button @click="removeADiscount" type="button" class="btn btn-sm btn-danger float-right"> <i class="fas fa-times"></i> </button>
                                            <hr class="m-b-5 m-t-5">
                                            <span v-if="discounts">
                                                <p>
                                                    <span class="input-checkbox" v-for="discount in discounts" :key="discount.id">
                                                        <input type="radio" v-model="aform.discount_id" :value="discount.id" name="aform.discount_id" :id="'discount'+discount.id" @change="reTotal">
                                                        <label :for="'discount'+discount.id"> {{ discount.title }}
                                                            <span class="m-l-5" v-if="checkPercent(discount.value)">
                                                                <b>({{ discount.value }})</b>
                                                            </span>
                                                            <span class="m-l-5" v-else>
                                                                <b>({{ discount.value | formatOMR }})</b>
                                                            </span>
                                                        </label>
                                                    </span>
                                                </p>
                                            </span>
                                            <span v-else>
                                                <em class="text-danger">No Discount available at the moment.</em>
                                            </span>

                                        </div>

                                    </div>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Subtotal</th>
                                                <td style="width: 100px;">
                                                    <span>
                                                        <input :value="aform.sub_total" type="text" readonly="readonly" class="form-control">
                                                        <input value="0" type="hidden" v-model="aform.sub_total" name="sub_total" id="sub_total">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0" v-if="aform.discount_id >= 1">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Discount - {{ aform.offered_reason }}</th>
                                                <td style="width: 100px;"><input value="0" type="text" v-model="aform.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('offered_value') }"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered m-b-0" v-if="aform.tax_enabled">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">{{ config.tax_label }}</th>
                                                <td style="width: 100px;"><input value="0" type="text" v-model="aform.tax_value" name="tax_value" id="tax_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('tax_value') }"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-right text-uppercase">Payable By Customer</th>
                                                <td style="width: 100px;"><input value="0" type="text" v-model="aform.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('total') }"></td>
                                            </tr>
                                        </thead>
                                    </table>
                                     <div class="row" v-show="appointment.visit_type_id != 2">
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
                                            <div v-show="(aform.payment_mode == 'epay') || (aform.payment_mode == 'visa') || (aform.payment_mode == 'cash+visa')" class="m-b-15">
                                                <label for="txn_id" class="control-label">Transaction Number</label>
                                                <input type="text" class="form-control" :class="{ 'is-invalid': aform.errors.has('txn_id') }" v-model="aform.txn_id" name="txn_id">
                                            </div>
                                            <div v-show="(aform.payment_mode == 'cash+visa')" class="m-b-15">
                                                <label for="visa_amount" class="control-label">Amount Paid  BY Visa</label>
                                                <input type="text" class="form-control" :class="{ 'is-invalid': aform.errors.has('visa_amount') }" v-model="aform.visa_amount" name="visa_amount">
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


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <span v-if="loader == 4">
                                <img class="wf-50" :src="loaderurl" alt="updating">
                            </span>
                            <span v-else>
                                <button v-show="!editmode" type="submit" class="btn btn-wide btn-success"> Submit </button>
                                <button v-show="editmode" type="submit" class="btn btn-wide btn-success"> Update </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setCourseModel"  data-backdrop="static" data-keyboard="false" aria-labelledby="setCourseModelTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form form @submit.prevent="submitCourse()" v-if="customer.insurance_id == 1">
                        <div class="modal-header">
                            <h5 class="modal-title" id="setCourseModelTitle">Create Course</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="doc-schdular">
                                <table class="table table-condensed table-striped table-bordered schedule-table">
                                    <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Treatment</th>
                                            <th>Suggest Date</th>
                                            <th>Therapist</th>
                                            <th>Availablity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(pcapp, pccount) in cformcount" :key="'p'+pccount">
                                            <td>{{ pccount + 1 }}</td>
                                            <td><input type="text" class="form-control" readonly v-model="cform.course__treatment_name[getIndex(pcapp, pccount)]"></td>
                                            <td>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="cform.course__date[getIndex(pcapp, pccount)]" :auto-submit="true"></vp-date-picker>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <select v-model="cform.course__doctor_id[getIndex(pcapp, pccount)]" :name="'course[doctor_id]['+pccount+']'" class="form-control" @change="getTimings(getIndex(pcapp, pccount))">
                                                        <option disabled value="">Select Therapist</option>
                                                        <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                            {{ doctor.name | capitalize }}
                                                        </option>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <input type="radio" v-model="cform.course__dchecked[getIndex(pcapp, pccount)]">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-4 p-r-0">
                                                        <div class="input-group">
                                                            <select v-model="cform.course__start_timeslot[getIndex(pcapp, pccount)]" :name="'course[start_timeslot]['+pccount+']'" class="form-control" @change="getClosings(getIndex(pcapp, pccount))">
                                                                <option disabled value="">Select Start Time</option>
                                                                <option v-for="(timeslot, key) in start_times[getIndex(pcapp, pccount)]" :key="key" v-bind:value="key">
                                                                    {{ timeslot }}
                                                                </option>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <input type="radio" v-model="cform.course__schecked[getIndex(pcapp, pccount)]">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 p-r-0">
                                                        <div class="input-group">
                                                            <select v-model="cform.course__end_timeslot[getIndex(pcapp, pccount)]" :name="'course[end_timeslot]['+pccount+']'" class="form-control" @change="getRooms(getIndex(pcapp, pccount))">
                                                                <option disabled value="">Select End Time</option>
                                                                <option v-for="(timeslot, key) in end_times[getIndex(pcapp, pccount)]" :key="key" v-bind:value="key">
                                                                    {{ timeslot }}
                                                                </option>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <div class="input-group-text">
                                                                    <input type="radio" v-model="cform.course__echecked[getIndex(pcapp, pccount)]">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <select v-model="cform.course__room_id[getIndex(pcapp, pccount)]" :name="'course[room_id]['+pccount+']'" class="form-control">
                                                            <option disabled value="">Select Room</option>
                                                            <option v-for="(room, key) in rooms[getIndex(pcapp, pccount)]" :key="key" v-bind:value="key">
                                                                {{ room }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <select v-model="cform.course__status_id[getIndex(pcapp, pccount)]" :name="'course[status_id]['+pccount+']'" class="form-control">
                                                    <option value="0">Do Not Book</option>
                                                    <option value="2">Book It Now</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 3">
                            <button type="submit" class="btn btn-wide btn-success" v-else> Create </button>
                        </div>
                    </form>
                    <p class="text-danger">Option is not available.</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
    export default {
        data() {
            return {
                svgurl: '/svg/',
                docurl: '/files/docs/',
                loader: false,
                loaderurl: '/svg/loop.gif',
                discounts:{},
                adiscount:{},
                editmode: false,
                catchmessage: '',
                calcmsg:'',
                currentYear: new Date().getFullYear(),
                currency: this.$root.$data.currency,
                config: this.$store.getters.configs,
                activeID: '',
                appointment:'',
                listSlots: [],
                clistSlots: [],
                customer: {},
                treatment:'',
                insurance:'',
                expirynote:'',
                options:'',
                countlist:[],
                icountlist:[],
                medicines:[],
                stocks:[],
                precourseappoint:[],
                start_times:[],
                end_times:[],
                rooms:[],
                therapists:'',
                icinvocied:'',
                ccinvocied:'',
                cformcount:'',
                aform: new Form({
                        appointment_id:'',
                        user_id:'',
                        showdiscount:'',
                        insurance_id:'',
                        insured:'',
                        sub_total:'',
                        insured_deduction:0,
                        insured_discount:0,
                        insured_deduction_reason:0,
                        insured_discount_reason:0,
                        offered:'',
                        offered_type:'',
                        offered_reason:'',
                        offered_discount:'',
                        offered_value:0,
                        tax_enabled:false,
                        tax_value:0,
                        total:'',
                        coinsurance:0,
                        coinsurance_value:0,
                        remark:'',
                        payment_mode:'cash',
                        txn_id:'',
                        discount_id:'',
                        received:'',
                        returnable:'',
                        visa_amount:'0',
                        cash_amount:''
                }),
                cform: new Form({
                    active_appointment_id:'',
                    course__treatment_id:[],
                    course__treatment_name:[],
                    course__doctor_id:[],
                    course__start_timeslot:[],
                    course__end_timeslot:[],
                    course__date:[],
                    course__room_id:[],
                    course__status_id:[],
                    course__dchecked:[],
                    course__schecked:[],
                    course__echecked:[],
                }),
                ccdform: new Form({
                    cd:'',
                    appointment_code:'',
                    insurance_id:'',
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return id;
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return '('+diffDuration.years()+' yrs)';
            },
            getAllAssets() {
                axios.get('/api/getOeCategoriesSelectList').then((data) => {this.oecategories = data.data }).catch();
                axios.get('/api/getDiseasesSelectList').then((data) => {this.diseases = data.data }).catch();
                axios.get('/api/getTreatmentsSelectList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getSymptomsSelectList').then((data) => {this.symptoms = data.data }).catch();
                axios.get('/api/getXTestsSelectList').then((data) => { this.xtests = data.data }).catch();
                axios.get('/api/getDTestsSelectList').then((data) => { this.dtests = data.data }).catch();
            },
            removeADiscount() {
                this.aform.discount_id = '';
                this.aform.offered_value = 0;
                this.aform.offered_reason = '';
                this.reTotal();
            },
            showAppointment() {
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
                this.$Progress.start();
                axios.get('/api/appointments/view/'+this.activeID).then(({ data }) => {
                    this.appointment = data[0];

                    this.icinvocied = data[0]['iinvoice'];

                    this.ccinvocied = data[0]['cinvoice'];

                    axios.get('/api/customers/'+this.appointment.user_id).then((data) => {
                        this.customer = data.data[0];
                      
                        this.aform.appointment_id = this.activeID;
                        this.aform.user_id =  data.data[0].id;
                        this.aform.insurance_id =  data.data[0].insurance_id;
                        if(this.appointment.appointment_type_id == 1) {
                            axios.post('/api/get-consult-mapped-insurances', {
                                insurance_id: data.data[0].insurance_id,
                                treatment_id: this.appointment.treatment_id
                            })
                            .then((response) => {
                                this.insurance = response.data;
                            });
                        } else if(this.appointment.appointment_type_id == 2) {
                            axios.post('/api/get-treatment-mapped-insurances', {
                                insurance_id: data.data[0].insurance_id,
                                treatment_id: this.appointment.treatment_id
                            })
                            .then((response) => {
                                this.insurance = response.data;
                            });
                        }
                    }).catch();
                    axios.get('/api/treatments/'+this.appointment.treatment_id).then((data) => {this.treatment = data.data}).catch();
                });
                axios.get('/api/getOptionsList').then(({ data }) => {
                    this.options = data;
                //    this.$Progress.finish();
                });
                this.$Progress.finish();
            },
            getAppointmentHistory() {
                this.$Progress.start();
                axios.get('/api/appointments/user-history/'+this.$route.params.id).then(({ data }) => {
                    //console.log(data);
                    this.appointment_histories = data;
                    this.$Progress.finish();
                });
            },
            calculate(){
                let tc, nt, rc;
                if(this.aform.visa_amount >= 1){
                    tc = this.makeNumber(this.aform.total) - this.makeNumber(this.aform.visa_amount);
                } else {
                    tc = this.makeNumber(this.aform.total);
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
                this.aform.returnable = returnable.toFixed(2);

            },
            reTotal(){
                this.aform.returnable = ''; this.aform.received = '';
                this.aform.sub_total = this.makeNumber(this.treatment.cost).toFixed(2);
                let total = this.aform.sub_total;
                if(this.aform.discount_id >= 1) {
                    this.addOffers();
                    total = this.makeNumber(total) - this.makeNumber(this.aform.offered_value);  
                }
                if(this.aform.tax_enabled) {
                    total = this.makeNumber(total) + this.makeNumber(this.aform.tax_value);
                }
                this.aform.total = (this.makeNumber(total) >= 0) ? this.makeNumber(total).toFixed(2) : 0;
            },
            addOffers() {
                let offer = ''; let reason = '';
                if(this.aform.discount_id >= 1) {
                    let adiscount = this.discounts[this.aform.discount_id];
                    if(adiscount.value.indexOf('%') >= 1) {
                        reason = adiscount.title;
                        offer = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(adiscount.value.replace('%', ''))/100).toFixed(2);
                    }
                    else {
                        reason = adiscount.title;
                        offer = this.makeNumber(adiscount.value).toFixed(2);
                    }
                    this.aform.offered_reason = reason;
                    this.aform.offered_value = offer;
                } else {
                    this.aform.offered_value = 0;
                    this.aform.offered_reason = '';
                }

                if(this.aform.insured == 1) {
                    this.aform.offered_value = 0;
                    this.aform.offered_reason = '';
                }
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            },

            setInvoice() {
                if(this.appointment.visit_type_id == 2) {
                    this.treatment.cost = 0;
                }
                this.reTotal();
                if(this.appointment.appointment_type_id == 1) {
                    this.getDiscounts('c');
                }
                else if(this.appointment.appointment_type_id == 2) {
                    this.getDiscounts('t');
                }

                $('#setInvocieModal').modal('show');
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
                if(this.appointment.appointment_type_id == 1) {
                    if(this.customer.date_of_birth === null) {
                        swal.fire({
                            type: 'error',
                            title: 'Missing Date Of Birth',
                            html: 'Please update Date of birth before setting up invoice. <br> Please Refresh the page if updated.',
                            confirmButtonText: 'Refresh The Page',
                            cancelButtonText: 'Ok',
                            showCancelButton: true,
                            confirmButtonColor: '#151E5F',
                            cancelButtonColor: '#e3342f',
                            footer: '<a href="/customers/edit/'+this.customer.id+'" target="_blank">Click here To Update Date Of Birth</a>'
                            }).then((result) => {
                                if (result.value) {
                                    location.reload();
                                };
                            });
                        return false;
                    }
                }
                this.loader = 4;
                this.aform.post('/api/invoices/create')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        title: 'Invoice has been generated successfully',
                        type: 'success',
                        }).then((result) => {
                            this.showAppointment()
                            $('#setInvocieModal').modal('hide');
                            this.loader = false;
                            let route = this.$router.resolve({path: '/invoices/print/'+data.data.invoice});
                            window.open(route.href, '_blank');
                        });

                    this.$Progress.finish();
                })
                .catch(() => {
                    this.loader = false;
                });
            },
            getTimings(ckey){
                this.cform.course__dchecked[ckey] = '';
                let d,l,t;
                d = this.cform.course__date[ckey];
                t = this.cform.course__doctor_id[ckey];
                l = this.appointment.location_id;
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+d+'&at=2&lid='+l+'&did='+t).then(({ data }) => { this.start_times[ckey] = data['start_slots']; });
            },
            getClosings(ckey){
                this.cform.course__schecked[ckey] = '';
                let d,l,t,s;
                d = this.cform.course__date[ckey];
                t = this.cform.course__doctor_id[ckey];
                s = this.cform.course__start_timeslot[ckey];
                l = this.appointment.location_id;
                axios.get('/api/appointments/get-end-timings?q='+d+'&at=2&lid='+l+'&did='+t+'&st='+s).then(({ data }) => {
                    this.end_times[ckey] = data;
                });
            },
            getRooms(ckey){
                this.cform.course__echecked[ckey] = '';
                let d,l,t,e,s;
                d = this.cform.course__date[ckey];
                t = this.cform.course__doctor_id[ckey];
                s = this.cform.course__start_timeslot[ckey];
                e = this.cform.course__end_timeslot[ckey];
                l = this.appointment.location_id;
                axios.get('/api/appointments/get-rooms?q='+d+'&lid='+l+'&st='+s+'&et='+t).then(({ data }) => {
                    this.rooms[ckey] = data;
                });
            },
            CourseAppointments() {
                axios.get('/api/getOnlyTherapistsList').then((data) => {  this.therapists = data.data }).catch();
                if(this.appointment.therapies) {
                    this.precourseappoint = JSON.parse(this.appointment.therapies);
                }
                $('#setCourseModel').modal('show');
            },
            createCourse() {
                swal.fire({
                    title: 'Create Course',
                    text: 'Are you sure, you want to create a course respective to this apppointment?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#151E5F',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Create It Now',
                    cancelButtonText: 'No',
                    showLoaderOnConfirm: true,
                }).then((result) => {
                    if (result.value) {
                        if(this.insurance.cash_discount == 1) { this.ccdform.cd = 1; }
                        else { this.ccdform.cd = 0; }

                        if(this.appointment.therapies) {
                            let ary = JSON.parse(this.appointment.therapies);
                            let ft = this.cform;
                            let arrkey = 0;
                            let cformcount = []; let index = 0;
                            ary.forEach(function(element, mkey) {
                                for (index = 0; index < element.days; index++) {
                                    ft.course__treatment_id[parseInt(arrkey)+parseInt(index)] = element.id;
                                    ft.course__treatment_name[parseInt(arrkey)+parseInt(index)] = element.name;
                                    cformcount.push(parseInt(arrkey)+parseInt(index));
                                }
                                arrkey = parseInt(arrkey)+parseInt(index);
                                //console.log(arrkey);
                            });
                            this.cformcount = cformcount;
                            this.cform.active_appointment_id = this.activeID;
                            this.CourseAppointments();
                        } else {
                            swal.fire({
                                title: 'No Suggested Treatments',
                                text: "Doctor has not prescribed any therapy.",
                                type: 'info',
                            });
                        }
                    }
                });
            },
            getDiscounts(type) {
                axios.post('/api/get-filtered-discounts', {
                    date: this.appointment.date,
                    type: type,
                    time_slots:this.appointment.start_timeslot,
                })
                .then((response) => {
                    this.discounts = response.data;
                });
            },
            submitCourse() {
                this.loader = 3;
                this.cform.post('/api/courses/create-course')
                .then((data) => {
                    swal.fire({
                        title: 'Course Created',
                        text: "Course has been created. Do you want to view course or stay on the page ?",
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#151E5F',
                        cancelButtonColor: '#06A23F',
                        confirmButtonText: 'View Course',
                        cancelButtonText: 'Stay On this Page'
                    }).then((result) => {
                        $('#setCourseModel').modal('hide');
                        this.loader = false;
                        let route = this.$router.resolve({path: '/appointments/courses/view/'+data.data});
                        window.open(route.href, '_blank');
                    });
                })
                .catch((response) => {
                    this.loader = false;
                });
            },
            MoveInCourse() {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You want to shift this appointment under Course.",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'off', placeholder: 'Enter Course ID'  },
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Shift it!',
                    cancelButtonText: 'Not Now',
                     showLoaderOnConfirm: true,
                    preConfirm: (cid) => {
                       return axios.post('/api/appointments/shift-in-course', {
                            course_code: cid,
                            user_id:this.appointment.user_id,
                            apid:this.activeID
                        })
                        .then(response => {
                            if(response.data.status == 'error') {
                                throw new Error(response.data.message);
                            } else {
                                this.showAppointment();
                                swal.fire('Shifted!', 'Course has been moved in prescribed courses successfully. Please check there.', 'success');
                            }
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            },
            setID() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
            },
            enableTax() {
                this.aform.tax_enabled = true;
                this.aform.tax_value = 0;
                let txp;
                if(this.appointment.appointment_type_id == 1) {
                    txp = this.config.consultaion_tax;
                }
                else if(this.appointment.appointment_type_id == 2) {
                    txp = this.config.treatment_tax;
                }
                this.aform.tax_value = this.makeNumber(this.makeNumber(this.aform.total)*this.makeNumber(txp.replace('%', ''))/100).toFixed(2);
                this.reTotal();
            },
            disableTax() {
                this.aform.tax_enabled = false;
                this.aform.tax_value = 0;
                this.reTotal();
            }
        },
        beforeMount() {
            this.setID();
        },
        mounted() {
            this.showAppointment();
            this.getAllAssets();
        }

    }
</script>
