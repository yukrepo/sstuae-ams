<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Laboratories List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addLaboratory"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Category</th>
                                            <th>Name</th>
                                            <th>Lab Range</th>
                                            <th>Remark</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(laboratory, count) in laboratories.data" :key="laboratory.id">
                                            <td>{{ (laboratories.current_page - 1)*(laboratories.per_page) + count + 1 }}</td>
                                            <td>{{ laboratory.category }}</td>
                                            <td>{{ laboratory.value }}</td>
                                            <td>{{ laboratory.lab_range }}</td>
                                            <td>{{ laboratory.remark }}</td>
                                            <td>{{ laboratory.created_at | setdate }}</td>
                                            <td align="center" v-if="laboratory.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editLaboratory(laboratory)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteLaboratory(laboratory.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="laboratories.total > 1">
                                <pagination class="m-0 float-right" :limit=10 :data="laboratories" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addlaboratoryModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addlaboratoryModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateLaboratory() : createLaboratory()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addlaboratoryModalTitle">Add Laboratory</h5>
                            <h5 class="modal-title" v-show="editmode" id="addlaboratoryModalTitle">Update Laboratory's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                         <label for="lab_category_id" class="control-label">Category</label>
                                        <select v-model="form.lab_category_id" name="lab_category_id" class="form-control" :class="{ 'is-invalid': form.errors.has('lab_category_id') }">
                                            <option disabled value="">Select Category</option>
                                            <option v-for="category in categories" :key="category.id" v-bind:value="category.id">
                                                {{ category.value }}
                                            </option>
                                        </select>
                                        <has-error :form="form" field="lab_category_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="form.value" type="text" name="value" id="value" placeholder="enter name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                        <has-error :form="form" field="value"></has-error>
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
                                </div>
                                <div class="col-6">
                                   <div class="form-group">
                                        <label for="lab_range" class="control-label">lab range</label>
                                        <textarea v-model="form.lab_range" name="lab_range" rows="4" id="lab_range" placeholder="enter lab range"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('lab_range') }"></textarea>
                                        <has-error :form="form" field="lab_range"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea v-model="form.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                        <has-error :form="form" field="remark"></has-error>
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
                categories: {},
                laboratories: {},
                form: new Form({
                    id:'',
                    value:'',
                    lab_category_id:'',
                    lab_range: '',
                    remark:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/laboratories?page=' + page)
                    .then(response => {
                        this.laboratories = response.data;
                    });
            },
            showLaboratories() {
                this.$Progress.start();
                axios.get('/api/laboratories').then(({ data }) => {
                    this.laboratories = data;
                    this.$Progress.finish();
                });
            },
            getCategories() {
                axios.get('/api/getLabCategoryList').then(({ data }) => (this.categories = data));
            },
            addLaboratory() {
                this.editmode = false;
                this.form.reset();
                $('#addlaboratoryModal').modal('show');
            },
            createLaboratory() {
                this.form.post('/api/laboratories')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlaboratoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Laboratory created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateLaboratory() {
                this.form.put('/api/laboratories/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlaboratoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Laboratory details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editLaboratory(laboratory) {
                this.editmode = true;
                this.form.fill(laboratory);
                $('#addlaboratoryModal').modal('show');
            },
            deleteLaboratory(id) {
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
                        this.form.delete('/api/laboratories/'+id)
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
            this.getCategories();
            this.showLaboratories();
            Fire.$on('AfterCreate', () => {
                this.showLaboratories();
            });
        }
    }
</script>
