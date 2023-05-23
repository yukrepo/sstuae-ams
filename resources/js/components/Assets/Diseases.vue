<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Diseases</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Diseases List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addDisease"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(disease, count) in diseases.data" :key="disease.id">
                                            <td>{{ (diseases.current_page - 1)*(diseases.per_page) + count + 1 }}</td>
                                            <td>{{ disease.code | uppercase }}</td>
                                            <td>{{ disease.value }}</td>
                                            <td>{{ disease.created_at | setdate }}</td>
                                            <td align="center" v-if="disease.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editDisease(disease)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteDisease(disease.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="diseases.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="diseases" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="adddiseaseModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddiseaseModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateDisease() : createDisease()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="adddiseaseModalTitle">Add Disease</h5>
                            <h5 class="modal-title" v-show="editmode" id="adddiseaseModalTitle">Update Disease's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                        <has-error :form="form" field="value"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="code" class="control-label">Code</label>
                                        <input v-model="form.code" type="text" name="code" id="code" placeholder="enter code"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                                        <has-error :form="form" field="code"></has-error>
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
                diseases: {},
                form: new Form({
                    id:'',
                    value:'',
                    code:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/diseases?page=' + page)
                    .then(response => {
                        this.diseases = response.data;
                    });
            },
            showDiseases() {
                this.$Progress.start();
                axios.get('/api/diseases').then(({ data }) => {
                    this.diseases = data;
                    this.$Progress.finish();
                });
            },
            addDisease() {
                this.editmode = false;
                this.form.reset();
                $('#adddiseaseModal').modal('show');
            },
            createDisease() {
                this.form.post('/api/diseases')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiseaseModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Disease created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateDisease() {
                this.form.put('/api/diseases/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiseaseModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Disease details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editDisease(disease) {
                this.editmode = true;
                this.form.fill(disease);
                $('#adddiseaseModal').modal('show');
            },
            deleteDisease(id) {
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
                        this.form.delete('/api/diseases/'+id)
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
            this.showDiseases();
            Fire.$on('AfterCreate', () => {
                this.showDiseases();
            });
        }
    }
</script>
