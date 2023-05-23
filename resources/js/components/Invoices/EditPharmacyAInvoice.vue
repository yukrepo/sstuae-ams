<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="py-1">
                    <div class="button-group">
                        <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/view/'+invoice.appointment_id">View Appointment </a>
                        <span class="float-right">
                            <a class="btn btn-sm btn-outline-secondary" :href="'/invoices/pharmacy/'+currentYear">back To List </a>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
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
                                                        <th class="total">Total (RO)</th>
                                                        <th class="total">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(med, key) in sale_medicines" :key="'fxt'+key">
                                                        <td class="sno">{{ key+1 }}</td>
                                                        <td class="desc">
                                                            <h6>{{ med.rowdata.name }}
                                                                <small class="m-0">(Batch No: {{ med.rowdata.batch_no }}</small>
                                                            </h6>
                                                        </td>
                                                        <td class="text-right">{{ med.rowdata.cost | formatOMR }}</td>
                                                        <td class="total">{{ med.rowdata.qty }}</td>
                                                        <td class="total">{{ med.rowdata.qty*med.rowdata.cost | formatOMR }}</td>
                                                        <td class="total">-</td>
                                                    </tr>
                                                    <tr v-for="(counter, cID) in countlist" :key="counter" :id="'row'+cID">
                                                        <td>{{counter+sale_medicines.length+1}}</td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input v-model="form.medicine__name[getIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['name']" readonly="readonly">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input v-model="form.medicine__batch_no[getIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['batch_no']" readonly="readonly">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input v-model="form.medicine__stock[getIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['stock']" readonly="readonly">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__cost[getIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['cost']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__qty[getIndex(counter, cID)]" class="form-control" type="number" @change="reTotal" name="medicine[]['qty']">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__total[getIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['total']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <button type="button" @click="removeCiBar(cID)" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="unit">NET AMOUNT (RO):</td>
                                                        <td class="total"> {{ form.sub_total | formatOMR }}</td>
                                                        <td class="total"> {{ form.old_total | formatOMR }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <div class="no-break">
                                                <table class="grand-total m-b-5">
                                                    <tbody>
                                                        <tr v-show="invoice.bliss_discount_value > 0">
                                                            <td class="unit">{{ invoice.bliss_discount }}:</td>
                                                            <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                                        </tr>
                                                        <tr style="font-weight: 600; color: #333; font-size: 1em;"  >
                                                            <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                                            <td class="total"> {{ (form.total)  | formatOMR }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </section>
                                    <section>
                                        <div class="row m-0">
                                            <div class="col-md-4">
                                                <label for="payment_mode" class="control-label">Payment Mode</label>
                                                <select class="form-control" :class="{ 'is-invalid': form.errors.has('payment_mode') }" v-model="form.payment_mode" name="payment_mode">
                                                    <option value="cash">Cash</option>
                                                    <option value="credit">Credit</option>
                                                    <option value="epay">E-Payment</option>
                                                    <option value="visa">VISA Card</option>
                                                    <option value="cash+visa">Cash + VISA</option>
                                                </select>
                                                <div v-show="(form.payment_mode == 'visa') || (form.payment_mode == 'epay') || (form.payment_mode == 'cash+visa')" class=" m-t-10">
                                                    <label for="txn_id" class="control-label">Transaction Number</label>
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('txn_id') }" v-model="form.txn_id" name="txn_id">
                                                </div>
                                                <div class="p-b-10 p-t-10">
                                                    <label for="remark" class="control-label">Remark</label>
                                                    <textarea class="form-control" v-model="form.remark" name="remark"></textarea>
                                                </div>

                                            </div>
                                            <div class="col-md-4">
                                                <div v-show="(form.payment_mode == 'cash+visa')" class="m-b-15 row">
                                                    <div class="col-md-6">
                                                        <label for="visa_amount" class="control-label">Paid BY Visa</label>
                                                        <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('visa_amount') }" v-model="form.visa_amount" name="visa_amount" @keyup="calculateCash">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="cash_amount" class="control-label">Paid BY Cash</label>
                                                        <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('cash_amount') }" v-model="form.cash_amount" name="cash_amount" readonly>
                                                    </div>
                                                </div>
                                                 <table class="table table-bordered" v-show="(form.payment_mode == 'cash') || (form.payment_mode == 'cash+visa')">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="3" class="text-center text-uppercase">Calculate Returnable Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <label for="received">Received</label>
                                                                <input type="text" class="form-control" v-model="form.received" placeholder="enter received value">
                                                            </td>
                                                            <td> <label for="received">--</label><br>
                                                            <button class="btn btn-sm btn-dark" type="button" @click="calculate" >Calculate</button> </td>
                                                            <td>
                                                                <label for="received">Returnable</label>
                                                                <input type="text" class="form-control" v-model="form.returnable" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><i class="text-danger">{{ calcmsg}}</i></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                    <div class="col-md-3">
                        <div class="card">
                            <div class="add-box">
                                <form @submit.prevent="addMedicine()">
                                    <div class="add-box-header">
                                        <h5>Add Medicine</h5>
                                    </div>
                                    <div class="add-box-body">
                                        <div class="form-group">
                                            <label for="medicine_id" class="control-label">medicine</label>
                                            <model-select :options="medicines"
                                                        name="medicine_id"
                                                        id="medicine_id"
                                                        aria-required="true"
                                                        v-model="nmform.medicine_id"
                                                        placeholder="select medicine"
                                                        @input="onSelect" >
                                            </model-select>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock_id" class="control-label">Batch no</label>
                                            <model-select :options="stocks"
                                                        name="stock_id"
                                                        id="stock_id"
                                                        aria-required="true"
                                                        v-model="nmform.stock_id"
                                                        placeholder="select batch no">
                                            </model-select>
                                        </div>
                                    </div>
                                    <div class="add-box-footer text-right">
                                        <input type="hidden" v-model="nmform.type">
                                        <button type="submit" class="btn btn-sm btn-dark"> Add Now </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="confirmModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Add Medicine
                        </div>
                        <div class="modal-body text-center">
                            <h4 class="m-b-20 m-t-20">Medicine has been added successfully.</h4>
                            <button type="button" class="btn btn-lg btn-success m-b-20" @click="reTotal" data-dismiss="modal"> OK ! </button>
                        </div>
                        <div class="modal-footer text-center d-block">
                            <i class="fas fa-check text-dark"></i>
                        </div>
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
                loader: false,
                loaderurl: '/svg/loop.gif',
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
                medicines:[],
                stocks:[],
                calcmsg:'',
                form: new Form({
                    id:'',
                    sale_id:'',
                    invoice_number:'',
                    medicine__qty:[],
                    medicine__batch_no:[],
                    medicine__stock_id:[],
                    medicine__cost:[],
                    medicine__stock:[],
                    medicine__name:[],
                    medicine__id:[],
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
                    reason_type:'2',
                    invoice_date:'',
                    received:'',
                    returnable:'',
                    invoice_date:'',
                    visa_amount:'0',
                    cash_amount:'',
                    old_total:0
                }),
                nmform: new Form({
                    medicine_id:'',
                    stock_id:'',
                    type:''
                }),
            }
        },
        methods: {
            getIndex(list, id) {
                return id;
            },
            getMedicinesList() {
                this.nmform.type = 2;
                axios.get('/api/getMedicinesSelectList').then((data) => {this.medicines = data.data }).catch();
            },
            getProfile() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
                axios.get('/api/get-profile').then(response => { this.profile = response.data; });
            },
            onSelect (product) {
               axios.get('/api/stocks/get-list-by-medicine/'+product)
                    .then((data) => {
                        if(data.data){
                            this.stocks = data.data;
                            //this.rePhTotal();
                        }
                    });
            },
            getInvoice() {
                this.$Progress.start();
                let $frm = this.form;
                let $ths = this;
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    this.activeYear = '20'+data.invoice_number.substr(3, 2);
                    $frm.sub_total = data.amount;
                    $frm.old_total = data.amount;
                    $frm.insurance_id = 1;
                    $frm.total = data.payable_amount;
                    $frm.id = data.id;
                    $frm.invoice_number = data.invoice_number;
                    $frm.payment_mode = data.payment_mode;
                    $frm.invoice_date = data.invoice_date;
                    $frm.received = data.received;
                    $frm.remark = data.remark;
                    $frm.returnable = data.returnable;
                    $frm.visa_amount = data.visa_amount;
                    $frm.cash_amount = data.cash_amount;
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
            calculate(){
                let tc, nt, rc;
                if(this.form.visa_amount >= 1){
                    tc = this.makeNumber(this.form.total) - this.makeNumber(this.form.visa_amount);
                } else {
                    tc = this.makeNumber(this.form.total);
                }
                if(this.form.received < tc) {
                    this.calcmsg = 'Received amount must be greater than or equal to payable by customer in cash.';
                    this.form.received = '';
                    this.form.returnable = '';
                    return;
                }
                else {
                    this.calcmsg = '';
                }
                let returnable = this.makeNumber(this.form.received) - tc;
                this.form.returnable = returnable.toFixed(3);

            },
            calculateCash() {
                if(this.form.visa_amount > this.form.total){
                    this.$toaster.error('Visa amount must be less than total payable');
                    this.form.visa_amount = this.makeNumber(this.form.total);
                }
                if(this.form.payment_mode == 'cash+visa') {
                    this.form.cash_amount = this.makeNumber(this.form.total) - this.makeNumber(this.form.visa_amount);
                }
            },
            updatePharmacyInvoice(){
                 if(this.form.payment_mode == 'visa') {
                    if((this.form.txn_id === null) || (this.form.txn_id === '') || (this.form.txn_id === 0)) {
                        swal.fire('Missing Values', 'Transaction number is required for VISA payment mode.', 'error');
                        return false;
                    }
                }
                else if(this.form.payment_mode == 'epay') {
                    if((this.form.txn_id === null) || (this.form.txn_id === '') || (this.form.txn_id === 0) || (this.form.remark === '')) {
                        swal.fire('Missing Values', 'Transaction number and Remark are required for E-Payment mode.', 'error');
                        return false;
                    }
                }
                else if(this.form.payment_mode == 'credit') {
                    if(this.form.remark === '') {
                        swal.fire('Missing Values', 'Remark is required for Credit Payment mode.', 'error');
                        return false;
                    }
                }
                else if(this.form.payment_mode == 'cash+visa') {
                    if((this.form.txn_id === null) || (this.form.visa_amount <= 0)) {
                        swal.fire('Missing Values', 'Transaction number and amount paid by VISA is required for this payment mode.', 'error');
                        return false;
                    }
                }
                this.loader = true;
                this.form.post('/api/invoices/update-inpharmacy-invoice')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        footer: '<a href="/invoices/pharmacy/'+this.activeYear+'">View Pharmacy Invoices List</a>'
                    }).then((result) => {
                        this.loader = false;
                        let route = this.$router.resolve({path: '/invoices/print-pharmacy-uinvoice/'+this.activeID});
                        window.open(route.href, '_blank');
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            reTotal(){
                let counter = this.countlist;
                let stotal = 0; let total = 0; let oldtotal = this.makeNumber(this.form.old_total);
                counter.forEach((key, element) => {
                    this.form.medicine__total[element] = this.makeNumber(this.form.medicine__cost[element]) * this.makeNumber(this.form.medicine__qty[element]);
                    stotal = stotal +  this.form.medicine__total[element];
                });
                stotal = stotal + oldtotal;
                this.form.sub_total =  stotal.toFixed(3);
                this.form.total = stotal.toFixed(3);
            },
            isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            },
            addPhOffers() {
                let offer = ''; let reason = '';
                if(this.form.offered == 1){
                    if(this.options.bliss_hours == 1) {
                        if(this.options.bliss_hours_discount_type == 1) {
                            reason = 'Bliss Hours Discount - '+ this.options.bliss_hours_discount+' (fixed)';
                            offer = this.makeNumber(this.options.bliss_hours_discount);
                        } else if(this.options.bliss_hours_discount_type == 2) {
                            reason = 'Bliss Hours Discount - '+this.options.bliss_hours_discount+'%'+' (percent)';
                            offer =  this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.options.bliss_hours_discount)/100);
                        } else {
                            reason = '';
                            offer = 0;
                        }
                        this.form.offered_reason = reason;
                        this.form.offered_value = offer;
                    } else {
                        this.form.offered_reason = '';
                        this.form.offered_value = 0;
                    }
                } else {
                    this.form.offered_reason = '';
                    this.form.offered_value = 0;
                }
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0; let oldtotal = this.makeNumber(this.form.old_total);
                counter.forEach((key, element) => {
                    this.form.medicine__total[element] = this.makeNumber(this.form.medicine__cost[element]) * this.makeNumber(this.form.medicine__qty[element]);
                    stotal = stotal +  this.form.medicine__total[element];
                });
                stotal = stotal + oldtotal;
                this.form.sub_total =  stotal.toFixed(3);
            },
            addMedicine() {
                let pkey = 0;
                if((this.nmform.medicine_id == '') || (this.nmform.stock_id == '')){
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Please select the medicine and batch no.',
                        timer: 2500
                    });
                    return false;
                }
                if(this.countlist.length == 0) {
                    pkey = 0;
                } else if(this.countlist.length == 1) {
                    pkey = 1;
                } else if(this.countlist.length > 1) {
                    pkey = this.countlist[this.countlist.length-1] + 1;
                }
                this.countlist.push(pkey);
                this.form.medicine__stock_id[pkey] = this.nmform.stock_id;
                this.form.medicine__id[pkey] = this.nmform.medicine_id;
                axios.get('/api/stocks/stock-by-id/'+this.nmform.stock_id)
                .then((edata) => {
                    this.form.medicine__qty[pkey] = 1;
                    this.form.medicine__batch_no[pkey] = edata.data.batch_no+' ('+ moment(edata.data.exp_date).format('DD.MM.YYYY')+')';
                    this.form.medicine__cost[pkey] = edata.data.cash_price;
                    this.form.medicine__stock[pkey] = edata.data.stock;
                    this.form.medicine__name[pkey] = edata.data.name;
                });
                this.nmform.stock_id = '';
                this.nmform.medicine_id = '';
                this.reTotal();
                $('#confirmModal').modal('show');
            },
            removeCiBar(barkey){
                this.countlist.splice(barkey, 1);
                this.form.medicine__qty.splice(barkey, 1);
                this.form.medicine__batch_no.splice(barkey, 1);
                this.form.medicine__stock_id.splice(barkey, 1);
                this.form.medicine__cost.splice(barkey, 1);
                this.form.medicine__stock.splice(barkey, 1);
                this.form.medicine__name.splice(barkey, 1);
                this.form.medicine__id.splice(barkey, 1);
                this.form.medicine__total.splice(barkey, 1);
               this.reTotal();
            },
        },
        beforeMount() {
            this.getProfile();
        },
        mounted() {
            this.getMedicinesList();
            this.getInvoice();
        }
    }
</script>
