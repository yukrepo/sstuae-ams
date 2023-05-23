 789 ,hy6767koo<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Purchases
                                    <div class="card-tools">
                                        <a class="btn btn-sm btn-dark" href="/pharmacy/purchases">
                                            <i class="fas fa-arrow-left fa-fw"></i> Back To List
                                        </a>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="purchase" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Invoice No</th>
                                            <th>Shipment No</th>
                                            <th>Supplier</th>
                                            <th>No Of Stocks</th>
                                            <th>Purchase Date</th>
                                            <th>Total value</th>
                                            <th>Payment Mode</th>
                                            <th>Payment Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ purchase.invoice_number }}</td>
                                            <td>{{ purchase.shipment_number }}</td>
                                            <td>{{ purchase.supplier_name }}</td>
                                            <td>
                                                <label class="btn btn-primary btn-sm m-b-0">
                                                    {{ purchase.stocks_count }}
                                                </label>
                                            </td>
                                            <td>{{ purchase.purchase_date | setdate }}</td>
                                            <td>{{ purchase.grand_total | formatOMR }}</td>
                                            <td>{{ purchase.firstpay_mode }}</td>
                                            <td>
                                                <label class="btn btn-success btn-sm m-b-0" v-if="purchase.pay_status == 1">
                                                    PAID
                                                </label>
                                                <label class="btn btn-danger btn-sm m-b-0" v-else>
                                                    PENDING
                                                </label>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                                <hr class="m-b-0 m-t-25">
                                <form @submit.prevent="editmode ? updateStock() : ''">
                                    <table id="stock-alert" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="wf-50">SNo</th>
                                                <th>Medicine</th>
                                                <th>Batch No</th>
                                                <th>Exp Date</th>
                                                <th>P.Cost</th>
                                                <th>Total Stock</th>
                                                <th>Available</th>
                                                <th>FOC</th>
                                                <th>Adjustment</th>
                                                <th>Added On</th>
                                                <th class="wf-100">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(stock, count) in stocks.data" :key="stock.id" :class="[(editmode && stock.id == form.id)? 'edit-line' : '' ]">
                                                <td>{{ count + 1 }}</td>
                                                <td>{{ stock.name }}</td>
                                                <td>{{ stock.batch_no | uppercase }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.exp_date" :auto-submit="true" :input-class="form.errors.has('exp_date') ?'form-control is-invalid' : 'form-control'"></vp-date-picker>
                                                </td>
                                                <td v-else>
                                                    {{ stock.exp_date | setdate }}
                                                </td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <input type="text" class="form-control" placeholder="modified price" v-model="form.purchase_cost">
                                                </td>
                                                <td v-else>
                                                    {{ stock.purchase_cost | formatNumber }}
                                                </td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 1)">
                                                    <input type="text" class="form-control" placeholder="modified stock" v-model="form.received_stock">
                                                </td>
                                                <td v-else>
                                                    {{ stock.received_stock | freeNumber }}
                                                </td>
                                                <td>{{ stock.stock | freeNumber }}</td>
                                                <td>{{ stock.foc | freeNumber }}</td>
                                                <td v-if="(editmode && stock.id == form.id && form.type == 2)">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon2">{{ stock.adjustments }}    <i class="fas fa-plus m-l-5"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control" placeholder="additional adjusted stock" v-model="form.adjustments">
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="enter reason" v-model="form.reason" :class="{ 'is-invalid': form.errors.has('reason') }">
                                                </td>
                                                <td v-else>{{ stock.adjustments | freeNumber }}</td>
                                                <td>{{ stock.created_at | setdate }}</td>
                                                <td>
                                                    <span  v-if="(editmode && stock.id == form.id)">
                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm" @click="cancelEdit"><i class="fas fa-times"></i></button>
                                                    </span>
                                                    <span v-else>
                                                        <button type="button" class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                                        <button  type="button" class="btn btn-purple btn-sm" @click="makeAdjustment(stock)"><i class="fas fa-exchange-alt"></i></button>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="updateFormModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Images</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="imgaes">Select All Images</label>
                                <input type="file" @change="uploadMImage" name="image" multiple placeholder="select image" class="form-control">
                                <hr>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="button" @click="updateAllImage()" class="btn btn-wide btn-success"> Upload </button>
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
                activeId: '',
                purchase: {},
                stocks:{},
                form: new Form({
                    id:'',
                    type:'',
                    purchase_cost:'',
                    received_stock:'',
                    adjustments:'',
                    exp_date:'',
                    reason:''
                })
            }
        },
        methods: {
            showPurchase() {
                this.$Progress.start();
                axios.get('/api/purchases/'+this.activeId).then((data) => { this.purchase = data.data; });
                axios.get('/api/get-stocks-by-purchase/'+this.activeId).then((data) => { this.stocks = data; });
                this.$Progress.finish();
            },
            viewStocks() {
                axios.get('api/stocks/'+pid)
                    .then((data) => {this.stocks = data.data })
                    .catch();
            },
            uploadMImage(e) {
                let vm = this.form;
                //let reader = new FileReader();
                var files = e.target.files;
               // var vm = this;   // HERE
                if(files){
                    var files_count = files.length;
                    for (let i=0; i<files_count; i++){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        vm.images.push(e.target.result);    // HERE
                    }
                    reader.readAsDataURL(files[i]);
                    }
                }
            },
            updateStock() {
                this.form.post('/api/stocks/'+this.form.id)
                .then((response) => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    this.editmode = false;
                    toast.fire({type:'success', title:'Stock details has been updated successfully.'});
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
            uploadImage(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.image = reader.result;
                }
                reader.readAsDataURL(file);
            },
            updateStock() {
                this.$Progress.start();
                this.form.put('/api/stocks/'+this.form.id)
                .then(() => {
                    axios.get('/api/get-stocks-by-purchase/'+this.activeId).then((data) => { this.stocks = data; });
                    this.editmode = false;
                    this.form.reset();
                    toast.fire({type:'success',   title:'Stock details updated successfully'});
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            setPurchase() {
                 let actiivepur = this.$route.path.split("/");
                this.activeId = actiivepur[3];
            }
        },
        beforeMount() {
            this.setPurchase();
        },
        mounted() {
            this.showPurchase();
            Fire.$on('AfterCreate', () => {
                this.showPurchase();
            });
        }
    }
</script>
