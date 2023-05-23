<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Oral Examination Categories List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addOeCategory"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 85px;">SNo</th>
                                            <th>Name</th>
                                            <!-- <th>Code</th> -->
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(oecategory, count) in oecategories.data" :key="oecategory.id">
                                             <td>{{ (oecategories.current_page - 1)*(oecategories.per_page) + count + 1 }}</td>
                                            <td>{{ oecategory.value | capitalize}}</td>
                                            <!-- <td>{{ oecategory.code }}</td> -->
                                            <td>{{ oecategory.created_at | setdate }}</td>
                                            <td align="center" v-if="oecategory.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editOeCategory(oecategory)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteOeCategory(oecategory.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="oecategories.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="oecategories" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addoecategoryModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addoecategoryModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateOeCategory() : createOeCategory()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addoecategoryModalTitle">Add OeCategory</h5>
                            <h5 class="modal-title" v-show="editmode" id="addoecategoryModalTitle">Update OeCategory's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="value" class="control-label">name</label>
                                    <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                    <has-error :form="form" field="value"></has-error>
                                </div>
                                <div class="form-group col-6">
                                   <!--  <label for="code" class="control-label">Code</label>
                                    <input v-model="form.code" type="text" name="code" id="code" placeholder="enter code"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                                    <has-error :form="form" field="code"></has-error> -->
                                </div>
                                <div class="form-group col-6">
                                    <label for="status_id" class="control-label">Status</label>
                                    <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                        <option value="" disabled>Select Status</option>
                                        <option value="2">Active</option>
                                        <option value="3">Deactive</option>
                                    </select>
                                    <has-error :form="form" field="status_id"></has-error>
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
                oecategories: {},
                form: new Form({
                    id:'',
                    value:'',
                    code:'',
                    status_id:2
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/oe-categories?page=' + page)
                    .then(response => {
                        this.oecategories = response.data;
                    });
            },
            showOeCategories() {
                this.$Progress.start();
                axios.get('/api/oe-categories').then(({ data }) => {
                    this.oecategories = data;
                    this.$Progress.finish();
                });
            },
            addOeCategory() {
                this.editmode = false;
                this.form.reset();
                $('#addoecategoryModal').modal('show');
            },
            createOeCategory() {
                this.form.post('/api/oe-categories')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addoecategoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Oe Category created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateOeCategory() {
                this.form.put('/api/oe-categories/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addoecategoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Oe Category details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editOeCategory(oecategory) {
                this.editmode = true;
                this.form.fill(oecategory);
                $('#addoecategoryModal').modal('show');
            },
            deleteOeCategory(id) {
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
                        this.form.delete('/api/oe-categories/'+id)
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
            this.showOeCategories();
            Fire.$on('AfterCreate', () => {
                this.showOeCategories();
            });
        }
    }
</script>
