<template>
    <div>
        <div class="search-box">
            <form @submit.prevent="makeSearch()">
                <div class="row">
                    <div class="col-md-3">
                        <label for="barcode" class="control-label">Medicine</label>
                        <model-select :options="medicines" name="barcode" v-model="sform.barcode"
                            placeholder="select medicine"></model-select>
                    </div>
                    <div class="col-md-3">
                        <label for="submit" class="control-label d-block">Actions</label>
                        <input type="submit" class="btn btn-sm btn-primary text-uppercase" value="Get ledger">
                        <button type="button" @click="resetSearch" class="btn btn-sm btn-danger text-uppercase">Reset
                            ledger</button>
                        <span v-show="loader == 1" class="m-l-15">
                            <img style="height:25px" :src="loaderurl">
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card m-b-10">
                        <div class="card-header">
                            <h3 class="card-title">
                                <span class="text-uppercase">Medicine details</span>
                            </h3>
                        </div>
                        <div class="card-body p-0 table-responsive m-0">
                            <table class="table table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Medicine Name</th>
                                        <th>Unitsize</th>
                                        <th>Barcode</th>
                                        <th>Cash Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="medicine.id">
                                        <td>{{ medicine.category_name }}</td>
                                        <td>{{ medicine.name }}</td>
                                        <td>{{ medicine.unitsize }} {{ medicine.unit }}</td>
                                        <td>{{ medicine.barcode }}</td>
                                        <td>{{ medicine.cash_price | formatOMR }}</td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="6">
                                            <p class="text-danger">make search to view ledger</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card m-b-10">
                        <div class="card-header">
                            <h3 class="card-title">
                                <span class="text-uppercase">Stock Details</span>
                            </h3>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Invoice No</th>
                                        <th>Batch No</th>
                                        <th>Exp Date</th>
                                        <th>Purchase Cost</th>
                                        <th>Received Stock</th>
                                        <th>Available</th>
                                        <th>Adjustments</th>
                                        <th>FOC</th>
                                        <th>Added On</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(stock, rkey) in stocks" :key="rkey">
                                        <td>{{ rkey + 1 }}</td>
                                        <td>{{ stock.purchase_invoice }}</td>
                                        <td>{{ stock.batch_no }}</td>
                                        <td>{{ stock.exp_date | setdate }}</td>
                                        <td>{{ stock.purchase_cost | formatOMR }}</td>
                                        <td>{{ stock.received_stock | freeNumber }}</td>
                                        <td>{{ stock.stock | freeNumber }}</td>
                                        <td>{{ stock.adjustments | freeNumber }}</td>
                                        <td>{{ stock.foc | freeNumber }}</td>
                                        <td>{{ stock.created_at | setdate }}</td>
                                        <th> <button class="btn btn-primary btn-sm" type="button"
                                                @click="viewCompleteLedger(stock.id)"> <i class="fas fa-eye"></i> </button>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="viewLedgerModal" data-backdrop="static" data-keyboard="false"
            aria-labelledby="viewLedgerModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewLedgerModalTitle">Stock Based Ledger</h5>
                    </div>
                    <div class="modal-body" v-if="(loader == 2)">
                        <img style="height:50px" :src="loaderurl">
                    </div>
                    <div class="modal-body " v-else>
                        <div class="doc-schdular">
                            <div class="card m-b-5">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <span class="text-uppercase">Admin Activities</span>
                                    </h3>
                                </div>
                                <div class="card-body p-0 table-responsive mid-content-box">
                                    <table class="table table-bordered table-striped m-0">
                                        <thead>
                                            <tr>
                                                <th>SNo</th>
                                                <th>Date</th>
                                                <th>Title</th>
                                                <th>Old Data</th>
                                                <th>New Date</th>
                                                <th>Reason</th>
                                                <th>Adjusted By</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="adjustments">
                                            <tr v-for="(adjust, count) in adjustments" :key="count">
                                                <td>{{ count + 1 }}</td>
                                                <td>{{ adjust.date | setdate }}</td>
                                                <td>{{ adjust.title }}</td>
                                                <td>Available - {{ adjust.old_data.available }} | Received - {{
                                                    adjust.old_data.received }}</td>
                                                <td>Available - {{ adjust.new_data.available }} | Received - {{
                                                    adjust.new_data.received }}</td>
                                                <td>{{ (adjust.reason) ? adjust.reason : '--' }}</td>
                                                <td>{{ adjust.admin }}</td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="7">
                                                    <p class="text-danger">No Adjustments/modifications done</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <span class="text-uppercase">Sales Details</span>
                                    </h3>
                                </div>
                                <div class="card-body p-0 table-responsive mid-content-box">
                                    <table class="table table-bordered table-striped m-0">
                                        <thead>
                                            <tr>
                                                <th>SNo</th>
                                                <th>Invoice Number</th>
                                                <th>Customer</th>
                                                <th>Sale Date</th>
                                                <th>Quantity</th>
                                                <th>Revised</th>
                                                <th>Created By</th>
                                                <th>Status @ Sales Time</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="sales">
                                            <tr v-for="(sale, count) in sales" :key="count">
                                                <td>{{ count + 1 }}</td>
                                                <td>{{ sale.invoice_number }}</td>
                                                <td>{{ sale.first_name }} {{ sale.last_name }}</td>
                                                <td>{{ sale.created_at | setdate }}</td>
                                                <td>{{ sale.qty }}</td>
                                                <td> <label class="status-label-full-outline text-danger"
                                                        v-if="sale.revised == 1">Yes</label>
                                                    <label class="status-label-full-outline text-success" v-else>No</label>
                                                </td>
                                                <td>{{ sale.name }}</td>
                                                <td>
                                                    <b>Available: </b> {{ JSON.parse(sale.rowdata).stock }}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="7">
                                                    <p class="text-danger">No sales done</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
export default {
    data() {
        return {
            editmode: false,
            stocks: [],
            medicines: [],
            medicine: '',
            loader: false,
            sales: [],
            adjustments: [],
            loaderurl: '/svg/loop.gif',
            form: new Form({
                id: '',
                image: '',
                barcode: ''
            }),
            sform: new Form({
                barcode: ''
            })
        }
    },
    methods: {
        resetSearch() {
            this.sform.reset();
            this.stocks = [];
            this.medicine = '';
        },
        makeSearch() {
            if (this.sform.barcode < 1) {
                this.$toaster.error('Please select medicine.');
                return;
            }
            this.loader = 1;
            this.sform.post('/api/stocks/get-stock-ledger').then((data) => {
                this.stocks = data.data.stocks;
                this.medicine = data.data.medicine;
                this.loader = false;
            });
        },
        viewCompleteLedger(sid) {
            this.loader = 2;
            $('#viewLedgerModal').modal('show');
            axios.get('/api/stocks/get-sale-stock-ledger/' + sid).then((data) => {
                this.adjustments = data.data.adjustments;
                this.sales = data.data.sales;
                this.loader = false;
            }).catch();
        }
    },
    beforeMount() {
        axios.get('/api/getMedicinesSelectList').then((data) => { this.medicines = data.data }).catch();
    },
    created() {
        Fire.$on('AfterCreate', () => {
            this.makeSearch();
        });
    }
}
</script>
