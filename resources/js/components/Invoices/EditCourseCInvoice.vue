<template>
    <div>
         <div class="content">
            <div class="container-fluid">
                <div class="py-1">
                    <div class="button-group">
                        <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/courses/view/'+invoice.course_code">View Course </a>
                        <span class="float-right">
                            <a class="btn btn-sm btn-outline-secondary" :href="'/invoices/appointments/'+currentYear">back To List </a>
                        </span>
                    </div>
                </div>
                <div class="p-b-25">
                    <div class="card">
                        <form @submit.prevent="updateInvoice">
                            <div class="card-header">
                                <h5 class="card-title">Update Invoice</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row p-b-10 m-l-0 m-r-0 detailbox">
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <p class="m-0 text-right" v-show="(insurance.discount) && (aform.insured == 1)">
                                            <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="aform.showdiscount">
                                                Show Discount [<b>{{ insurance.name }}</b> -- (Discount: <b>{{ insurance.discount }}</b>)]
                                            </p-check>
                                        </p>
                                    </div>
                                </div>
                                <div class="row m-b-20">
                                    <div class="col-12">
                                        <table class="table table-striped table-bordered m-b-15">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th style="width: 130px;">Appointment ID</th>
                                                    <th>Description</th>
                                                    <th style="width: 100px;">Fees</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ invoice.appointment_id }}</td>
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
                                            <div class="col-8">
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
                                                    <p v-if="appointment.appointment_type_id == 1">
                                                        <span v-if="insurance.name">
                                                            <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="aform.insured" @change="reTotal">
                                                            <b>{{ insurance.name }}</b> (Deductable: <b>{{ (customer.consult_deductable)?customer.consult_deductable:'NULL' }}</b>) (Co-Insurance: <b>{{ customer.co_insurance }}</b>)</p-check>
                                                            <span class="alert-danger p-l-5 p-r-5" v-show="expirynote">{{ expirynote }}</span>
                                                        </span>
                                                        <span v-else class="text-danger">
                                                            <i>{{customer.insurancer}} does not cover this consultation.</i>
                                                        </span>
                                                    </p>
                                                    <p v-else-if="appointment.appointment_type_id == 2">
                                                        <span v-show="insurance.name">
                                                            <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="aform.insured" @change="reTotal">
                                                            <b>{{ insurance.name }}</b> (Co-Insurance: {{ customer.co_insurance }})</p-check>
                                                            <span class="alert-danger p-l-5 p-r-5" v-show="expirynote">{{ expirynote }}</span>
                                                        </span>
                                                        <!-- <span v-else class="text-danger">
                                                            <i>{{customer.insurancer}} does not cover this treatment.</i>
                                                        </span> -->
                                                    </p>
                                                    <p v-else>
                                                        No Insurancer details
                                                    </p>
                                                </span>
                                            </div>
                                            <div class="col-4">
                                                <label class="control-label">Discount by SST

                                                </label> <button @click="removeADiscount" type="button" class="btn btn-sm btn-danger float-right"> <i class="fas fa-times"></i> </button><hr class="m-b-5 m-t-5">
                                                <span v-if="aform.insured == 1">
                                                    <em class="text-danger">Discount can not be applied with insurance.</em>
                                                </span>
                                                <span v-else-if="discounts">
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
                                                        <span v-if="aform.insured == 1">
                                                            <span v-if="aform.showdiscount == 1">
                                                                <input value="0" type="text" v-model="aform.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('sub_total') }">
                                                            </span>
                                                            <span v-else>
                                                                <input :value="(aform.sub_total - aform.insured_discount)" type="text" readonly="readonly" class="form-control">
                                                                <input value="0" type="hidden" v-model="aform.sub_total" name="sub_total" id="sub_total">
                                                            </span>
                                                        </span>
                                                        <span v-else>
                                                            <input :value="aform.sub_total" type="text" readonly="readonly" class="form-control">
                                                            <input value="0" type="hidden" v-model="aform.sub_total" name="sub_total" id="sub_total">
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered m-b-0" v-show="aform.discount_id >= 1">
                                            <tbody>
                                                <tr>
                                                    <th class="text-right text-uppercase">{{ aform.offered_reason }}</th>
                                                    <td style="width: 100px;"><input value="0" type="text" v-model="aform.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('offered_value') }"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered m-b-0" v-show="aform.insured == 1">
                                            <tbody>
                                                <tr v-show="aform.showdiscount == 1">
                                                    <th class="text-right text-uppercase">Insurance Discount</th>
                                                    <td style="width: 100px;"><input value="0" type="text" v-model="aform.insured_discount" name="insured_discount" id="insured_discount" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('insured_discount') }"></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right text-uppercase">Payable by Insurance</th>
                                                    <td style="width: 100px;"><input value="0" type="text" v-model="aform.insured_deduction" name="insured_deduction" id="insured_deduction" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('insured_deduction') }"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered m-b-15">
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
                                                <div v-show="(aform.payment_mode == 'cash+visa')" class="m-b-15 row">
                                                    <div class="col-md-6">
                                                        <label for="visa_amount" class="control-label">Paid BY Visa</label>
                                                        <input type="text" class="form-control" :class="{ 'is-invalid': aform.errors.has('visa_amount') }" v-model="aform.visa_amount" name="visa_amount" @keyup="calculateCash">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="cash_amount" class="control-label">Paid BY Cash</label>
                                                        <input type="text" class="form-control" :class="{ 'is-invalid': aform.errors.has('cash_amount') }" v-model="aform.cash_amount" name="cash_amount" readonly>
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


                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <span class="mydatepicker  m-r-10">
                                    <date-picker v-model="aform.invoice_date" lang="en" type="datetime" format="YYYY-MM-DD HH:mm:ss" confirm></date-picker>
                                </span>
                                <span v-if="loader">
                                    <img class="wf-50" :src="loaderurl" alt="updating">
                                </span>
                                <span v-else>
                                    <button type="submit" class="btn btn-wide btn-success"> Update Invoice </button>
                                </span>
                            </div>
                        </form>
                    </div>
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
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: '',
                loader: false,
                loaderurl: '/svg/loop.gif',
                active_location: '',
                appointment:'',
                customer: {},
                insurance:'',
                treatment:'',
                medicines:[],
                profile:'',
                invoice:'',
                discounts:'',
                calcmsg:'',
                aform: new Form({
                        appointment_id:'',
                        user_id:'',
                        invoice_number:'',
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
                        total:'',
                        coinsurance:0,
                        coinsurance_value:0,
                        remark:'',
                        payment_mode:'cash',
                        txn_id:'',
                        discount_id:'',
                        received:'',
                        returnable:'',
                        invoice_date:'',
                        visa_amount:'0',
                        cash_amount:''
                    }),
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            printpage() {
                this.$htmlToPaper('printDiv');
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
            getInvoice() {
                this.$Progress.start();
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    this.aform.fill(data);
                    this.aform.sub_total = this.makeNumber(data.amount);
                    this.aform.total = this.makeNumber(data.payable_amount);
                    this.aform.insured_discount = this.makeNumber(data.ins_disc_value);
                    this.aform.offered_discount = this.makeNumber(data.bliss_discount_value);
                    this.aform.insured_deduction = this.makeNumber(data.inc_deduction_value);
                    if(data.inc_deduction_value > 0){
                        this.aform.insured = 1;
                    }
                    axios.get('/api/appointments/view/'+data.appointment_id).then(({ data }) => {
                        this.appointment = data[0];
                        axios.get('/api/customers/'+this.appointment.user_id).then((data) => {
                            this.customer = data.data[0];
                            if(this.appointment.appointment_type_id == 1) {
                                this.getDiscounts('c');
                                axios.post('/api/get-consult-mapped-insurances', {
                                    insurance_id: data.data[0].insurance_id,
                                    treatment_id: this.appointment.treatment_id
                                })
                                .then((response) => {
                                    this.insurance = response.data;
                                });
                            } else if(this.appointment.appointment_type_id == 2) {
                                this.getDiscounts('t');
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
                    this.$Progress.finish();
                });
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return diffDuration.years()+' yrs';
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
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
                this.aform.returnable = returnable.toFixed(3);

            },
            reTotal(){
                this.aform.returnable = ''; this.aform.received = '';
                if(this.aform.insured == 1) {
                    this.addInsurance();
                    this.aform.sub_total = this.makeNumber(this.treatment.cost).toFixed(3);
                    let insured_deduction = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount) - this.makeNumber(this.aform.total);
                    this.aform.insured_deduction = (this.makeNumber(insured_deduction) >= 0) ? this.makeNumber(insured_deduction).toFixed(3) : 0;
                } else if(this.aform.discount_id >= 1) {
                    this.aform.insured_deduction = '';
                    this.aform.insured_discount = '';
                    this.addOffers();
                    this.aform.sub_total = this.makeNumber(this.treatment.cost).toFixed(3);
                    let total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.offered_value);
                    this.aform.total = (this.makeNumber(total) >= 0) ? this.makeNumber(total).toFixed(3) : 0;
                } else {
                    this.aform.insured_deduction = '';
                    this.aform.insured_discount = '';
                    this.aform.sub_total =this.makeNumber(this.treatment.cost).toFixed(3);
                    this.aform.total = this.makeNumber(this.aform.sub_total).toFixed(3);
                }
            },
            hideDiscount() {

            },
            addInsurance() {
                this.aform.discount_id = ''; this.aform.offered_value = '';
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
                let offer = ''; let reason = '';
                if(this.aform.discount_id >= 1) {
                    let adiscount = this.discounts[this.aform.discount_id];
                    if(adiscount.value.indexOf('%') >= 1) {
                        reason = adiscount.title;
                        offer = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(adiscount.value.replace('%', ''))/100);
                    }
                    else {
                        reason = adiscount.title;
                        offer = this.makeNumber(adiscount.value);
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
            removeADiscount() {
                this.aform.discount_id = '';
                this.aform.offered_value = 0;
                this.aform.offered_reason = '';
                this.reTotal();
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            calculateCash() {
                if(this.aform.visa_amount > this.aform.total){
                    this.$toaster.error('Visa amount must be less than total payable');
                    this.aform.visa_amount = this.makeNumber(this.aform.total);
                }
                if(this.aform.payment_mode == 'cash+visa') {
                    this.aform.cash_amount = this.makeNumber(this.aform.total) - this.makeNumber(this.aform.visa_amount);
                }
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
                //console.log(this.discounts);
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            updateInvoice() {
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
                this.loader = true;
                this.aform.post('/api/invoices/modify')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print This Invoice',
                        cancelButtonText: 'Stay On This Page',
                        footer: '<a href="/invoices/appointments/'+this.currentYear+'">Go Back To List</a>'
                        }).then((result) => {
                            this.loader = false;
                            if(this.aform.insurance_id == 1) {
                                let route = this.$router.resolve({path: '/invoices/print-uinvoice/'+data.data.invoice});
                                window.open(route.href, '_blank');
                            } else {
                                let route = this.$router.resolve({path: '/invoices/print-iinvoice/'+data.data.invoice});
                                window.open(route.href, '_blank');
                            }
                        });
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.loader = false;
                });
            },
            setInvoice() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
            }
        },
        beforeMount() {
            this.setInvoice();
        },
        mounted() {
            this.getInvoice();
        }
    }
</script>
