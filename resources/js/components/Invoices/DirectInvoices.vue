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
                        <div class="col-md-3">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                        </div>
                        <div class="col-md-3 text-right">
                            <button class="btn btn-primary btn-sm mr-2" type="button" @click="setInvoice">
                                <i class="fa fa-plus"></i> Add New
                            </button>
                            <div class="btn-group" role="group" aria-label="First group">
                                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                 <invoice-list :invoice_types="{9:'Direct Invoices'}" :field="''" :fieldLabel="''" :aYear="activeYear" :title="'Direct Invoices'" :searchFields="searchdata"></invoice-list>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAppointModalTitle"> Create Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <form @submit.prevent="createCashInvoice()">
                                <div class="row m-b-20">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="user_id" class="control-label">Search Customer</label>
                                            <model-select :options="customers"
                                                        name="user_id"
                                                        id="user_id"
                                                        aria-required="true"
                                                        v-model="form.user_id"
                                                        placeholder="search patient"
                                                        :class="{ 'is-invalid': form.errors.has('user_id') }">
                                            </model-select>
                                            <has-error :form="form" field="user_id"></has-error>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-group">
                                            <label for="ref_by" class="control-label">Reference</label>
                                            <input type="text" v-model="form.reference" placeholder="Enter reference value" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-group">
                                            <label for="invocie_date" class="control-label">Invoice Date</label>
                                            <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.invoice_date" :auto-submit="true" placeholder="select date"></vp-date-picker>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-b-20">
                                    <div class="col-12">
                                        <table class="table table-striped table-bordered table-condensed">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th class="wf-75">SNo</th>
                                                    <th>PARTICULARS</th>
                                                    <th class="wf-150">Unit Price</th>
                                                    <th class="wf-150">Quantity</th>
                                                    <th class="wf-150">Sub Total</th>
                                                    <th class="wf-75">
                                                        <button class="btn btn-sm btn-success" type="button" @click="addRow">
                                                            <i class="fa fa-plus cursor-pointer" ></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                    <td>{{counter+1}}</td>
                                                    <td>
                                                        <input v-model="form.row__heading[getPhIndex(counter, cID)]" class="form-control mb-1" type="text" placeholder="Heading" />
                                                         <input v-model="form.row__text[getPhIndex(counter, cID)]" class="form-control" type="text"  placeholder="Details" />
                                                    </td>
                                                    <td>
                                                        <input v-model="form.row__cost[getPhIndex(counter, cID)]" class="form-control" type="text" />
                                                    </td>
                                                    <td>
                                                        <input v-model="form.row__qty[getPhIndex(counter, cID)]" class="form-control" type="number" min="1" @change="reCPhTotal" />
                                                    </td>
                                                    <td>
                                                        <input v-model="form.row__total[getPhIndex(counter, cID)]" class="form-control" type="text" min="1" readonly="readonly" />
                                                    </td>
                                                    <td>
                                                        <button type="button" @click="removeBar(getPhIndex(counter, cID));reCPhTotal()" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <td colspan="4" class="text-right">
                                                <b> TOTAL PAYABLE</b>
                                                </td>
                                                <td>
                                                    <input value="0" type="text" v-model="form.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('total') }">
                                                </td>
                                                <td>
                                                    <button type="button" @click="reCPhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                </td>
                                            </tfoot>
                                            <tfoot>
                                                <td colspan="4" class="text-right">
                                                <b> SUBTOTAL</b>
                                                </td>
                                                <td>
                                                    <input value="0" type="text" v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('sub_total') }">
                                                </td>
                                                <td>
                                                    <button type="button" @click="reCPhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                </td>
                                            </tfoot>
                                            <tfoot v-if="form.offered_value">
                                                <td colspan="4" class="text-right">
                                                <b> {{ form.offered_reason }}</b>
                                                </td>
                                                <td>
                                                    <input value="0" type="text" v-model="form.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('offered_value') }">
                                                </td>
                                                <td>
                                                    <button type="button" @click="reCPhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                </td>
                                            </tfoot>
                                            <tfoot v-if="form.tax_enabled">
                                                <td colspan="4" class="text-right">
                                                <b> VAT ({{ $store.getters.configs.normal_tax }})</b>
                                                </td>
                                                <td>
                                                    <input value="0" type="text" v-model="form.tax_value" name="tax_value" id="tax_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('tax_value') }">
                                                </td>
                                                <td>
                                                    <button type="button" @click="reCPhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                </td>
                                            </tfoot>
                                        </table>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="add-box">
                                                    <div class="add-box-body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="payment_mode" class="control-label">Payment Mode</label>
                                                                    <select class="form-control" :class="{ 'is-invalid': form.errors.has('payment_mode') }" v-model="form.payment_mode" name="payment_mode">
                                                                        <option value="transfer" v-show="this.form.user_id == 1">Transferred</option>
                                                                        <option value="cash" v-show="this.form.user_id != 1">Cash</option>
                                                                        <option value="epay" v-show="this.form.user_id != 1">E-Payment</option>
                                                                        <option value="visa" v-show="this.form.user_id != 1">VISA Card</option>
                                                                        <option value="cash+visa" v-show="this.form.user_id != 1">Cash + VISA</option>
                                                                        <option value="credit" v-show="this.form.user_id != 1">Credit</option>
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
                                            <div class="col-md-3">
                                                <label class="control-label">Enable Tax</label>
                                                <hr class="m-b-5 m-t-5">
                                                <button type="button" @click="disableTax" v-if="form.tax_enabled" class="btn btn-sm btn-dark wf-175">Disable Taxation</button>
                                                <button type="button" @click="enableTax" v-else class="btn btn-sm btn-outline-dark wf-175">Enable Taxation</button>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="add-box">
                                                    <div class="add-box-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label class="control-label">Add Discount Label</label>
                                                                    <input type="text" class="form-control" v-model="form.offered_reason" placeholder="Enter Discount label" />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">Add Discount Value</label>
                                                                    <div class="input-group">
                                                                        <select class="custom-select" v-model="form.offered_type">
                                                                            <option selected>Choose...</option>
                                                                            <option value="1">Fixed</option>
                                                                            <option value="2">Percent</option>
                                                                        </select>
                                                                        <input type="text" class="form-control" v-model="form.offered_percent" placeholder="Enter Discounted Amount" style="height:32px" />
                                                                        <div class="input-group-append">
                                                                            <button class="btn btn-sm btn-outline-secondary" type="button" @click="reCPhTotal">Apply</button>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
import InvoiceList from "./Includes/InvoiceList.vue";

    export default {
         components: {InvoiceList },
        data() {
            return {
                profile:'',
                svgurl: '/svg/',
                loader:false,
                startYear: 2018,
                yearList: this.$store.getters.yearList,
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                invoices:{},
                customer: {},
                customers: [],
                countlist:[0],
                calcmsg:'',
                catchmsg:'',
                form: new Form({
                    user_id:'',
                    insurance_id:1,
                    reference:'',
                    invoice_date:'',
                    row__qty:[],
                    row__heading:[],
                    row__text:[],
                    row__cost:[],
                    row__total:[],
                    sub_total:0,
                    visa_amount:'',
                    cash_amount:'',
                    total:0,
                    offered:'',
                    offered_type:1,
                    offered_reason:'',
                    offered_percent:0,
                    offered_value:0,
                    type:7,
                    remark:'',
                    payment_mode:'cash',
                    txn_id:'',
                    iinvoice:'',
                    returnable:'',
                    received:'',
                    tax_enabled:false,
                    tax_value:0,
                }),
                loaderurl:'/svg/loop.gif',
                sform: new Form({
                        invoice_number:'',
                        user_id:'',
                        date:'',
                }),
                reason:''
            }
        },
        computed: {
            searchdata() {
                return {
                    invoice_number:this.sform.invoice_number,
                    user_id:this.sform.user_id,
                    date:this.sform.date,
                }
            }
        },
        methods: {
            addRow() {
                this.countlist.push(this.countlist[this.countlist.length-1]+1);
            },
            getPhIndex(list, id) {
                return id;
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
                this.form.returnable = returnable.toFixed(2);

            },

            changeYear(year) {
                this.activeYear = year;
                Fire.$emit('changeyear');
            },
            
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            enableTax() {
                this.form.tax_enabled = true;
                this.form.tax_value = 0;
                this.reCPhTotal();
            },
            disableTax() {
                this.form.tax_enabled = false;
                this.reCPhTotal();
            },
            reCPhTotal(){
                this.updatecSubTotal();
                this.addDiscount();
                let total = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.offered_value);
                if(this.form.tax_enabled) {
                     let txp = this.$store.getters.configs.normal_tax;
                    this.form.tax_value = this.makeNumber(this.makeNumber(total)*this.makeNumber(txp.replace('%', ''))/100).toFixed(2);
                } else {
                    this.form.tax_value = 0;
                }
                total = this.makeNumber(total) + this.makeNumber(this.form.tax_value);
                this.form.total = (this.makeNumber(total) >= 0) ? this.makeNumber(total).toFixed(2) : 0;
            },
            addDiscount() {
                let offer = ''; 
                if(this.form.offered_percent) {
                    if(this.form.offered_type == 2) {
                        offer = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.form.offered_percent)/100);
                    }
                    else {
                        offer = this.makeNumber(this.form.offered_percent);
                    }
                    this.form.offered_value = offer;
                } 
            },
            removeBar(barkey){
                this.countlist.splice(barkey, 1);
                this.form.row__qty.splice(barkey, 1);
                this.form.row__heading.splice(barkey, 1);
                this.form.row__text.splice(barkey, 1);
                this.form.row__cost.splice(barkey, 1);
                this.form.row__total.splice(barkey, 1);
                this.reCPhTotal();
            },
            getCCashVisa() {
                let visa_amount = this.makeNumber(this.form.total) - this.makeNumber(this.form.cash_amount);
                this.form.visa_amount = visa_amount.toFixed(2);
            },
           
            setInvoice() {
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                this.form.reset();
                $('#setInvocieModal').modal('show');
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            updatecSubTotal() {
                let counter = this.countlist;
                let stotal = 0; 
                counter.forEach((ele, element) => {
                    this.form.row__total[element] = this.makeNumber(this.form.row__cost[element]) * this.makeNumber(this.form.row__qty[element]);
                    stotal = stotal +  this.form.row__total[element];
                });
                this.form.sub_total = stotal;
            },
            createCashInvoice() {
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
                this.form.post('/api/invoices/create-new-direct-invoice')
                .then((data) => {
                    swal.fire({
                        title: 'Invoice has been generated successfully.',
                        type: 'success',
                        }).then((result) => {
                            Fire.$emit('changeyear');
                            this.loader = false;
                            this.form.reset();
                            $('#setInvocieModal').modal('hide');
                            //this.$router.push('/invoices/print-dpinvoice/'+data.data);
                            let route = this.$router.resolve({path: '/invoices/print-ui-invoice/'+data.data});
                            window.open(route.href, '_blank');
                        });
                })
                .catch((error) => {
                    this.loader = false;
                    this.catchmsg = error.message;
                });
            },   
            searchAssets() {
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                 this.sform.year = this.activeYear;
                this.invoices = {};
                this.sform.post('/api/findDInvoices')
                    .then(({data}) => {
                        this.invoices = data;
                         this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            },
        },
        beforeMount() {
            let activeId = this.$route.path.split("/");
            this.activeYear = activeId[3];
        },
        created() {
            this.searchAssets();
        }
    }
</script>
