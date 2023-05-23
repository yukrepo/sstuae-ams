<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-1">
                            <input type="text" class="form-control" v-model="sform.batch_no" placeholder="Batch No">
                        </div>
                        <div class="col-md-2">
                            <vp-date-picker locale="en" format="YYYY-MM-DD" placeholder="added on Date"
                                v-model="sform.created_at" :auto-submit="true" :max="max_date"></vp-date-picker>
                        </div>
                        <div class="col-md-2">
                            <model-select :options="medicines" v-model="sform.medicine_id" placeholder="search medicine">
                            </model-select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.supplier_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Suppliers</option>
                                <option :value="supplier.value" v-for="supplier in suppliers"
                                    :key="'suppliers' + supplier.value">
                                    {{ supplier.text }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.stock" class="form-control" @change="makeSearch">
                                <option disabled value="">Stock Status</option>
                                <option value="1">Out Of Stocks</option>
                                <option value="2">Available Stocks</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                        </div>
                        <div class="col-md-1 text-right">
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
                                <h3 class="card-title">Stocks</h3>
                            </div>
                            <div class="card-body p-0 table-responsive search-content-box">
                                <form @submit.prevent="editmode ? updateStock() : ''">
                                    <table id="stock-alert" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="wf-75">SNo</th>
                                                <th>Purchase</th>
                                                <th>Medicine</th>
                                                <th>Batch No</th>
                                                <th>Exp Date</th>
                                                <th>Purchase Cost</th>
                                                <th>Cash Price</th>
                                                <th>Total Stock</th>
                                                <th>Available</th>
                                                <th>Adjustments</th>
                                                <th>FOC</th>
                                                <th>Added On</th>
                                                <th v-show="(profile.admin_type_id == 1) || (profile.admin_type_id == 4)">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(stock, count) in stocks.data" :key="stock.id">
                                                <td>{{ (stocks.current_page - 1) * (stocks.per_page) + count + 1 }}</td>
                                                <td>{{ stock.purchase_invoice }}</td>
                                                <td>{{ stock.name }}</td>
                                                <td>{{ stock.batch_no }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.exp_date"
                                                        :auto-submit="true"
                                                        :input-class="form.errors.has('exp_date') ? 'form-control is-invalid' : 'form-control'"></vp-date-picker>
                                                </td>
                                                <td v-else>
                                                    {{ stock.exp_date | setdate }}
                                                </td>
                                                <td>{{ stock.purchase_cost | formatOMR }}</td>
                                                <td>{{ stock.cash_price | formatOMR }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <input type="text" class="form-control" placeholder="modified stock"
                                                        v-model="form.received_stock">
                                                </td>
                                                <td v-else>
                                                    {{ stock.received_stock | freeNumber }}
                                                </td>
                                                <td>{{ stock.stock | freeNumber }}</td>
                                                <td>{{ stock.adjustments | freeNumber }}</td>
                                                <td>{{ stock.foc | freeNumber }}</td>
                                                <td>{{ stock.created_at | setdate }}</td>
                                                <td v-show="(profile.admin_type_id == 1) || (profile.admin_type_id == 4)">
                                                    <span v-if="(editmode && stock.id == form.id)">
                                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                                class="fas fa-save"></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            @click="cancelEdit"><i class="fas fa-times"></i></button>
                                                    </span>
                                                    <span v-else>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                                        <button type="button" class="btn btn-purple btn-sm"
                                                            @click="makeAdjustment(stock)"><i
                                                                class="fas fa-exchange-alt"></i></button>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div class="card-footer">
                                <img class="wf-50 float-right" :src="loaderurl" alt="updating" v-show="loader">
                                <pagination class="m-0 float-right" :limit="10" :data="stocks"
                                    @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
const _today = new Date();
const _todayComps = _today.getFullYear() + '-' + (_today.getMonth() + 1) + '-' + (_today.getDate() - 1);
export default {
    data() {
        return {
            editmode: false,
            stocks: {},
            suppliers: {},
            loader: false,
            loaderurl: '/svg/loop.gif',
            sform: new Form({
                created_at: '',
                stock: '',
                medicine_id: '',
                batch_no: '',
                search: '',
                supplier_id: ''
            }),
            medicines: [],
            max_date: _todayComps,
            profile: this.$store.getters.user,
            form: new Form({
                id: '',
                type: '',
                purchase_cost: '',
                received_stock: '',
                adjustments: '',
                exp_date: '',
                reason: ''
            })
        }
    },
    methods: {
        getResults(page = 1) {
            this.loader = true;
            if (this.sform.search == 1) {
                this.sform.post('/api/findStock?page=' + page).then(({ data }) => { this.stocks = data; this.loader = false; });
            } else {
                axios.get('/api/stocks?page=' + page).then(response => { this.stocks = response.data; this.loader = false; });
            }
        },
        showStock() {
            this.$Progress.start();
            axios.get('/api/stocks').then(({ data }) => {
                this.stocks = data;
                this.$Progress.finish();
            });
        },
        clearFilter() {
            this.sform.reset();
            this.showStock();
        },
        updateStock() {
            this.$Progress.start();
            this.form.put('/api/stocks/' + this.form.id)
                .then(() => {
                    if (this.sform.search == 1) {
                        this.sform.post('/api/findStock').then(({ data }) => { this.stocks = data; });
                    } else {
                        axios.get('/api/stocks').then(({ data }) => { this.stocks = data; });
                    }
                    this.editmode = false;
                    this.form.reset();
                    toast.fire({ type: 'success', title: 'Stock details updated successfully' });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
        },
        makeAdjustment(stock) {
            this.editmode = true;
            this.form.fill(stock);
            this.form.reason = '';
            this.form.adjustments = '';
            this.form.type = 2;
        },
        editStock(stock) {
            this.editmode = true;
            this.form.fill(stock);
            this.form.type = 1;
        },
        cancelEdit() {
            this.editmode = false;
            this.form.reset();
        },
        makeSearch() {
            this.loader = true;
            this.sform.search = 1;
            this.stocks = {};
            this.sform.post('/api/findStock')
                .then(({ data }) => {
                    this.stocks = data;
                    this.loader = false;
                })
                .catch(() => {
                    this.loader = false;
                });
        }
    },
    created() {
        axios.get('/api/getMedicinesSelectList').then((data) => { this.medicines = data.data }).catch();
        axios.get('/api/getSuppliersSelectList').then((data) => { this.suppliers = data.data }).catch();
        this.showStock();
        Fire.$on('AfterCreate', () => {
            this.showStock();
        });
    }
}
</script>
