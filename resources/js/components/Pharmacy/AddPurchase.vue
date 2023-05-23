<template>
    <div>
         <div class="content bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form @submit.prevent="addPurchase()">
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Add Purchase</h3>
                                </div>
                                <div class="full-form-body">
                                    <div class="row customer-div">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mobile" class="label-control">Invoice Number</label>
                                                <input type="text" v-model="form.invoice_number" name="invoice_number" id="invoice_number" class="form-control" :class="{ 'is-invalid': form.errors.has('invoice_number') }">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mobile" class="label-control">Shipment Number</label>
                                                <input type="text" v-model="form.shipment_number" name="shipment_number" id="shipment_number" class="form-control" :class="{ 'is-invalid': form.errors.has('shipment_number') }">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mobile" class="label-control">Purchase Date</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.purchase_date" :auto-submit="true" :input-class="form.errors.has('purchase_date') ?'form-control is-invalid' : 'form-control'"></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mobile" class="label-control">Search Supplier</label>
                                                <model-select :options="suppliers"
                                                    autocomplete="off"
                                                    name="supplier_id"
                                                    v-model="form.supplier_id"
                                                    @input="selectSupplier"
                                                    placeholder="select supplier">
                                                </model-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="label-control">View Supplier Details</label>
                                                <a class="btn btn-sm btn-success btn-block" v-if="sselected != ''" href="javascript:;" @click="supplierDetail"> View Supplier Details
                                                </a>
                                                <a class="btn btn-sm btn-secondary btn-block" v-else href="javascript:;"> Supplier Not Selected
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pdetails">
                                        <div class="ptitle">
                                            <p class="m-0">Products
                                                <span class="float-right">
                                                    <button type="button" @click="addBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button>
                                                </span>
                                            </p>
                                            <span class="float-none"></span>
                                        </div>
                                        <div class="pbody">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="wf-50">SNo</th>
                                                        <th>Product</th>
                                                        <th class="wf-45">View</th>
                                                        <th>Batch No</th>
                                                        <th class="wf-100">Exp Date</th>
                                                        <th class="wf-100">Stock</th>
                                                        <th class="wf-100">Foc</th>
                                                        <th class="wf-150">Purchase Cost / Pack</th>
                                                        <th class="wf-100">Total</th>
                                                        <th class="wf-50">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                        <td>{{counter+1}}</td>
                                                        <td>
                                                            <model-select :options="products"
                                                                            autocomplete="off"
                                                                            name="product_detail[]['barcode']"
                                                                            v-model="form.product_detail__id[getIndex(counter, cID)]"
                                                                            placeholder="select medicine"
                                                                            @input="onProductSelect(form.product_detail__id[getIndex(counter, cID)], cID)">
                                                            </model-select>
                                                        </td>
                                                        <td>
                                                            <button type="button" v-if="productSelected[cID] >= 1" class="btn btn-sm btn-success" @click="productDetail(productSelected[cID])"><i class="fas fa-laptop"></i></button>
                                                            <button type="button" v-else class="btn btn-sm btn-secondary"><i class="fas fa-laptop"></i></button>
                                                        </td>
                                                        <td>
                                                            <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__batch_no[getIndex(counter, cID)]" class="form-control" type="text" name="product_detail[]['batch_no']">
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__batch_no[getIndex(counter, cID)]" class="form-control" type="text" name="product_detail[]['batch_no']">
                                                        </td>
                                                        <td>
                                                            <vp-date-picker v-if="productSelected[cID] >= 1" locale="en" format="YYYY-MM-DD" v-model="form.product_detail__exp_date[getIndex(counter, cID)]" :auto-submit="true"></vp-date-picker>
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__exp_date[getIndex(counter, cID)]" type="text" name="product_detail[]['exp_date']" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__stock[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="1" type="number" name="product_detail[]['stock']">
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__stock[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="1" type="number" name="product_detail[]['stock']">
                                                        </td>
                                                        <td>
                                                            <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__foc[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="1" type="number" name="product_detail[]['foc']">
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__foc[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="1" type="number" name="product_detail[]['foc']">
                                                        </td>
                                                        <td>
                                                            <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__purchase_cost[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="0" type="text" name="product_detail[]['purchase_cost']" placeholder="0.000">
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__purchase_cost[getIndex(counter, cID)]" class="form-control" @change="reTotal" value="0" type="text" name="product_detail[]['purchase_cost']" placeholder="0.000">
                                                        </td>
                                                        <td>
                                                            <input v-if="productSelected[cID] >= 1" v-model="form.product_detail__total[getIndex(counter, cID)]" class="form-control" type="text" readonly="readonly" name="product_detail[]['total']" value="0.00">
                                                            <input v-else disabled="disabled" readonly="readonly" v-model="form.product_detail__total[getIndex(counter, cID)]" class="form-control" type="text" name="product_detail[]['total']" value="0.00">
                                                        </td>
                                                        <td>
                                                            <button type="button" @click="removeBar(getIndex(counter, cID))" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="pfooter">
                                            <b>Sub Total</b>
                                            <input value="0.000" type="text"  v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="row customer-div">
                                        <div class="col-md-2 form-group">
                                            <label for="bill_copy" class="control-label">Invoice Bill</label>
                                            <input type="file" @change="uploadBillCopy" placeholder="upload invoice / bill" class="form-control" :class="{ 'is-invalid': form.errors.has('bill_copy') }">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="label-control">Discount Description</label>
                                                <input placeholder="discount description" class="form-control" type="text" v-model="form.discount_desc" name="discount_desc" id="discount_desc">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">Discounted Amount</label>
                                                <input :placeholder="'discount in '+$root.$data.currency" class="form-control" type="text" v-model="form.discount_amount" @change="reTotal" name="discount_amount" id="discount_amount">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="label-control">Payment Mode</label>
                                                <select class="form-control" v-model="form.payment_mode" name="payment_mode" :class="{ 'is-invalid': form.errors.has('payment_mode') }">
                                                    <option value="CASH">Cash</option>
                                                    <option value="BT">Bank Transfer</option>
                                                    <option value="CREDIT">Credit</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">Payment Description</label>
                                                <input placeholder="Transition number" class="form-control" type="text" v-model="form.payment_desc" name="payment_desc" id="payment_desc">
                                            </div>
                                        </div>
                                         <div class="col-md-2 form-group">
                                            <label class="label-control">Remark</label>
                                            <textarea class="form-control"  v-model="form.remark" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label class="label-control">Grand Total</label>
                                            <input value="0.000" class="form-control"  v-model="form.grand_total" type="text" name="payable_amount" id="payable_amount" readonly="readonly" :class="{ 'is-invalid': form.errors.has('grand_total') }">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-10">

                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-block btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="supplierModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Supplier Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>Name</b><br>{{ supplier.name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ supplier.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ supplier.contact_no }}</p></li>
                            <li><p class="alert m-0"><b>Company Name</b><br>{{ supplier.company_name }}</p></li>
                            <li><p class="alert m-0"><b>Connection Source</b><br>{{ supplier.connection_source }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ supplier.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ supplier.status }}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="productModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Medicine Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><img :src="imgurl+product.image" alt="" class="promodelimage"></p></li>
                            <li><p class="alert m-0"><b>Category</b><br>{{ product.category_name }}</p></li>
                            <li><p class="alert m-0"><b>Name</b><br>{{ product.name }}</p></li>
                            <li><p class="alert m-0"><b>Medicine Barcode</b><br>{{ product.barcode }}</p></li>
                            <li><p class="alert m-0"><b>packsize / Unitsize</b><br>{{ product.unitsize | freeNumber }} {{ product.unit | uppercase }}</p></li>
                            <li><p class="alert m-0"><b>Added On</b><br>{{ product.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Description</b><br>{{ (product.description ? product.description : '--') }}</p></li>
                        </ul>
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
                stocks: {},
                options: [],
                suppliers:[],
                supplier:[],
                sselected: '',
                countlist:[0,1,2],
                productSelected: [],
                product:[],
                products: [],
                limit: 10,
                imgurl: '../images/products/',
                form: new Form({
                    id:'',
                    supplier_id:'',
                    invoice_number:'',
                    shipment_number:'',
                    purchase_date:'',
                    bill_copy:'',
                    sub_total:'',
                    discount_desc:'',
                    discount_percent:'',
                    discount_amount:'',
                    tax_percent:'',
                    tax_amount:'',
                    grand_total:'',
                    remark:'',
                    shipment_number:'',
                    payment_type: 2,
                    payment_id:'',
                    payment_desc:'',
                    payment_mode:'CASH',
                    product_detail__id:[],
                    product_detail__purchase_cost:[],
                    product_detail__batch_no:[],
                    product_detail__stock:[],
                    product_detail__total:[],
                    product_detail__foc:[],
                    product_detail__exp_date:[]
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            addPurchase(){
                this.form.post('/api/stocks')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        type: 'success',
                        title: 'Purchase done.',
                        text: "Purchase has been completed successfully.",
                        confirmButtonColor: '#38c172',
                        confirmButtonText: 'ok'
                    }).then((result) => {
                        if (result.value) {
                            this.form.reset();
                            this.productSelected = [];
                            this.sselected = '';
                            this.countlist = [];
                            this.countlist = [1,2,3];
                            this.$router.push('/pharmacy/purchases');
                        }
                        else{

                        }
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            getAllAssets() {
                axios.get('/api/getMedicinesSelectList').then((data) => {this.products = data.data }).catch();
                axios.get('/api/getSuppliersSelectList').then((data) => {this.suppliers = data.data }).catch();
            },
            uploadBillCopy(e) {
                let file = e.target.files[0];
                if(file.type.split('/')[0] != 'image') {
                    swal.fire('Alert!', 'Please upload image file only.', 'error');
                    return false;
                }
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.bill_copy = reader.result;
                }
                reader.readAsDataURL(file);
            },
            reTotal(){
               this.updateSubTotal(); //this.addTax(); this.addDiscount();
               this.updateGrandTotal();
            },
            updateGrandTotal() {
                this.form.grand_total = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.discount_amount);
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                counter.forEach(element => {
                    let quantity = this.makeNumber(this.form.product_detail__stock[element]);
                    this.form.product_detail__total[element] = this.makeNumber(this.form.product_detail__purchase_cost[element]) * quantity
                    stotal = stotal +  this.form.product_detail__total[element];
                });
                this.form.sub_total = stotal;
            },
            /* addTax() {
                this.form.tax_amount = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.form.tax_percent)/100);
            },
            addDiscount() {
                this.form.discount_amount = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.form.discount_percent)/100);
            }, */
            addBar(){
                let bcount = this.countlist.length;
                this.countlist.push(bcount);
            },
            makeNumber(val) {
                if(isNaN(val)){
                    return 0;
                }
                else {
                    return parseFloat(val);
                }
            },
            removeBar(barkey){
                this.$delete(this.countlist, barkey);
                this.productSelected.splice(barkey, 1);
                this.form.product_detail__id.splice(barkey, 1);
                this.form.product_detail__purchase_cost.splice(barkey, 1);
                this.form.product_detail__batch_no.splice(barkey, 1);
                this.form.product_detail__stock.splice(barkey, 1);
                this.form.product_detail__foc.splice(barkey, 1);
                this.form.product_detail__total.splice(barkey, 1);
                this.form.product_detail__exp_date.splice(barkey, 1);
                this.reTotal();
            },
            selectSupplier() {
                this.sselected = this.form.supplier_id
            },
            supplierDetail() {
                let val = this.form.supplier_id;
                $('#supplierModal').modal('show');
                axios.get('/api/suppliers/'+val)
                    .then((data) => {this.supplier = data.data })
                    .catch();
            },
            productDetail(pid) {
                $('#productModal').modal('show');
                axios.get('/api/medicines/'+pid)
                    .then((data) => {this.product = data.data[0] })
                    .catch();
            },
            onProductSelect(product, key) {
               this.productSelected[key] = product;
            }
        },
        created() {
            this.getAllAssets();
        }
    }
</script>
