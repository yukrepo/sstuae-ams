<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories List
                                <div class="card-tools">
                                    <!-- <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addAdminType"><i class="fas fa-plus fa-fw"></i></button> -->
                                </div>
                            </h3>
                        </div>
                        <div class="card-body p-0 content-box-180">
                            <table id="stock-alert" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="wf-50">SNo</th>
                                        <th>Value</th>
                                        <th>Permissions</th>
                                        <th class="wf-100">Added On</th>
                                        <th class="wf-50">Status</th>
                                        <th class="wf-50">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(admin_type, count) in admin_types.data" :key="admin_type.id">
                                        <td>{{ (admin_types.current_page - 1)*(admin_types.per_page) + count + 1 }}</td>
                                        <td>{{ admin_type.type | capitalize }}</td>
                                        <td>{{ admin_type.permissions }}</td>
                                        <td>{{ admin_type.created_at | setdate }}</td>
                                        <td align="center" v-if="admin_type.status_id == 2">
                                            <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                        </td>
                                        <td align="center" v-else>
                                            <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                        </td>
                                        <td>
                                            <span  v-if="admin_type.id > 3">
                                                <button class="btn btn-warning btn-sm" @click="editAdminType(admin_type)"><i class="fas fa-edit"></i></button>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer" v-if="admin_types.total > 1">
                            <pagination class="m-0 float-right" :data="admin_types" @pagination-change-page="getResults" :show-disabled="true">
                                <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addadmin_typeModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addadmin_typeModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateAdminType() : createAdminType()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addadmin_typeModalTitle">Add New Admin Category</h5>
                            <h5 class="modal-title" v-show="editmode" id="addadmin_typeModalTitle">Update Admin's Category</h5>
                        </div>
                        <div class="modal-body">
                            <div class="">
                                <div class="form-group">
                                    <label for="type" class="control-label">type</label>
                                    <input v-model="form.type" type="text" name="type" id="type" placeholder="enter type name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <has-error :form="form" field="type"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="status_id" class="control-label">Status</label>
                                    <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                        <option value="" disabled>Select Status</option>
                                        <option value="2">Active</option>
                                        <option value="3">Deactive</option>
                                    </select>
                                    <has-error :form="form" field="status_id"></has-error>
                                </div>
                                <!-- <div class="form-group col-12">
                                    <label for="permissions" class="control-label">Permissions</label>
                                </div> -->
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
                admin_types: {},
                form: new Form({
                    id:'',
                    type:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/admin-types?page=' + page)
                    .then(response => {
                        this.admin_types = response.data;
                    });
            },
            showAdminTypes() {
                this.$Progress.start();
                axios.get('/api/admin-types').then(({ data }) => {
                    this.admin_types = data;
                    this.$Progress.finish();
                });
            },
            addAdminType() {
                this.editmode = false;
                this.form.reset();
                $('#addadmin_typeModal').modal('show');
            },
            createAdminType() {
                this.form.post('/api/admin-types')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addadmin_typeModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Admin Category created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {
                    swal.fire({
                        type:'error',
                        title:'Not done',
                        text: 'Your request can not be completed. Please try again'
                    });
                });
            },
            updateAdminType() {
                this.form.put('/api/admin-types/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addadmin_typeModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Admin Category updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editAdminType(admin_type) {
                this.editmode = true;
                this.form.fill(admin_type);
                $('#addadmin_typeModal').modal('show');
            },
            deleteAdminType(id) {
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
                        this.form.delete('/api/admin-types/'+id)
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
            this.showAdminTypes();
            Fire.$on('AfterCreate', () => {
                this.showAdminTypes();
            });
        }
    }
</script>
