<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9 py-1">
                        <vue-search placer="search by mobile, name, identity, email, patient ID"></vue-search>
                    </div>
                    <div class="col-md-3 text-right py-1">
                        <a href="/customers/add" class="btn btn-sm btn-wide btn-success"> <i class="fas fa-plus "></i>  New Customer</a>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Customers List </h3>
                            </div>
                            <div class="card-body p-0 table-responsive content-box-200">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-60">SNo</th>
                                            <th>M/F</th>
                                            <th>Source</th>
                                            <th>Patient ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Mobile</th>
                                            <th>D.O.B.</th>
                                            <th class="wf-100">Joined On</th>
                                            <th class="wf-125">Consent Form</th>
                                            <th class="wf-100 text-center">Status</th>
                                            <th class="wf-150">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(customer, sn) in customers.data" :key="customer.id">
                                            <td>{{ (customers.current_page - 1)*(customers.per_page) + sn + 1 }}
                                                <span class="m-l-5 text-theme" v-show="customer.device_type"> <i class="fa fa-mobile-alt"></i> </span>
                                            </td>
                                            <td v-if="customer.gender == 1">
                                                    <img class="img-icon" :src="svgurl+'boy.svg'" alt="Male">
                                            </td>
                                            <td v-else-if="customer.gender == 2">
                                                    <img class="img-icon" :src="svgurl+'girl.svg'" alt="Female">
                                            </td>
                                            <td v-else>
                                                    --
                                            </td>
                                            <td> <span class="text-uppercase font-weight-bold"> {{ customer.source }} </span></td>
                                            <td>{{ customer.username }}</td>
                                            <td>{{ customer.first_name | capitalize }}</td>
                                            <td>{{ customer.last_name | capitalize }}</td>
                                            <td>{{ customer.mobile }}</td>
                                            <td>{{ customer.dob | setdate }}</td>
                                            <td>{{ customer.created_at | setdate }}</td>
                                            <td align="center">
                                                <label class="status-label-full bg-success" v-if="customer.sign">Submitted</label>
                                                <label class="status-label-full bg-danger" v-else>Pending</label>
                                            </td>
                                            <td align="center">
                                                <label class="status-label-full" :class="customer.status_css">{{  customer.status }}</label>
                                            </td>
                                            <td>
                                                <a class="btn btn-dark btn-sm" target="_blank" :href="'/customers/download-history/'+customer.id"><i class="fas fa-file-pdf"></i></a>
                                                <a class="btn btn-primary btn-sm" :href="'/customers/view/'+customer.id"><i class="fas fa-desktop"></i></a>
                                                <a class="btn btn-warning btn-sm" :href="'/customers/edit/'+customer.id"><i class="fas fa-edit"></i></a>
                                                <button class="btn btn-danger btn-sm" @click="deleteCustomer(customer.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="customers.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="customers" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
                                    <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
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
    export default {
        data() {
            return {
                svgurl: '/svg/',
                editmode: false,
                customers: {},
                form: new Form({
                    id:'',
                    name:'',
                    contact_no:'',
                    email: '',
                    designation_type_id:'',
                    location_id:'',
                    qualification:'',
                    about:'',
                    status_id:''
                }),
                asearch:''
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/customers?page=' + page)
                    .then(response => {
                        this.customers = response.data;
                    });
            },
            showCustomers() {
                this.$Progress.start();
                axios.get('/api/customers').then(({ data }) => {
                    this.customers = data;
                    this.$Progress.finish();
                });
            },
            addCustomer() {
                this.editmode = false;
                this.form.reset();
                $('#addcustomerModal').modal('show');
            },
            createCustomer() {
                this.form.post('/api/customers')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcustomerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Customer created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateCustomer() {
                this.form.put('/api/customers/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcustomerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Customer details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editCustomer(customer) {
                this.editmode = true;
                this.form.fill(customer);
                $('#addcustomerModal').modal('show');
            },
            deleteCustomer(id) {
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
                        this.form.delete('/api/customers/'+id)
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
                axios.get('/api/findCustomer?q='+this.asearch)
                    .then((data) => {
                        this.customers = data.data;
                    })
                    .catch();
            });
            this.showCustomers();
            Fire.$on('AfterCreate', () => {
                this.showCustomers();
            });
        }
    }
</script>
