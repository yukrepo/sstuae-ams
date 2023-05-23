<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Lab Categories List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addLabCategory"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Value</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(lab_category, count) in lab_categories.data" :key="lab_category.id">
                                            <td>{{ (lab_categories.current_page - 1)*(lab_categories.per_page) + count + 1 }}</td>
                                            <td>{{ lab_category.value }}</td>
                                            <td>{{ lab_category.created_at | setdate }}</td>
                                            <td align="center" v-if="lab_category.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editLabCategory(lab_category)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteLabCategory(lab_category.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="lab_categories.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="lab_categories" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addlab_categoryModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addlab_categoryModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateLabCategory() : createLabCategory()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addlab_categoryModalTitle">Add LabCategory</h5>
                            <h5 class="modal-title" v-show="editmode" id="addlab_categoryModalTitle">Update LabCategory's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="value" class="control-label">Value</label>
                                    <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                    <has-error :form="form" field="value"></has-error>
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
                lab_categories: {},
                form: new Form({
                    id:'',
                    value:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/lab-categories?page=' + page)
                    .then(response => {
                        this.lab_categories = response.data;
                    });
            },
            showLabCategories() {
                this.$Progress.start();
                axios.get('/api/lab-categories').then(({ data }) => {
                    this.lab_categories = data;
                    this.$Progress.finish();
                });
            },
            addLabCategory() {
                this.editmode = false;
                this.form.reset();
                $('#addlab_categoryModal').modal('show');
            },
            createLabCategory() {
                this.form.post('/api/lab-categories')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlab_categoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Lab Category created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateLabCategory() {
                this.form.put('/api/lab-categories/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlab_categoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Lab Category details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editLabCategory(lab_category) {
                this.editmode = true;
                this.form.fill(lab_category);
                $('#addlab_categoryModal').modal('show');
            },
            deleteLabCategory(id) {
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
                        this.form.delete('/api/lab-categories/'+id)
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
            this.showLabCategories();
            Fire.$on('AfterCreate', () => {
                this.showLabCategories();
            });
        }
    }
</script>
