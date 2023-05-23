<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" v-model="sform.invoice_number" placeholder="Invoice No">
                        </div>
                        <div class="col-md-2">
                            <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
                        </div>
                        <div class="col-md-2">
                            <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="sform.date" :auto-submit="true" placeholder="select date"></vp-date-picker>
                        </div>
                        <div class="col-md-2">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                           <button class="btn btn-danger btn-sm mr-1" type="button" @click="refreshRecord">Clear</button>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="btn-group" role="group" aria-label="First group">
                                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <button class="btn btn-sm btn-dark" @click="setPharmacyInvoice()"> <i class="fas fa-plus"></i> Create Pharmacy Invoice </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Direct Pharmacy Invoices </h3>
                            </div>
                            <div class="card-body p-0 table-responsive m-0">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-75">SNo</th>
                                            <th>Invoice Number</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th class="wf-100">Added On</th>
                                            <th class="text-center wf-75">Status</th>
                                            <th class="wf-120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(invoice, sn) in invoices.data" :key="invoice.id" :class="[(invoice.cancel != '0') ? 'bg-lightpink' : '' ]">
                                            <td>{{  (invoices.current_page - 1)*(invoices.per_page) + sn + 1 }}</td>
                                            <td>{{ invoice.invoice_number }}</td>
                                            <td>
                                                <button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewCustomer(invoice.user_id)">
                                                {{ invoice.first_name | capitalize }} {{ invoice.last_name?invoice.last_name:'' }}</button>
                                            </td>
                                            <td>{{ invoice.amount | formatOMR }}</td>
                                             <td>{{ invoice.admin | capitalize }}</td>
                                            <td>{{ invoice.created_at | setdate }}</td>
                                            <td v-if="invoice.cancel != '0'">
                                                --
                                            </td>
                                            <td align="center" v-else-if="invoice.payment_status == 1">
                                                <label class="status-label-full bg-teal">Paid</label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label-full bg-pink">Pending</label>
                                            </td>
                                            <td>
                                                <a class="btn btn-dark btn-sm" :href="'/invoices/print-dpinvoice/'+invoice.invoice_number" target="_blank"><i class="fas fa-print"></i></a>
                                                <a class="btn btn-warning btn-sm" :href="'/invoices/edit-pharmacy-invoice/'+invoice.invoice_number"><i class="fas fa-edit"></i></a>
                                                <button type="button" class="btn btn-danger btn-sm wf-25" @click="cancelInvoice(invoice.invoice_number)" ><i class="fas fa-times"></i></button>
                                                <button type="button" class="btn btn-danger btn-sm wf-25" @click="showReason(invoice)" v-show="invoice.cancel != '0'"><i class="fas fa-eye"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :limit="10" :data="invoices" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setPharmacyInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setPharmacyInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAppointModalTitle"> Create Pharmacy Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div>
                                <form @submit.prevent="createPharmacyCashInvoice()">
                                    <div class="row m-b-20">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label for="user_id" class="control-label">Search Customer</label>
                                                <model-select :options="customers"
                                                            name="user_id"
                                                            id="user_id"
                                                            aria-required="true"
                                                            v-model="form.user_id"
                                                            placeholder="search patient"
                                                            @input="onCustomerSelect" :class="{ 'is-invalid': form.errors.has('user_id') }">
                                                </model-select>
                                                <has-error :form="form" field="user_id"></has-error>
                                            </div>
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                    <tr class="text-uppercase">
                                                        <th style="width: 50px;">SNo</th>
                                                        <th>Medicine</th>
                                                        <th>Batch No (Exp Date)</th>
                                                        <th style="width:110px">Stock</th>
                                                        <th style="width:110px">Cash Price</th>
                                                        <th style="width:110px">Quantity</th>
                                                        <th style="width:110px">Total</th>
                                                        <th style="width: 50px;"> #</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                        <td>{{counter+1}}</td>
                                                        <td>
                                                            <input v-model="form.medicine__name[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['name']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__batch_no[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['batch_no']" readonly="readonly">
                                                        </td>
                                                            <td>
                                                            <input v-model="form.medicine__stock[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['stock']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__cost[getPhIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['cost']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__qty[getPhIndex(counter, cID)]" class="form-control" type="number" min="1" :max="form.medicine__stock[getPhIndex(counter, cID)]" @change="reCPhTotal" name="medicine[]['qty']">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__total[getPhIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['total']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <button type="button" @click="removeBar(cID);reCPhTotal()" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <td colspan="6" class="text-right">
                                                    <b> SUBTOTAL</b>
                                                    </td>
                                                    <td>
                                                        <input value="0" type="text" v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('sub_total') }">
                                                    </td>
                                                    <td>
                                                        <button type="button" @click="reCPhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                    </td>
                                                </tfoot>
                                            </table>
                                            <table class="table table-bordered m-b-0" v-show="form.discount_id >= 1">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-right text-uppercase">{{ form.offered_reason }}</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('offered_value') }"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-right text-uppercase">Total Payable</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('total') }"></td>
                                                    </tr>
                                                </thead>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="add-box">
                                                        <div class="add-box-body">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="payment_mode" class="control-label">Payment Mode</label>
                                                                        <select class="form-control" :class="{ 'is-invalid': form.errors.has('payment_mode') }" v-model="form.payment_mode" name="payment_mode">
                                                                            <option value="transfer" v-show="this.form.user_id <= 9">Transferred</option>
                                                                            <option value="cash" v-show="this.form.user_id >= 10">Cash</option>
                                                                            <option value="epay" v-show="this.form.user_id >= 10">E-Payment</option>
                                                                            <option value="visa" v-show="this.form.user_id >= 10">VISA Card</option>
                                                                            <option value="cash+visa" v-show="this.form.user_id >= 10">Cash + VISA</option>
                                                                            <option value="credit" v-show="this.form.user_id >= 10">Credit</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group" v-show="(form.payment_mode == 'epay') || (form.payment_mode == 'visa') || (form.payment_mode == 'cash+visa')">
                                                                        <label for="txn_id" class="control-label">Transaction Number</label>
                                                                        <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('txn_id') }" v-model="form.txn_id" name="txn_id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3" v-show="form.payment_mode == 'cash+visa'">
                                                                    <div class="form-group">
                                                                        <label for="cash_amount" class="control-label">Paid in cash </label>
                                                                        <input type="text" v-model="form.cash_amount" name="cash_amount" class="form-control" @keyup="getCCashVisa">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3" v-show="form.payment_mode == 'cash+visa'">
                                                                    <div class="form-group">
                                                                        <label for="visa_amount" class="control-label">Paid By Visa Card </label>
                                                                        <input type="text" v-model="form.visa_amount" name="visa_amount" class="form-control" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="remark" class="control-label">Remark</label>
                                                                <textarea class="form-control" rows="1"  v-model="form.remark" name="remark"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 border p-2">
                                                    <label class="control-label">Discount by SST

                                                    </label> <button @click="removeADiscount" type="button" class="btn btn-sm btn-danger float-right"> <i class="fas fa-times"></i> </button><hr class="m-b-5 m-t-5">
                                                    <span v-if="discounts">
                                                        <p>
                                                            <span class="input-checkbox" v-for="discount in discounts" :key="discount.id">
                                                                <input type="radio" v-model="form.discount_id" :value="discount.id" name="form.discount_id" :id="'discount'+discount.id" @change="reCPhTotal">
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
                                        </div>
                                        <div class="col-4">
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
                                                        <button type="submit" class="btn btn-sm btn-dark"> Add Now </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <table class="table table-bordered" v-show="(form.payment_mode == 'cash') || (form.payment_mode == 'cash+visa')">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="3" class="text-center text-uppercase">
                                                                Calculate Returnable Amount</th>
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
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group text-danger text-right"><i>{{ catchmsg }}</i></div>
                                    <div class="form-group text-right" v-if="loader">
                                        <img style="height:40px" :src="svgurl+'loop.gif'" alt="updating">
                                    </div>
                                    <div class="text-right form-group" v-else>
                                        <button type="submit" class="btn btn-sm btn-primary"> Create Invoice </button>
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
                            <button type="button" v-if="nmform.type == 1" class="btn btn-lg btn-success m-b-20" @click="reCPhTotal" data-dismiss="modal"> OK ! </button>
                            <button type="button" class="btn btn-lg btn-success m-b-20" @click="reCPhTotal" data-dismiss="modal" v-else> OK ! </button>
                        </div>
                        <div class="modal-footer text-center d-block">
                            <i class="fas fa-check text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="overQtyModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="overQtyModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Stock Quantity Issue
                        </div>
                        <div class="modal-body text-center">
                            <h4 class="m-b-20 m-t-20">It seems you have entered more Quantity than we have in stock.<br>
                            The quantity will automatically be reversed to max stock available.
                            </h4>
                            <button type="button" class="btn btn-lg btn-success m-b-20" @click="reCPhTotal" data-dismiss="modal"> OK ! </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="reasonModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="reasonModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">Invoice Cancellation Reason</h5>
                    </div>
                    <div class="modal-body">
                        <p v-for="(reasons, rkey) in reason" :key="rkey" class="border-bottom">
                            <b class="text-uppercase">{{ rkey }}</b><br>
                            <span>{{ reasons }}</span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Close </button>
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
                profile:'',
                svgurl: '/svg/',
                loader:false,
                startYear: 2018,
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: this.$route.params.id,
                invoices:{},
                customer: {},
                customers: [],
                countlist:[],
                medicines:[],
                calcmsg:'',
                stocks:[],
                catchmsg:'',
                discounts:{},
                form: new Form({
                    appointment_id:'',
                    user_id:'',
                    insurance_id:1,
                    medicine__qty:[],
                    medicine__batch_no:[],
                    medicine__stock_id:[],
                    medicine__cost:[],
                    medicine__stock:[],
                    medicine__name:[],
                    medicine__id:[],
                    medicine__total:[],
                    sub_total:0,
                    visa_amount:'',
                    cash_amount:'',
                    total:0,
                    offered:'',
                    offered_type:'',
                    offered_reason:'',
                    offered_value:0,
                    type:4,
                    remark:'',
                    payment_mode:'cash',
                    txn_id:'',
                    discount_id:'',
                    iinvoice:'',
                    returnable:'',
                    received:''
                }),
                nmform: new Form({
                    medicine_id:'',
                    stock_id:'',
                }),
                loader:false,
                loaderurl:'/svg/loop.gif',
                sform: new Form({
                        invoice_number:'',
                        user_id:'',
                        date:'',
                        search:'',
                }),
                reason:''
            }
        },
        methods: {
            activeEdit(cval, adate) {
                if(this.profile.admin_type_id == 1) {
                    return true;
                } else {
                    let today = new Date();
                    let ndate = new Date(adate);
                    var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                    var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                    var diffDuration =moment.duration(b.diff(a));
                    return ((diffDuration.days() >= 0) && (cval == '0'))?true:false;
                }
            },
            getPhIndex(list, id) {
                return id;
            },
            getDiscounts(type) {
                axios.post('/api/get-filtered-discounts', {
                    date:'',
                    type: 'dm',
                    time_slots:'',
                })
                .then((response) => {
                    this.discounts = response.data;
                });
                //console.log(this.discounts);
            },
            viewInvoice(invoice){

            },
            viewCustomer(uid){
            },
            viewAppointment(apid){
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

            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/invoices/direct-pharmacy-invoices/'+year);
                //let route = this.$router.resolve({path: '/invoices/direct-pharmacy-invoices/'+year});
                //window.open(route.href, '_blank');
                Fire.$emit('changeyear');
            },
            getMedicinesList() {
                axios.get('/api/getMedicinesSelectList').then((data) => {this.medicines = data.data }).catch();
            },
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            yearsList() {
                axios.get('/api/getPrevYearsList').then(({ data }) => (this.yearList = data));
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            removeADiscount() {
                this.form.discount_id = '';
                this.form.offered_value = 0;
                this.form.offered_reason = '';
                this.reCPhTotal();
            },

            getResults(page = 1) {
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                if(this.sform.search == 1) {
                    this.sform.post('/api/findDPharmacyInvoices?page=' + page)
                    .then(({data}) => {this.invoices = data;});
                } else {
                    axios.get('/api/invoices/get-dpharmacy-invoices/'+checkYear+'?page=' + page)
                        .then(response => { this.invoices = response.data; });
                }
            },

            showInvoices() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/invoices/get-dpharmacy-invoices/'+checkYear).then(({ data }) => {
                    this.invoices = data;
                    this.$Progress.finish();
                });
            },
            reCPhTotal(){
                this.updatecSubTotal();
                this.addDiscount();
                let total = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.offered_value);
                this.form.total = this.makeNumber(total);
            },
            addDiscount() {
                let offer = ''; let reason = '';
                if(this.form.discount_id >= 1) {
                    let adiscount = this.discounts[this.form.discount_id];
                    if(adiscount.value.indexOf('%') >= 1) {
                        reason = adiscount.title;
                        offer = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(adiscount.value.replace('%', ''))/100);
                    }
                    else {
                        reason = adiscount.title;
                        offer = this.makeNumber(adiscount.value);
                    }
                    this.form.offered_reason = reason;
                    this.form.offered_value = offer;
                } else {
                    this.form.offered_value = 0;
                    this.form.offered_reason = '';
                }
            },
            removeBar(barkey){
                this.countlist.splice(barkey, 1);
                this.form.medicine__qty.splice(barkey, 1);
                this.form.medicine__batch_no.splice(barkey, 1);
                this.form.medicine__stock_id.splice(barkey, 1);
                this.form.medicine__cost.splice(barkey, 1);
                this.form.medicine__stock.splice(barkey, 1);
                this.form.medicine__name.splice(barkey, 1);
                this.form.medicine__id.splice(barkey, 1);
                this.form.medicine__total.splice(barkey, 1);
               this.reCPhTotal();
            },
            getCCashVisa() {
                let visa_amount = this.makeNumber(this.form.total) - this.makeNumber(this.form.cash_amount);
                this.form.visa_amount = visa_amount.toFixed(3);
            },
            onSelect (product) {
               axios.get('/api/stocks/get-list-by-medicine/'+product)
                    .then((data) => {
                        if(data.data){
                            this.stocks = data.data;
                            //this.reCPhTotal();
                        }
                    });
            },
            addMedicine() {
                if((this.nmform.medicine_id == '') || (this.nmform.stock_id == '')){
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Please select the medicine and batch no.',
                        timer: 2500
                    });
                    return false;
                }
                let bcount = this.countlist.length;
                let pkey = bcount;

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
                this.countlist.push(pkey);
                this.reCPhTotal();
                $('#confirmModal').modal('show');
            },
            onCustomerSelect(option) {
                axios.get('/api/customers/'+option)
                    .then((data) => {
                        this.sselected = data.data[0].id;
                        this.form.user_id = data.data[0].id;
                        if(data.data[0].id <= 9) {
                            this.form.payment_mode = 'transfer';
                        } else {
                            this.form.payment_mode = 'cash';
                        }
                    })
                    .catch();
            },
            setPharmacyInvoice() {
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                this.getMedicinesList();
                this.form.reset();
                $('#setPharmacyInvocieModal').modal('show');
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            updatecSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                counter.forEach(element => {
                    if(this.makeNumber(this.form.medicine__qty[element]) > this.makeNumber(this.form.medicine__stock[element])) {
                        this.form.medicine__qty[element] = this.form.medicine__stock[element];
                        $('#overQtyModal').modal('show');
                    }
                    this.form.medicine__total[element] = this.makeNumber(this.form.medicine__cost[element]) * this.makeNumber(this.form.medicine__qty[element]);
                    stotal = stotal +  this.form.medicine__total[element];
                });
                this.form.sub_total = stotal;
            },
            createPharmacyCashInvoice() {
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
                else if(this.form.payment_mode == 'cash+visa') {
                    if((this.form.txn_id === null) || (this.form.visa_amount <= 0)) {
                        swal.fire('Missing Values', 'Transaction number and amount paid by VISA is required for this payment mode.', 'error');
                        return false;
                    }
                }
                this.loader = true;
                this.form.post('/api/invoices/create-direct-invoice')
                .then((data) => {
                    swal.fire({
                        title: 'Medicine Invoice has been generated successfully.',
                        type: 'success',
                        }).then((result) => {
                            Fire.$emit('changeyear');
                            this.loader = false;
                            this.form.reset();
                            $('#setPharmacyInvocieModal').modal('hide');
                            //this.$router.push('/invoices/print-dpinvoice/'+data.data);
                            let route = this.$router.resolve({path: '/invoices/print-dpinvoice/'+data.data});
                            window.open(route.href, '_blank');
                        });
                })
                .catch((error) => {
                    this.loader = false;
                    this.catchmsg = error.message;
                });
            },
            cancelInvoice(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "Please enter the reason before cancelling the invoice.",
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
                        return axios.post('/api/cancel-pharmacy-invoice', {
                            invoice_number: id,
                            reason:reason
                        })
                        .then(response => {
                            Fire.$emit('changeyear');
                            swal.fire('Cancelled!', 'Invoice has been cancelled successfully.', 'success');
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            },
            showReason(inv) {
                this.reason = JSON.parse(inv.cancel);
                $('#reasonModal').modal('show');
            },
            searchAssets() {
                let activeId = this.$route.path.split("/");
                this.activeYear = activeId[3];
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                 this.sform.year = this.activeYear;
                this.invoices = {};
                this.sform.post('/api/findDPharmacyInvoices')
                    .then(({data}) => {
                        this.invoices = data;
                         this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            },
            refreshRecord() {
                this.sform.reset();
                this.showInvoices();
            }
        },
        beforeMount() {
            this.searchAssets();
            axios.get('/api/get-active-user').then(response => {this.profile = response.data;});
        },
        mounted() {
            this.yearsList();
            this.showInvoices();
            Fire.$on('changeyear', () => {
                this.showInvoices();
            });
            this.getDiscounts();
        }
    }
</script>
