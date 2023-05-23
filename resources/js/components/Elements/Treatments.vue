<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" v-model="sform.keyword" placeholder="Enter Something">
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.appointment_type_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Treatment Type</option>
                                <option value="1">Consultation</option>
                                <option value="2">Treatment</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.status_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Status</option>
                                <option value="3">Deactive</option>
                                <option value="2">Active</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.is_it_dual" class="form-control" @change="makeSearch">
                                <option disabled value="">Is It Dual?</option>
                                <option value="0">Not Dual</option>
                                <option value="2">Dual With Doctors</option>
                                <option value="1">Dual With Therapists</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                        </div>
                        <div class="col-md-1 text-right">
                            <button class="btn btn-sm btn-danger" @click="clearFilter" type="button">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Treatments List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addTreatment"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-200">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-60">ID</th>
                                            <th>Treatment Type</th>
                                            <th>Treatment</th>
                                            <th>Cost <small>( <b>{{ $root.$data.currency }}</b> )</small> </th>
                                            <th>Timing</th>
                                            <th>Dual</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Added On</th>
                                            <th class="wf-60 text-center">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(treatment, ikey) in treatments.data" :key="treatment.id">
                                            <td>{{ (treatments.current_page - 1)*(treatments.per_page) + ikey + 1 }}</td>
                                            <td>{{ treatment.appointment_type }}</td>
                                            <td>{{ treatment.treatment }}</td>
                                            <td>{{ treatment.cost | formatOMR }}</td>
                                            <td>{{ treatment.timing | freeNumber }} Mins</td>
                                            <td v-if="treatment.is_it_dual == 1">
                                                <label class="status-label bg-teal">T</label>
                                            </td>
                                            <td v-else-if="treatment.is_it_dual == 2">
                                                <label class="status-label bg-teal">D</label>
                                            </td>
                                            <td v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>{{ (treatment.remark)?treatment.remark.substr(0, 20):'--' }}</td>
                                            <td>{{ treatment.created_at | setdate }}</td>
                                            <td align="center" v-if="treatment.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editTreatment(treatment)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteTreatment(treatment.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="treatments.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="treatments" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addtreatmentModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addtreatmentModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateTreatment() : createTreatment()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addtreatmentModalTitle">Add Treatment</h5>
                            <h5 class="modal-title" v-show="editmode" id="addtreatmentModalTitle">Update Treatment</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="appointment_type_id" class="control-label">Appointment Type</label>
                                    <select v-model="form.appointment_type_id" name="appointment_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('appointment_type_id') }">
                                        <option disabled value="">Select Appointment Type</option>
                                        <option v-for="appointment_type in appointment_types" :key="appointment_type.id" v-bind:value="appointment_type.id">
                                            {{ appointment_type.appointment_type }}
                                        </option>
                                    </select>
                                    <has-error :form="form" field="appointment_type_id"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="treatment" class="control-label">Name</label>
                                    <input v-model="form.treatment" type="text" name="treatment" id="treatment" placeholder="enter treatment name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('treatment') }">
                                    <has-error :form="form" field="treatment"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="timing" class="control-label">Timings</label>
                                    <input v-model="form.timing" type="text" name="timing" id="timing" placeholder="enter timing in mins"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('timing') }">
                                    <has-error :form="form" field="timing"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="cost" class="control-label">Cost ({{ $root.$data.currency }})</label>
                                    <input v-model="form.cost" type="number" name="cost" id="cost" :placeholder="'enter cost in '+ $root.$data.currency"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('cost') }">
                                    <has-error :form="form" field="cost"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="remark" class="control-label">Remark</label>
                                    <textarea v-model="form.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                    <has-error :form="form" field="remark"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <div class="form-group">
                                        <label for="status_id" class="control-label">Status</label>
                                        <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                            <option disabled value="">Select Status</option>
                                            <option value="2">Active</option>
                                            <option value="3">Deactive</option>
                                        </select>
                                        <has-error :form="form" field="status_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="is_it_dual" class="control-label">Type Of Dual Therapy</label>
                                        <select v-model="form.is_it_dual" name="is_it_dual" id="is_it_dual" class="form-control" :class="{ 'is-invalid': form.errors.has('is_it_dual') }">
                                            <option value="0">Not A Dual Therapy</option>
                                            <option value="1">With Therapists</option>
                                            <option value="2">With Doctors</option>
                                        </select>
                                        <has-error :form="form" field="is_it_dual"></has-error>
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
                treatments: {},
                appointment_types: {},
                loader:false,
                loaderurl:'/svg/loop.gif',
                form: new Form({
                    id:'',
                    treatment:'',
                    appointment_type_id:'',
                    is_it_dual:'',
                    timing:'',
                    cost:'',
                    remark:'',
                    status_id:''
                }),
                sform: new Form({
                    search:'',
                    appointment_type_id:'',
                    status_id:'',
                    keyword:'',
                    is_it_dual:''
                })
            }
        },
        methods: {
            clearFilter() {
                this.sform.reset();
                this.showTreatments();
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                this.treatments = {};
                this.sform.post('/api/findTreatments')
                    .then(({data}) => {
                        this.treatments = data;
                        this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            },
            setActive(menuItem) {
                this.activeMenu = menuItem; // no need for Vue.set()
                console.log(menuItem);
            },
            getAppointmentTypes() {
                axios.get('/api/getAppointmentTypesList').then(({ data }) => (this.appointment_types = data));
            },
            getResults(page = 1) {
                if(this.sform.search == 1) {
                    this.sform.post('/api/findTreatments?page=' + page)
                        .then(data => {
                            this.treatments = data.data;
                        });
                } else {
                    axios.get('/api/treatments?page=' + page)
                        .then(response => {
                            this.treatments = response.data;
                    });
                }
            },
            showTreatments() {
                this.$Progress.start();
                axios.get('/api/treatments').then(({ data }) => {
                    this.treatments = data;
                    this.$Progress.finish();
                });
            },
            addTreatment() {
                this.editmode = false;
                this.form.reset();
                $('#addtreatmentModal').modal('show');
            },
            createTreatment() {
                this.form.post('/api/treatments')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addtreatmentModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Treatment created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateTreatment() {
                this.form.put('/api/treatments/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addtreatmentModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Treatment details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editTreatment(treatment) {
                this.editmode = true;
                this.form.fill(treatment);
                $('#addtreatmentModal').modal('show');
            },
            deleteTreatment(id) {
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
                        this.form.delete('/api/treatments/'+id)
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
            this.getAppointmentTypes();
            this.showTreatments();
            Fire.$on('AfterCreate', () => {
                this.showTreatments();
            });
        }
    }
</script>
