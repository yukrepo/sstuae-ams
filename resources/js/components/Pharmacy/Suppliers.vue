<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Medicine Suppliers
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addSupplier"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Company Name</th>
                                            <th>Supplier Name</th>
                                            <th>Contact Person</th>
                                            <th>Contact No</th>
                                            <th>Email</th>
                                            <th>City</th>
                                            <th>Address</th>
                                            <th>Joined</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="supplier in suppliers.data" :key="supplier.id">
                                            <td>{{ supplier.id }}</td>
                                            <td>{{ supplier.company_name }}</td>
                                            <td>{{ supplier.name }}</td>
                                            <td>{{ supplier.connection_source }}</td>
                                            <td>{{ supplier.contact_no }}</td>
                                            <td>{{ supplier.email }}</td>
                                            <td>{{ supplier.city }}</td>
                                            <td>{{ supplier.address }}</td>
                                            <td>{{ supplier.created_at | setdate }}</td>
                                            <td align="center" v-if="supplier.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editSupplier(supplier)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteSupplier(supplier.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="suppliers.total > 1">
                                <pagination class="m-0 float-right" :data="suppliers" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addsupplierModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addsupplierModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateSupplier() : createSupplier()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addsupplierModalTitle">Add Supplier</h5>
                            <h5 class="modal-title" v-show="editmode" id="addsupplierModalTitle">Update Supplier's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Supplier name</label>
                                        <input v-model="form.name" type="text" name="name" placeholder="enter full name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="company_name" class="control-label">company name</label>
                                        <input v-model="form.company_name" type="text" name="company_name" placeholder="enter company name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('company_name') }">
                                        <has-error :form="form" field="company_name"></has-error>
                                    </div>
                                     <div class="form-group">
                                        <label for="connection_source" class="control-label">Local Contact Person</label>
                                        <input v-model="form.connection_source" type="text" name="connection_source" placeholder="enter local person name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('connection_source') }">
                                        <has-error :form="form" field="connection_source"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_no" class="control-label">contact no</label>
                                        <input v-model="form.contact_no" type="text" name="contact_no" placeholder="enter mobile number"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('contact_no') }">
                                        <has-error :form="form" field="contact_no"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="control-label">email</label>
                                        <input v-model="form.email" type="email" name="email" placeholder="enter email"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="city" class="control-label">city</label>
                                        <input v-model="form.city" type="text" name="city" placeholder="enter city name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('city') }">
                                        <has-error :form="form" field="city"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="control-label">address</label>
                                        <textarea v-model="form.address" name="address" placeholder="enter address"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('address') }"></textarea>
                                        <has-error :form="form" field="address"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="remark" class="control-label">remark</label>
                                        <textarea v-model="form.remark" name="remark" placeholder="enter remark"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                        <has-error :form="form" field="remark"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="status_id" class="control-label">Status</label>
                                        <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                            <option disabled value="">Select Status</option>
                                            <option value="2">Active</option>
                                            <option value="3">Deactive</option>
                                        </select>
                                        <has-error :form="form" field="status_id"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button v-show="!editmode" type="submit" class="btn btn-wide btn-success"> Create </button>
                            <button v-show="editmode" type="submit" class="btn btn-wide btn-success"> Update </button>
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
                suppliers: {},
                form: new Form({
                    id:'',
                    name:'',
                    email:'',
                    contact_no:'',
                    city: '',
                    address:'',
                    connection_source:'',
                    status_id:'',
                    remark:'',
                    company_name: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/suppliers?page=' + page)
                    .then(response => {
                        this.suppliers = response.data;
                    });
            },
            showSuppliers() {
                this.$Progress.start();
                axios.get('/api/suppliers').then(({ data }) => {
                    this.suppliers = data;
                    this.$Progress.finish();
                });
            },
            addSupplier() {
                this.editmode = false;
                this.form.reset();
                $('#addsupplierModal').modal('show');
            },
            createSupplier() {
                this.form.post('/api/suppliers')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addsupplierModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Supplier created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateSupplier() {
                this.form.put('/api/suppliers/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addsupplierModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Supplier details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editSupplier(supplier) {
                this.editmode = true;
                this.form.fill(supplier);
                $('#addsupplierModal').modal('show');
            },
            deleteSupplier(id) {
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
                        this.form.delete('/api/suppliers/'+id)
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
            this.showSuppliers();
            Fire.$on('AfterCreate', () => {
                this.showSuppliers();
            });
        }
    }
</script>
