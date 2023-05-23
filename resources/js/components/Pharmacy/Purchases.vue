<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-3">
                            <select v-model="sform.supplier_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Supplier</option>
                                <option :value="supplier.value" v-for="supplier in suppliers" :key="supplier.value">{{
                                    supplier.text }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <vp-date-picker locale="en" format="YYYY-MM-DD" placeholder="Purchase Date" v-model="sform.date"
                                :auto-submit="true" min="2019-10-06"></vp-date-picker>
                        </div>
                        <div class="col-md-3">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-sm btn-danger" @click="clearFilter" type="button">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Purchases
                                    <div class="card-tools">
                                        <a class="btn btn-sm btn-green-theme" href="/pharmacy/add-purchase"> <i
                                                class="fas fa-plus"></i> </a>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>Supplier</th>
                                            <th>Invoice No</th>
                                            <th>Shipment No</th>
                                            <th>Medicines</th>
                                            <th>Total</th>
                                            <th>Purchase Date</th>
                                            <th class="wf-150">Payment Status</th>
                                            <th class="wf-75">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(purchase, count) in purchases.data" :key="purchase.id">
                                            <td>{{ (purchases.current_page - 1) * (purchases.per_page) + count + 1 }}</td>
                                            <td><button class="border-0 btn-primary status-label"
                                                    @click="viewSupplier(purchase.supplier_id)"><i
                                                        class="fas fa-laptop"></i></button> {{ purchase.supplier_name }}
                                            </td>
                                            <td>{{ purchase.invoice_number }}</td>
                                            <td>{{ purchase.shipment_number }}</td>
                                            <td>
                                                <button class="border-0 btn-primary status-label"
                                                    @click="viewStock(purchase.id)">{{ purchase.stocks_count }}
                                                </button>
                                            </td>
                                            <td>{{ purchase.grand_total | formatOMR }}</td>
                                            <td>{{ purchase.purchase_date | setdate }}</td>
                                            <td align="center" v-if="purchase.pay_status == 1">
                                                <label class="status-label bg-teal m-r-2"><i
                                                        class="fas fa-check"></i></label>
                                                <button class="border-0 btn-primary status-label"
                                                    @click="viewPayment(purchase.pay_id)"><i
                                                        class="fas fa-laptop"></i></button>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink m-r-2"
                                                    v-show="profile.admin_type_id == 1 || profile.admin_type_id == 4"><i
                                                        class="fas fa-times"></i></label>
                                                <button class="border-0 btn-primary status-label"
                                                    @click="viewPayment(purchase.pay_id)"><i
                                                        class="fas fa-laptop"></i></button>
                                                <button class="border-0 btn-warning status-label"
                                                    v-show="profile.admin_type_id == 1 || profile.admin_type_id == 4"
                                                    @click="updatePayment(purchase.pay_id)"><i
                                                        class="fas fa-edit"></i></button>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-primary"
                                                    :href="'/pharmacy/view-purchase/' + purchase.id"
                                                    v-show="profile.admin_type_id == 1 || profile.admin_type_id == 4"> <i
                                                        class="fas fa-eye"></i> </a>
                                                <a target="_blank" :href="'/files/docs/' + purchase.bill_copy"
                                                    class="btn btn-secondary btn-sm list-btn"><i
                                                        class="fas fa-download"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="purchases.total > 1">
                                <pagination class="m-0 float-right" :data="purchases" @pagination-change-page="getResults"
                                    :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
                                    <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="supplierModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Supplier Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li>
                                <p class="alert m-0"><b>Name</b><br>{{ supplier.name }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Email</b><br>{{ supplier.email }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Contact No</b><br>{{ supplier.contact_no }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Company Name</b><br>{{ supplier.company_name }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Connection Source</b><br>{{ supplier.connection_source }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Joined On</b><br>{{ supplier.created_at | setdate }}</p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Status</b><br>{{ supplier.status }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="paymentModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Payment Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <span v-if="payment.payment_type == 1">
                            <ul class="link-list">
                                <li>
                                    <p class="alert m-0"><b>Payment Type</b><br>Payment in 2 Parts</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>First Payment Amount</b><br>{{ payment.firstpay_amount }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>First Payment Mode</b><br>{{ payment.firstpay_mode }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>First Payment Date</b><br>{{ payment.firstpay_date }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>First Payment Details</b><br>{{ payment.firstpay_desc }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Second Payment Amount</b><br>{{ payment.secondpay_amount }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Second Payment Mode</b><br>{{ payment.secondpay_mode }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Second Payment Date</b><br>{{ payment.secondpay_date }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Second Payment Details</b><br>{{ payment.secondpay_desc }}</p>
                                </li>
                            </ul>
                        </span>
                        <span v-else-if="payment.payment_type == 2">
                            <ul class="link-list">
                                <li>
                                    <p class="alert m-0"><b>Payment Type</b><br>Full Payment</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Payment Amount</b><br>{{ payment.firstpay_amount }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Payment Mode</b><br>{{ payment.firstpay_mode }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Payment Date</b><br>{{ payment.firstpay_date }}</p>
                                </li>
                                <li>
                                    <p class="alert m-0"><b>Payment Details (Txn)</b><br>{{ payment.firstpay_desc }}</p>
                                </li>
                            </ul>
                        </span>
                        <span v-else>
                            <ul class="link-list">
                                <li>
                                    <p class="alert m-0"><b>Payment Type</b><br>Unknown Payment Type</p>
                                </li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="stockModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Product Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product</th>
                                    <th>Batch No</th>
                                    <th>Purchase Cost</th>
                                    <th>Stock</th>
                                    <th>Exp Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(stock, sID) in stocks" :key="stock.id">
                                    <td>{{ sID + 1 }}</td>
                                    <td>{{ stock.name }}</td>
                                    <td>{{ stock.batch_no }}</td>
                                    <td>{{ stock.purchase_cost | formatOMR }}</td>
                                    <td>{{ stock.stock }}</td>
                                    <td>{{ stock.exp_date | setdate }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="updateFormModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updatePayments()">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Payment Details</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h5>Grand Total: <b class="m-l-5">{{ payment.grand_total }}</b></h5>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p>First Payment</p>
                                    <hr>
                                    <div class="form-group">
                                        <label class="label-control">Amount</label>
                                        <input type="text" readonly="readonly" v-bind:value="payment.firstpay_amount"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Mode</label>
                                        <input type="text" readonly="readonly" v-bind:value="payment.firstpay_mode"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Desc</label>
                                        <input type="text" readonly="readonly" v-bind:value="payment.firstpay_desc"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Date</label>
                                        <input type="text" readonly="readonly"
                                            v-bind:value="payment.firstpay_date | setdate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <p>Second Payment</p>
                                    <hr>
                                    <div class="form-group">
                                        <label class="label-control">Amount</label>
                                        <input type="text" readonly="readonly" v-bind:value="payment.secondpay_amount"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Mode</label>
                                        <select class="form-control" v-model="form.secondpay_mode" name="secondpay_mode"
                                            :class="{ 'is-invalid': form.errors.has('secondpay_mode') }">
                                            <option value="BT">Bank Transfer</option>
                                            <option value="CASH">Cash</option>
                                            <option value="CC">Credit Card</option>
                                            <option value="DC">Debit Card</option>
                                            <option value="PAYTM">Paytm</option>
                                        </select>
                                        <has-error :form="form" field="secondpay_mode"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Desc</label>
                                        <input v-model="form.secondpay_desc" type="text" name="secondpay_desc"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.has('secondpay_desc') }">
                                        <has-error :form="form" field="secondpay_desc"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Payment Date</label>
                                        <input v-model="form.secondpay_date" type="text" name="secondpay_date"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.has('secondpay_date') }">
                                        <has-error :form="form" field="secondpay_date"></has-error>
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
export default {
    data() {
        return {
            editmode: false,
            purchases: {},
            profile: '',
            supplier: '',
            payment: [],
            stocks: '',
            suppliers: '',
            loader: false,
            loaderurl: '/svg/loop.gif',
            form: new Form({
                id: '',
                grand_total: '',
                secondpay_mode: '',
                secondpay_date: '',
                secondpay_desc: ''
            }),
            sform: new Form({
                search: '',
                supplier_id: '',
                date: '',
            })
        }
    },
    methods: {
        clearFilter() {
            this.sform.reset();
            Fire.$emit('AfterCreate');
        },
        makeSearch() {
            this.loader = true;
            this.sform.search = 1;
            this.purchases = {};
            this.sform.post('/api/findPurchases')
                .then(({ data }) => {
                    this.purchases = data;
                    this.loader = false;
                })
                .catch(() => {
                    this.loader = false;
                });
        },
        getResults(page = 1) {
            axios.get('/api/purchases?page=' + page)
                .then(response => {
                    this.purchases = response.data;
                });
        },
        showPurchases() {
            this.$Progress.start();
            axios.get('/api/purchases').then(({ data }) => {
                this.purchases = data;
                this.$Progress.finish();
            });
        },
        viewSupplier(sid) {
            $('#supplierModal').modal('show');
            axios.get('/api/suppliers/' + sid)
                .then((data) => { this.supplier = data.data })
                .catch();
        },
        viewPayment(pid) {
            $('#paymentModal').modal('show');
            axios.get('/api/payments/' + pid)
                .then((data) => { this.payment = data.data[0] })
                .catch();
        },
        viewStock(pid) {
            $('#stockModal').modal('show');
            axios.get('/api/stocks/' + pid)
                .then((data) => { this.stocks = data.data })
                .catch();
        },
        updatePayment(pid) {
            axios.get('/api/payments/' + pid)
                .then((data) => {
                    this.payment = data.data[0];
                    this.form.id = data.data[0].id;
                })
                .catch();

            $('#updateFormModal').modal('show');
        },
        updatePayments() {
            this.form.put('/api/payments/' + this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#updateFormModal').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'Payment details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
        },
    },
    beforeMount() {
        axios.get('/api/getSuppliersSelectList').then((data) => { this.suppliers = data.data }).catch();
        axios.get('/api/get-active-user').then(response => { this.profile = response.data; });
    },
    created() {
        Fire.$on('searching', () => {
            axios.get('/api/findPurchase?q=' + this.$parent.search)
                .then((data) => {
                    this.purchases = data.data;
                })
                .catch();
        });
        this.showPurchases();
        Fire.$on('AfterCreate', () => {
            this.showPurchases();
        });
    }
}
</script>
