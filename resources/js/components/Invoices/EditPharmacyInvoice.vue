<template>
    <div>
        <div class="content">
            <div class="container">
                <div class="py-1">
                    <div class="button-group">
                        <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/view/'+invoice.appointment_id">View Appointment </a>
                        <span class="float-right">
                            <a class="btn btn-sm btn-outline-secondary" :href="'/invoices/pharmacy/'+currentYear">back To List </a>
                        </span>
                    </div>
                </div>
                <div class="p-b-25">
                    <div class="card">
                        <form @submit.prevent="updatePharmacyInvoice()">
                            <div id="printDiv">
                                <section class="bodybox">
                                    <div class="container introbox m-t-30">
                                        <h2 class="m-b-30">Medicines Invoice
                                            <small class="float-right"><small> Invoice No: {{ invoice.invoice_number }} </small></small>
                                        </h2>
                                        <div class="row">
                                            <div class="col-4">
                                                <p class="bottom-bordered">
                                                    <b>PATIENT ID :</b> {{ customer.username }}
                                                </p>
                                            </div>
                                            <div class="col-4 text-center">
                                                <p class="bottom-bordered">
                                                    <b>NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}
                                                </p>
                                            </div>
                                            <div class="col-4 text-right">
                                                <p class="bottom-bordered">
                                                    <b>AGE :</b> {{ getAge(customer.date_of_birth) }}
                                                    <span v-if="customer.gender == 1"> (Male) </span>
                                                    <span v-else-if="customer.gender == 2"> (Female) </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <p class="bottom-bordered m-0">
                                                    <b>REFERENCE :</b> {{ appointment.doctor | capitalize}}
                                                </p>
                                            </div>
                                            <div class="col-4 text-center">
                                                <p class="bottom-bordered m-0" v-if="invoice.insurance_id > 1">
                                                    <b>Insured BY : {{ customer.insurancer | capitalize}}</b>
                                                </p>
                                                <p class="bottom-bordered m-0" v-else>
                                                    <b v-if="invoice.payment_mode == 'cc_dc'">PAID BY:  VISA CARD</b>
                                                    <b v-else>PAID BY : {{invoice.payment_mode }}</b>
                                                </p>

                                            </div>
                                            <div class="col-4 text-right">
                                                <p class="bottom-bordered m-0">
                                                    <b>DATE-TIME : {{ invoice.created_at | setfulldate }}</b>
                                                </p>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped text-left m-0">
                                            <thead>
                                                <tr>
                                                    <th class="sno">SNo</th>
                                                    <th class="desc">PARTICULARS</th>
                                                    <th>Unit Price (RO)</th>
                                                    <th class="total">Qty</th>
                                                    <th class="total">Return/Adjust</th>
                                                    <th class="total">Total (RO)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(med, key) in sale_medicines" :key="key">
                                                    <td class="sno">{{ key+1 }}</td>
                                                    <td class="desc">
                                                        <h6>{{ med.rowdata.name }}</h6>
                                                        <p class="m-0">Batch No: {{ med.rowdata.batch_no }}</p>
                                                    </td>
                                                    <td>{{ med.rowdata.cost | formatOMR }}</td>
                                                    <td class="total">{{ med.rowdata.qty }}</td>
                                                    <td class="total">
                                                        <input type="number" @change="reTotal" min="0" :max="form.medicine__qty[key]" class="form-control" v-model="form.medicine__rqty[key]"  >
                                                    </td>
                                                    <td class="total">{{ form.medicine__total[key] | formatOMR }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="unit">NET AMOUNT (RO):</td>
                                                    <td class="total"> {{ form.sub_total | formatOMR }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="no-break">
                                            <table class="grand-total m-b-5">
                                                <tbody>
                                                    <tr v-show="invoice.bliss_discount_value">
                                                        <td class="unit">{{ invoice.bliss_discount }}:</td>
                                                        <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.bliss_discount_value">
                                                        <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                                        <td class="total"> {{ (invoice.amount - invoice.bliss_discount_value)  | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.ins_disc_value != 0">
                                                        <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                                        <td class="total"> {{ (invoice.amount - invoice.ins_disc_value) | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;">
                                                        <td class="unit"><b>Paid By Customer </b>:</td>
                                                        <td class="total"> {{ invoice.payable_amount | formatOMR }}</td>
                                                    </tr>
                                                    <tr v-show="invoice.coinsurance_value != 0">
                                                        <td class="unit"><b>Payable By {{ customer.insurancer | capitalize}} </b>:</td>
                                                        <td class="total"> {{ invoice.coinsurance_value | formatOMR }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </section>
                                <section>
                                    <div class="row m-0">
                                        <div class="col-md-4">
                                            <div class="text-left"><strong>PAYMENT DETAILS</strong> (Paid By Customer)</div>
                                            <div class="notice text-left">
                                                <div class="pay"><span>Payment Method:</span> {{ invoice.payment_mode }}</div>
                                                <div class="pay"><span>Txn No:</span> {{ invoice.txn_id }}</div>
                                                    <div class="pay"><span>Created By:</span> {{ invoice.admin | capitalize }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="add-box">
                                                <div class="add-box-body">
                                                    <div class="form-group">
                                                        <label for="reason_type" class="control-label d-block text-left">Invoice Revision</label>
                                                        <select class="form-control" :class="{ 'is-invalid': form.errors.has('reason_type') }" v-model="form.reason_type" name="reason_type">
                                                            <option value="1">Admin Adjustment</option>
                                                            <option value="2">User Adjustment</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="reason" class="control-label d-block text-left">reason</label>
                                                        <textarea class="form-control" v-model="form.reason" :class="{ 'is-invalid': form.errors.has('reason') }" name="reason"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 p-t-20 text-right">
                                            <button type="button" class="btn btn-sm btn-dark" @click="reTotal"> Recalculate</button>
                                            <br>
                                            <div class="form-group m-t-20">
                                                <span class="mydatepicker">
                                                    <date-picker v-model="form.invoice_date" lang="en" type="datetime" format="YYYY-MM-DD HH:mm:ss" confirm></date-picker>
                                                </span>
                                            </div>
                                            <span v-if="loader">
                                                <img class="wf-50" :src="loaderurl" alt="updating">
                                            </span>
                                            <span v-else>
                                                <button type="submit" class="btn btn-wide btn-success"> Submit Modifications </button>
                                            </span>
                                            <br>
                                        </div>
                                    </div>
                                </section>
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
                svgurl: '/svg/',
                docurl: '/files/docs/',
                imgurl: '/images/',
                editmode: false,
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeYear: '',
                activeID: '',
                active_location: '',
                listSlots: [],
                profile:'',
                invoice:'',
                appointment:'',
                customer:'',
                sale:'',
                sale_medicines:'',
                countlist:[],
                form: new Form({
                    id:'',
                    sale_id:'',
                    invoice_number:'',
                    medicine__spid:[],
                    medicine__qty:[],
                    medicine__rqty:[],
                    medicine__stock_id:[],
                    medicine__cost:[],
                    medicine__total:[],
                    insured:'',
                    insurance_id:'',
                    sub_total:'',
                    visa_amount:'',
                    cash_amount:'',
                    total:'',
                    type:'',
                    coinsurance:0,
                    coinsurance_value:0,
                    remark:'',
                    payment_mode:'',
                    txn_id:'',
                    discount_id:'',
                    reason:'',
                    reason_type:'',
                    invoice_date:''
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            getProfile() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
                axios.get('/api/get-profile').then(response => { this.profile = response.data; });
            },
            getInvoice() {
                this.$Progress.start();
                let $frm = this.form;
                let $ths = this;
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    this.activeYear = '20'+data.invoice_number.substr(3, 2);
                    $frm.sub_total = data.amount;
                    $frm.insurance_id = data.insurance_id;
                    $frm.coinsurance_value = data.coinsurance_value;
                    $frm.total = data.payable_amount;
                    $frm.id = data.id;
                    $frm.invoice_number = data.invoice_number;
                    $frm.invoice_date = data.invoice_date;
                    $frm.type = data.invoice_type;
                    if(data.invoice_type == 3) {
                        $frm.insured = 1;
                    }
                    axios.get('/api/appointments/view/'+data.appointment_id).then(({ data }) => {
                        this.appointment = data[0];
                        axios.get('/api/customers/'+data[0].user_id)
                            .then((data) => {this.customer = data.data[0] })
                            .catch();
                    });
                    axios.get('/api/sales/view/'+data.invoice_number).then(({ data }) => {
                        this.sale = data;
                         $frm.sale_id = data.id;
                        axios.get('/api/sale-medicines/'+data.id)
                            .then((sdata) => {
                                this.sale_medicines = sdata.data;
                                sdata.data.forEach(function(element, i) {
                                    $frm.medicine__rqty.push(0);
                                    $frm.medicine__stock_id.push(element.stock_id);
                                    $frm.medicine__cost.push(element.price);
                                    $frm.medicine__total.push(element.price);
                                    $frm.medicine__qty.push(element.qty);
                                    $frm.medicine__spid.push(element.spid);
                                    $ths.countlist.push(i);
                                });
                            })
                            .catch();
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
            updatePharmacyInvoice(){
                this.form.post('/api/invoices/update-pharmacy-invoice')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        //footer: '<a href="/invoices/pharmacy/'+activeYear+'">View Pharmacy Invoices List</a>'
                    }).then((result) => {
                        this.$router.push('/invoices/print-pharmacy-uinvoice/'+this.activeID);
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            reTotal(){
                this.updateSubTotal(); this.addPhInsurance(); this.addPhDiscount();
                let total = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.coinsurance_value);
                this.form.total = this.makeNumber(total).toFixed(3);
            },
            addPhDiscount(){

            },
            isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            },
            addPhInsurance() {
                this.form.discount_id = ''; this.form.offered_value = '';
                if(this.form.insured == 1){
                    if(this.customer.co_insurance === null) {
                        this.form.total = 0.000;
                    }
                    else if(this.customer.co_insurance.indexOf('%') >= 1) {
                        this.form.total = (this.makeNumber(this.form.sub_total)*this.makeNumber(this.customer.co_insurance.replace('%', ''))/100).toFixed(3);
                    }
                    else if(this.customer.co_insurance >= 1) {
                        this.form.total = this.makeNumber(this.customer.co_insurance).toFixed(3);
                    }
                    else {
                        this.form.total = this.makeNumber(this.form.sub_total).toFixed(3);
                    }

                    this.form.coinsurance_value = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.total);
                    this.form.coinsurance_value = this.makeNumber(this.form.coinsurance_value).toFixed(3);

                } else {
                    this.form.coinsurance_value = 0;
                    this.form.coinsurance = '';
                }
            },
            addPhOffers() {
                let offer = ''; let reason = '';
                if(this.ciform.offered == 1){
                    if(this.options.bliss_hours == 1) {
                        if(this.options.bliss_hours_discount_type == 1) {
                            reason = 'Bliss Hours Discount - '+ this.options.bliss_hours_discount+' (fixed)';
                            offer = this.makeNumber(this.options.bliss_hours_discount);
                        } else if(this.options.bliss_hours_discount_type == 2) {
                            reason = 'Bliss Hours Discount - '+this.options.bliss_hours_discount+'%'+' (percent)';
                            offer =  this.makeNumber(this.makeNumber(this.ciform.sub_total)*this.makeNumber(this.options.bliss_hours_discount)/100);
                        } else {
                            reason = '';
                            offer = 0;
                        }
                        this.ciform.offered_reason = reason;
                        this.ciform.offered_value = offer;
                    } else {
                        this.ciform.offered_reason = '';
                        this.ciform.offered_value = 0;
                    }
                } else {
                    this.ciform.offered_reason = '';
                    this.ciform.offered_value = 0;
                }
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                let $form = this.form;
                counter.forEach(element => {
                    $form.medicine__total[element] = this.makeNumber($form.medicine__cost[element]) * (this.makeNumber($form.medicine__qty[element]) - this.makeNumber($form.medicine__rqty[element]));
                    stotal = stotal +  $form.medicine__total[element];
                });
                this.form.sub_total = stotal;
            },
        },
        beforeMount() {
            this.getProfile();
        },
        mounted() {
            this.getInvoice();
        }
    }
</script>
