<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Diagnosis List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addXray"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
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
                                        <tr v-for="(diagnos, count) in diagnosis.data" :key="diagnos.id">
                                            <td>{{ (diagnosis.current_page - 1)*(diagnosis.per_page) + count + 1 }}</td>
                                            <td>{{ diagnos.code | uppercase }}</td>
                                            <td>{{ diagnos.value | capitalize}}</td>
                                            <td>{{ diagnos.created_at | setdate }}</td>
                                            <td align="center" v-if="diagnos.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editXray(diagnos)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteXray(diagnos.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="diagnosis.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="diagnosis" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="adddiagnosModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddiagnosModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateXray() : createXray()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="adddiagnosModalTitle">Add Diagnosis</h5>
                            <h5 class="modal-title" v-show="editmode" id="adddiagnosModalTitle">Update Diagnosis's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="value" class="control-label">Name</label>
                                    <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                    <has-error :form="form" field="value"></has-error>
                                </div>
                                 <div class="form-group  col-12">
                                        <label for="code" class="control-label">Code</label>
                                        <input v-model="form.code" type="text" name="code" id="code" placeholder="enter code"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                                        <has-error :form="form" field="code"></has-error>
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
                diagnosis: {},
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
                axios.get('/api/diagnosis?page=' + page)
                    .then(response => {
                        this.diagnosis = response.data;
                    });
            },
            showDiagnosis() {
                this.$Progress.start();
                axios.get('/api/diagnosis').then(({ data }) => {
                    this.diagnosis = data;
                    this.$Progress.finish();
                });
            },
            addXray() {
                this.editmode = false;
                this.form.reset();
                $('#adddiagnosModal').modal('show');
            },
            createXray() {
                this.form.post('/api/diagnosis')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiagnosModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Diagnosis created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateXray() {
                this.form.put('/api/diagnosis/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiagnosModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Diagnosis details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editXray(diagnos) {
                this.editmode = true;
                this.form.fill(diagnos);
                $('#adddiagnosModal').modal('show');
            },
            deleteXray(id) {
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
                        this.form.delete('/api/diagnosis/'+id)
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
            this.showDiagnosis();
            Fire.$on('AfterCreate', () => {
                this.showDiagnosis();
            });
        }
    }
</script>
