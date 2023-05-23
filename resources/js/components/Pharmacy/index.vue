<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                    <li class="breadcrumb-item active">Stock</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Stocks
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-primary mr-1"><i class="fas fa-download fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Product</th>
                                            <th>Product Code</th>
                                            <th>Batch No</th>
                                            <th>Barcode</th>
                                            <th>Packsize</th>
                                            <th>Selling Cost</th>
                                            <th>Puchasing Cost</th>
                                            <th>Stock</th>
                                            <th>Added On</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="stock in stocks.data" :key="stock.id">
                                            <td>{{ stock.id }}</td>
                                            <td>{{ stock.name }}</td>
                                            <td>{{ stock.model_no }}</td>
                                            <td>{{ stock.batch_no }}</td>
                                            <td>{{ stock.barcode }}</td>
                                            <td>{{ stock.packsize }}</td>
                                            <td>{{ stock.selling_cost | formatNumber }}</td>
                                            <td>{{ stock.purchase_cost | formatNumber }}</td>
                                            <td>{{ stock.stock | freeNumber }}</td>
                                            <td>{{ stock.created_at | setdate }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editStock(stock)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteStock(stock.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="stocks.total > 1">
                                <pagination class="m-0 float-right" :data="stocks" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editStockModal"  data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateStock()">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Stock</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="stock">Stock</label>
                                <input v-model="form.stock" type="number" name="stock" class="form-control" :class="{ 'is-invalid': form.errors.has('stock') }">
                                <has-error :form="form" field="name"></has-error>
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
                stocks: {},
                form: new Form({
                    id:'',
                    stock:''
                })
            }
        },
        methods: {
            getResults() {
                axios.get('/api/stocks?page=' + page)
                    .then(response => {
                        this.stocks = response.data;
                });
            },
            showStock() {
                axios.get('/api/stocks').then(({ data }) => {
                    this.stocks = data;
                    this.$Progress.finish();
                });
            },
            editStock(stock) {
                this.form.fill(stock);
                $('#editStockModal').modal('show');
            },
            updateStock() {
                this.form.put('/api/stocks/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#editStockModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Stock details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            deleteStock(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('/api/stocks/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            }
        },
        created() {
            Fire.$on('searching', () => {
                axios.get('/api/findStock?q='+this.$parent.search)
                    .then((data) => {
                        this.stocks = data.data;
                    })
                    .catch();
            });
            this.showStock();
            Fire.$on('AfterCreate', () => {
                this.showStock();
            });
        }
    }
</script>
