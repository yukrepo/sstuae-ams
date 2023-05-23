<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Symptoms List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addSymptom"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 85px;">#SNo</th>
                                            <th>Name</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(symptom, count) in symptoms.data" :key="symptom.id">
                                            <td>{{ (symptoms.current_page - 1)*(symptoms.per_page) + count + 1 }}</td>
                                            <td>{{ symptom.value | capitalize}}</td>
                                            <td>{{ symptom.created_at | setdate }}</td>
                                            <td align="center" v-if="symptom.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editSymptom(symptom)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteSymptom(symptom.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="symptoms.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="symptoms" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addsymptomModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addsymptomModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateSymptom() : createSymptom()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addsymptomModalTitle">Add Symptom</h5>
                            <h5 class="modal-title" v-show="editmode" id="addsymptomModalTitle">Update Symptom's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-12">
                                        <label for="value" class="control-label">Name</label>
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
                symptoms: {},
                form: new Form({
                    id:'',
                    value:'',
                    code:'',
                    status_id:'2'
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/symptoms?page=' + page)
                    .then(response => {
                        this.symptoms = response.data;
                    });
            },
            showSymptoms() {
                this.$Progress.start();
                axios.get('/api/symptoms').then(({ data }) => {
                    this.symptoms = data;
                    this.$Progress.finish();
                });
            },
            addSymptom() {
                this.editmode = false;
                this.form.reset();
                $('#addsymptomModal').modal('show');
            },
            createSymptom() {
                this.form.post('/api/symptoms')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addsymptomModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Symptom created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateSymptom() {
                this.form.put('/api/symptoms/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addsymptomModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Symptom details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editSymptom(symptom) {
                this.editmode = true;
                this.form.fill(symptom);
                $('#addsymptomModal').modal('show');
            },
            deleteSymptom(id) {
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
                        this.form.delete('/api/symptoms/'+id)
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
            this.showSymptoms();
            Fire.$on('AfterCreate', () => {
                this.showSymptoms();
            });
        }
    }
</script>
