<template>
    <div>
         <div class="content">
            <div class="container-fluid">
                <div class="py-2">
                    <router-link target="_blank" class="btn btn-sm btn-success" :to="'/appointments/courses/print-acknowledge/'+course.course_code">Print Acknowledgement</router-link>
                    <span class="float-right">
                        <a class="btn btn-sm btn-outline-dark" :href="'/appointments/courses/view/'+course.course_code">Back To Course View</a>
                    </span>
                </div>
                <div class="p-b-25">
                    <div class="card">
                        <form @submit.prevent="createInvoice">
                            <div class="card-header">
                                <h5 class="card-title">Course Cash Invoice</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row p-b-10 m-l-0 m-r-0">
                                    <div class="col-4">
                                        <p class="bottom-bordered">
                                            <b>Course ID :</b> {{ course.course_code }}
                                        </p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="bottom-bordered">
                                            <b>Patient NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}
                                        </p>
                                    </div>
                                    <div class="col-4 text-right">
                                        <p class="bottom-bordered m-0">
                                            <b>REFERENCE :</b> {{ course.doctor | capitalize}}
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-bordered table-striped text-left m-0">
                                            <thead>
                                                <tr>
                                                    <th style="width:70px; text-align:left">SNo</th>
                                                    <th class="desc">TREATMENT</th>
                                                    <th class="text-right">Price (RO)</th>
                                                    <th class="text-right">Count</th>
                                                    <th class="text-right wf-120">Total (RO)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(treatment, key) in treatments" :key="key">
                                                    <td>{{ key+1 }}</td>
                                                    <td>{{ treatment.treatment }} Sessions</td>
                                                    <td style="text-align:right">{{ treatment.cost | formatOMR }}</td>
                                                    <td style="text-align:right">{{ treatment.qty }}</td>
                                                    <td style="text-align:right">{{ treatment.total | formatOMR }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-right text-uppercase">Sub Total</th>
                                                <td style="width: 120px;">
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
                                                <td style="width: 120px;"><input type="text" v-model="aform.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('offered_value') }"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-right text-uppercase">Net Payable</th>
                                                <td style="width: 120px;"><input type="text" v-model="aform.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': aform.errors.has('total') }"></td>
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
                                        <span>
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
                invoice:'',
                 loader: false,
                loaderurl: '/svg/loop.gif',
                calcmsg:'',
                activeID: '',
                active_location: '',
                course:'',
                treatments:'',
                total:'',
                sub_total:'',
                discount:'',
                discount_value:'',
                discounts:'',
                customer:'',
                aform: new Form({
                    type:6,
                    invoice_date:'',
                    total:'',
                    payment_mode:'cash',
                    txn_id:'',
                    remark:'',
                    returnable:'',
                    received:'',
                    course_code:'',
                    user_id:'',
                    invoice_number:'',
                    insurance_id:1,
                    insured:0,
                    sub_total:'',
                    insured_deduction:0,
                    offered:0,
                    offered_reason:'',
                    offered_value:0,
                    cash_amount:'',
                    visa_amount:'',
                    payment_type:3,
                    listlen:'',
                    rawdata:'',
                    paying_now:''
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
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
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
            checkDiscount() {
                let listlen = this.aform.listlen;
                if(listlen >= 5) {
                    this.discount = this.discounts[1];
                } else if((listlen == 3) || (listlen == 4)) {
                    this.discount = this.discounts[0];
                } else {
                    this.aform.offered = 0;
                    this.discount = {'title':'No discount available', 'value':0};
                }
            },
            removeADiscount() {
                this.aform.discount_id = '';
                this.aform.offered_value = 0;
                this.aform.offered = 0;
                this.aform.offered_reason = '';
                this.aform.paying_now = this.aform.total;
                this.aform.payment_type = 3;
                this.aform.cash_amount = '';
                this.aform.visa_amount = '';
                this.reTotal();
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
            getCCashVisa() {
                let visa_amount = this.makeNumber(this.aform.paying_now) - this.makeNumber(this.aform.cash_amount);
                this.aform.visa_amount = visa_amount.toFixed(3);
            },
            getInvoice() {
                this.$Progress.start();
                let $aform = this.aform;
                axios.get('/api/courses/'+this.activeID).then((cdata) => {
                    this.course = cdata.data;
                    $aform.invoice_number = cdata.data.primary_invoice;
                    $aform.course_code = cdata.data.course_code;
                    axios.post('/api/get-cash-invoicing-treatments', {
                        appointment_code:cdata.data.consult_code,
                        invoice_number:cdata.data.invoice
                    }).then((data2) => {
                        this.treatments =  data2.data.treatments;
                        this.total =  data2.data.total;
                        this.sub_total =  data2.data.sub_total;
                        let subtotal = 0;
                        data2.data.treatments.forEach((element, key) => {
                            subtotal = this.makeNumber(subtotal) + this.makeNumber(element.total);
                        });
                        $aform.listlen = data2.data.treatments_count;
                        $aform.sub_total = subtotal;
                        $aform.total = subtotal;
                        $aform.paying_now = subtotal;
                        $aform.rawdata = data2.data.treatments;
                        this.checkDiscount();
                    }).catch();
                    axios.get('/api/customers/'+cdata.data.user_id).then((udata) => {this.customer = udata.data[0]; }).catch();
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
            setInvoice() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
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
                else if(this.aform.invoice_date == '') {
                    swal.fire('Missing Values', 'Please enter invoice date.', 'error');
                        return false;
                }
                this.loader = 1;
                this.aform.post('/api/invoices/update-cash-course-invoice')
                .then((data) => {
                    this.loader = false;
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print Invoice',
                        cancelButtonText: 'Stay On The Page',
                    }).then((result) => {
                        if (result.value) {
                            let route = this.$router.resolve({path: '/invoices/print-course-invoice/'+data.data.invoice});
                            window.open(route.href, '_blank');
                        }
                    }).catch(error => {
                        this.loader = false;
                        swal.showValidationMessage(`Request failed: ${error}`);
                    });
                })
                .catch(error => {
                    this.loader = false;
                    swal.fire(`Request failed: ${error.message}`);
                });
            },
        },
        beforeMount() {
            this.setInvoice();
            axios.get('/api/get-course-discounts').then(response => { this.discounts = response.data; });
        },
        mounted() {
            this.getInvoice();
        }
    }
</script>
