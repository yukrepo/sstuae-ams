<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row synop mb-3">
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-primary"><i class="fa fa-2x fa-building"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Appointments</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-success"><i class="fa fa-2x fa-dollar-sign"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Medicines</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-warning"><i class="fa fa-2x fa-dollar-sign"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Treatments</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-danger"><i class="fa fa-2x fa-user"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Customers</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-primary"><i class="fa fa-2x fa-building"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Invoices</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-success"><i class="fa fa-2x fa-dollar-sign"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Doctors</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-warning"><i class="fa fa-2x fa-dollar-sign"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Accounts</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12 m-b-20">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="square square-danger"><i class="fa fa-2x fa-user"></i></div>
                                    <div class="m-l-15 m-t-5 align-self-center">
                                        <h5 class="m-b-5 font-light">Suppliers</h5>
                                        <h3 class="text-muted m-b-0">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                synops: '',
                products: '',

            }
        },
        methods: {
            getResults() {
                axios.get('api/dashboard').then(({ data }) => (this.synops = data));
            },
            showProducts() {
                this.$Progress.start();
                this.products = '';
               // axios.get('api/dashboard/stocks').then(({ data }) => (this.products = data));
                this.$Progress.finish();
            },
           /*  updateProduct() {
                this.editform.put('api/product_details/'+this.editform.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    this.showProducts();
                    $('#editproductModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'product details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editProduct(pdid) {
                axios.get('api/product_details/'+pdid).then(({ data }) => {
                    this.editform.fill(data);
                    $('#editproductModal').modal('show');
                });
            }, */
        },
        created() {
         //   this.getResults();
         //   this.showProducts();
            //setInterval(() => { this.getResults()}, 3000);
        }
    }
</script>
