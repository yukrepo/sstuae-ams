<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9 py-1">
                        <vue-search placer="search by insurance name"></vue-search>
                    </div>
                    <div class="col-md-3 text-right py-1">
                        <button class="btn btn-success btn-sm mr-1" type="button" @click="refreshRecord"><i class="fas fa-retweet fa-fw"></i> Reset</button>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Insurancers List
                                    <div class="card-tools">

                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addInsurancer"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">ID</th>
                                            <th>Name</th>
                                            <th class="text-center">Contact No</th>
                                            <th class="text-center">Consulation</th>
                                            <th class="text-center">Free Followup</th>
                                            <th class="text-center">Approval</th>
                                            <th class="text-center">Treatment</th>
                                            <th class="text-center">C. D.</th>
                                            <th class="text-center">Pharmacy</th>
                                            <th class="wf-100">Added On</th>
                                            <th class="wf-50">Status</th>
                                            <th class="wf-75">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(insurancer, count) in insurancers.data" :key="insurancer.id">
                                            <td>{{ (insurancers.current_page - 1)*(insurancers.per_page) + count + 1 }}</td>
                                            <td>{{ insurancer.name }}</td>
                                            <td class="text-center">{{ insurancer.contact_no }}</td>
                                            <td class="text-center">
                                                <b>{{ insurancer.insured_consultation.length }}</b>
                                                <button class="m-l-5 btn btn-sm btn-outline-primary" type="button" @click="viewConsultation(insurancer.id)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                {{ insurancer.followup_days }} days
                                            </td>
                                            <td class="text-center">
                                                {{ insurancer.approval_days }} days
                                            </td>
                                            <td class="text-center">
                                                <b>{{ insurancer.insured_treatments.length }}</b>
                                                <button class="m-l-5 btn btn-sm btn-outline-primary" type="button" @click="viewTreatments(insurancer.id)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-success" type="button" @click="removeCashDiscount(insurancer.id)" v-if="insurancer.cash_discount == 1">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" type="button" @click="addCashDiscount(insurancer.id)" v-else>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <b>{{ insurancer.insured_medicines.length }}</b>
                                                <button class="m-l-5 btn btn-sm btn-outline-primary" type="button" @click="viewMedicines(insurancer.id)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                            <td>{{ insurancer.created_at | setdate }}</td>
                                            <td align="center" v-if="insurancer.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editInsurancer(insurancer)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteInsurancer(insurancer.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="insurancers.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="insurancers" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addinsurancerModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addinsurancerModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateInsurancer() : createInsurancer()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addinsurancerModalTitle">Add Insurance</h5>
                            <h5 class="modal-title" v-show="editmode" id="addinsurancerModalTitle">Update Insurance's Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Name</label>
                                        <input v-model="form.name" type="text" name="name" id="name" placeholder="enter company name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_no" class="control-label">Contact No</label>
                                        <input v-model="form.contact_no" type="text" name="contact_no" id="contact_no" placeholder="enter mobile number"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('contact_no') }">
                                        <has-error :form="form" field="contact_no"></has-error>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="followup_days" class="control-label">Free Followup Days</label>
                                            <input v-model="form.followup_days" type="text" name="followup_days" id="followup_days" placeholder="free revisit days"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('followup_days') }">
                                            <has-error :form="form" field="followup_days"></has-error>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="approval_days" class="control-label">Approval Days</label>
                                            <input v-model="form.approval_days" type="text" name="approval_days" id="approval_days" placeholder="approval days"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('approval_days') }">
                                            <has-error :form="form" field="approval_days"></has-error>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                             <div class="form-group">
                                                <label for="cash_discount" class="control-label">Cash Discount</label>
                                                <select v-model="form.cash_discount" name="cash_discount" id="cash_discount" class="form-control" :class="{ 'is-invalid': form.errors.has('cash_discount') }">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                                <has-error :form="form" field="cash_discount"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="status_id" class="control-label">Status</label>
                                                <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                                    <option value="">Select Status</option>
                                                    <option value="2">Active</option>
                                                    <option value="3">Deactive</option>
                                                </select>
                                                <has-error :form="form" field="status_id"></has-error>
                                            </div>
                                        </div>
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
        <div class="modal fade" id="consultationModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="consultationModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="consultationModalTitle">Consultation Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-scroll">
                            <table class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Consultation</th>
                                        <th>Cost</th>
                                        <th>Discount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(consult, count) in mapped_consultations" :key="consult.tid">
                                        <td>{{ ++count }}</td>
                                        <td>{{ consult.name }}</td>
                                        <td>{{ consult.cost | formatOMR }}</td>
                                        <td>
                                            <span v-if="cform.treatment_id == consult.tid">
                                                <input type="text" class="form-control" v-model="cform.discount">
                                            </span>
                                            <span v-else-if="consult.discount">
                                                <span v-if="checkPercent(consult.discount)">
                                                    <b>{{ consult.discount }}</b>
                                                </span>
                                                <span v-else>
                                                    <b>{{ consult.discount | formatOMR }}</b>
                                                </span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="(saving && cform.treatment_id == consult.tid)">
                                                <img style="height:28px" :src="svgurl+'loop.gif'" alt="updating">
                                            </span>
                                            <span v-else-if="cform.treatment_id == consult.tid">
                                                <button class="btn btn-primary btn-sm" @click="saveConsultation()">
                                                    <i class="fas fa-file"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" @click="saveCancel"><i class="fas fa-times"></i></button>
                                            </span>
                                            <span v-else>
                                                <button class="btn btn-warning btn-sm" @click="updateConsultation(consult)"><i class="fas fa-edit"></i></button>
                                                <button v-show="consult.discount" class="btn btn-danger btn-sm" @click="deleteConsultation(consult)"><i class="fas fa-trash"></i></button>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="medicinesModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="medicineModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="medicineModalTitle">Medicine Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-scroll">
                            <table class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th class="wf-75">SNo</th>
                                        <th>Medicine</th>
                                        <th>Insurance Price</th>
                                        <th class="wf-75">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(medicine, count) in mapped_medicines" :key="medicine.medicine_id">
                                        <td>{{ ++count }}</td>
                                        <td>{{ medicine.name }}</td>
                                        <td>
                                            {{ medicine.insurance_price | formatOMR }}
                                        </td>
                                        <td>
                                            <span v-if="dform.rid == medicine.rid">
                                                <img style="height:28px" :src="svgurl+'loop.gif'" alt="updating">
                                            </span>
                                            <span v-else-if="mform.medicine_id == medicine.medicine_id">
                                                <img style="height:28px" :src="svgurl+'loop.gif'" alt="updating">
                                            </span>
                                            <span v-else>
                                                <button v-if="medicine.rid" class="btn btn-success btn-sm" @click="removeMed(medicine)"><i class="fas fa-check"></i></button>
                                                <button v-else class="btn btn-danger btn-sm" @click="addMed(medicine)"><i class="fas fa-times"></i></button>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="treatmentModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="treatmentModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="treatmentModalTitle">Treatment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-scroll">
                            <table class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Treatment</th>
                                        <th>Cost</th>
                                        <th>Discount</th>
                                        <th>C.D. (3-4)</th>
                                        <th>C.D. (5+)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(treatment, count) in mapped_treatments" :key="treatment.tid">
                                        <td>{{ ++count }}</td>
                                        <td>{{ treatment.name }}</td>
                                        <td>{{ treatment.cost | formatOMR }}</td>
                                        <td>
                                            <span v-if="cform.treatment_id == treatment.tid">
                                                <input type="text" class="form-control" v-model="cform.discount">
                                            </span>
                                            <span v-else-if="treatment.discount">
                                                <span v-if="checkPercent(treatment.discount)">
                                                    <b>{{ treatment.discount }}</b>
                                                </span>
                                                <span v-else>
                                                    <b>{{ treatment.discount | formatOMR }}</b>
                                                </span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="cform.treatment_id == treatment.tid">
                                                <input type="text" class="form-control" v-model="cform.cd34">
                                            </span>
                                            <span v-else-if="treatment.cd34">
                                                <span v-if="checkPercent(treatment.cd34)">
                                                    <b>{{ treatment.cd34 }}</b>
                                                </span>
                                                <span v-else>
                                                    <b>{{ treatment.cd34 | formatOMR }}</b>
                                                </span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="cform.treatment_id == treatment.tid">
                                                <input type="text" class="form-control" v-model="cform.cd56">
                                            </span>
                                            <span v-else-if="treatment.cd56">
                                               <span v-if="checkPercent(treatment.cd56)">
                                                    <b>{{ treatment.cd56 }}</b>
                                                </span>
                                                <span v-else>
                                                    <b>{{ treatment.cd56 | formatOMR }}</b>
                                                </span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="(saving && editable == treatment.tid)">
                                                <img style="height:28px" :src="svgurl+'loop.gif'" alt="updating">
                                            </span>
                                            <span v-else-if="cform.treatment_id == treatment.tid">
                                                <button class="btn btn-primary btn-sm" @click="saveTreatment()">
                                                    <i class="fas fa-file"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" @click="saveCancel"><i class="fas fa-times"></i></button>
                                            </span>
                                            <span v-else>
                                                <button class="btn btn-warning btn-sm" @click="updateTreatment(treatment)"><i class="fas fa-edit"></i></button>
                                                <button v-show="treatment.discount" class="btn btn-danger btn-sm" @click="deleteTreatment(treatment)"><i class="fas fa-trash"></i></button>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                editable:'',
                saving:'',
                insurancers: {},
                medicines:'',
                treatments:'',
                mapped_consultations:'',
                mapped_treatments:'',
                mapped_medicines:'',
                form: new Form({
                    id:'',
                    name:'',
                    contact_no:'',
                    remark:'',
                    followup_days:'',
                    approval_days:14,
                    status_id:'',
                    cash_discount:'0'
                }),
                cform: new Form({
                    insurance_id:'',
                    treatment_id:'',
                    discount:'',
                    cd34:'',
                    cd56:'',
                    id:''
                }),
                mform: new Form({
                    insurance_id:'',
                    medicine_id:'',
                    id:''
                }),
                dform: new Form({
                    id: '',
                    type:''
                }),
                asearch:''
            }
        },
        methods: {
            refreshRecord() {
                this.asearch = '';
                Fire.$emit('AfterCreate');
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            getIndex(list, id) {
                return list;
            },
            setActive(menuItem) {
                this.activeMenu = menuItem; // no need for Vue.set()
                //console.log(menuItem);
            },
            getResults(page = 1) {
                axios.get('/api/insurances?page=' + page)
                    .then(response => {
                        this.insurancers = response.data;
                    });
            },
            showInsurancers() {
                this.$Progress.start();
                axios.get('/api/insurances').then(({ data }) => {
                    this.insurancers = data;
                    this.$Progress.finish();
                });
            },
            addInsurancer() {
                this.editmode = false;
                this.form.reset();
                $('#addinsurancerModal').modal('show');
            },
            viewConsultation(iid) {
                this.mapped_consultations = {};
                this.cform.insurance_id = iid;
                this.editable = iid;
                axios.get('/api/getMappedConsultations/'+iid).then(({ data }) => {
                    this.mapped_consultations = data;
                });
                $('#consultationModal').modal('show');
            },
            updateConsultation(consult) {
                this.cform.treatment_id = consult.tid;
                this.cform.discount = consult.discount;
                this.cform.id = consult.rid;
            },
            saveCancel(){
                this.saving = '';
                this.cform.treatment_id = '';
                 this.editable = '';
            },
            saveConsultation() {
                this.saving = 1;
                this.cform.post('/api/update-consultation-insurance-mapping')
                .then(() => {
                    this.saving = '';
                    axios.get('/api/getMappedConsultations/'+this.cform.insurance_id).then(({ data }) => {
                        this.mapped_consultations = data;
                    });

                    this.editable = '';
                    this.cform.treatment_id = '';
                    this.cform.discount = '';
                    //Fire.$emit('AfterCreate');
                    this.$toaster.success('Insurance mapping with consultation has been updated.');
                })
                .catch(() => {

                });
            },
            deleteConsultation(treat) {
                this.saving = 1;
                this.editable = treat.tid;
                this.dform.type = 1;
                this.dform.id = treat.rid;
                this.dform.post('/api/update-insurance-mapping')
                .then(() => {
                    axios.get('/api/getMappedConsultations/'+treat.insurance_id).then(({ data }) => {
                        this.mapped_consultations = data;
                    });
                    //Fire.$emit('AfterCreate');
                    this.saving = '';
                    this.editable = '';
                    this.$toaster.success('Insurance mapping with consultation has been updated.');
                    this.dform.reset();
                })
            },
            viewTreatments(iid) {
                this.mapped_treatments = {};
                this.cform.insurance_id = iid;
                this.editable = iid;
                axios.get('/api/getMappedTreatments/'+iid).then(({ data }) => {
                    this.mapped_treatments = data;
                });
                $('#treatmentModal').modal('show');
            },
            updateTreatment(consult) {
                this.cform.treatment_id = consult.tid;
                this.cform.discount = consult.discount;
                this.cform.id = consult.rid;
            },
            saveTreatment() {
                this.saving = 1;
                this.cform.post('/api/update-treatment-insurance-mapping')
                .then(() => {
                    this.saving = '';
                    axios.get('/api/getMappedTreatments/'+this.cform.insurance_id).then(({ data }) => {
                        this.mapped_treatments = data;
                    });

                    this.editable = '';
                    this.cform.treatment_id = '';
                    this.cform.discount = '';
                    //Fire.$emit('AfterCreate');
                    this.$toaster.success('Insurance mapping with treatment has been updated.');
                })
                .catch(() => {

                });
            },
            addCashDiscount(iid) {
               swal.fire({
                    title: 'Are you sure?',
                    text: "You want to enable cash discount for this insurance company.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, I am sure!'
                }).then((result) => {
                    if (result.value) {
                        axios.get('/api/insurances/addCashDiscount/'+iid)
                            .then(() => {
                                swal.fire('Its Done!', 'Cash discount has been applied.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Done!', 'cash discount can not be applied at the moment.', 'error');
                            });
                    }
                });
            },
            removeCashDiscount(iid) {
               swal.fire({
                    title: 'Are you sure?',
                    text: "You want to disable cash discount for this insurance company.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, I am sure!'
                }).then((result) => {
                    if (result.value) {
                        axios.get('/api/insurances/removeCashDiscount/'+iid)
                            .then(() => {
                                swal.fire('Its Done!', 'Cash discount has been disabled.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Done!', 'cash discount can not be disabled at the moment.', 'error');
                            });
                    }
                });
            },
            deleteTreatment(treat) {
                this.saving = 1;
                this.editable = treat.tid;
                this.dform.type = 2;
                this.dform.id = treat.rid;
                this.dform.post('/api/update-insurance-mapping')
                .then(() => {
                    axios.get('/api/getMappedTreatments/'+treat.insurance_id).then(({ data }) => {
                        this.mapped_treatments = data;
                    });
                    //Fire.$emit('AfterCreate');
                    this.saving = '';
                this.editable = '';
                    this.$toaster.success('Insurance mapping with treatments has been updated.');
                    this.dform.reset();
                })
            },
            viewMedicines(iid) {
                this.mapped_medicines = {};
                this.mform.insurance_id = iid;
                axios.get('/api/getMappedMedicines/'+iid).then(({ data }) => {
                    this.mapped_medicines = data;
                });
                $('#medicinesModal').modal('show');
            },
            addMed(medicine) {
                this.mform.insurance_id = medicine.insurance_id;
                this.mform.medicine_id = medicine.medicine_id;
                this.mform.post('/api/update-medicine-insurance-mapping')
                .then(() => {
                    axios.get('/api/getMappedMedicines/'+medicine.insurance_id).then(({ data }) => {
                        this.mapped_medicines = data;
                    });
                    this.mform.reset();
                    //Fire.$emit('AfterCreate');
                    this.$toaster.success('Insurance mapping with pharmacy has been updated.');
                })
                .catch(() => {

                });
            },
            removeMed(mid){
                this.dform.type = 3;
                this.dform.id = mid.rid;
                this.dform.post('/api/update-insurance-mapping')
                .then(() => {
                    axios.get('/api/getMappedMedicines/'+mid.insurance_id).then(({ data }) => {
                        this.mapped_medicines = data;
                    });
                    //Fire.$emit('AfterCreate');
                    this.$toaster.success('Insurance mapping with pharmacy has been updated.');
                    this.dform.reset();
                })
                .catch(() => {

                });
            },
            createInsurancer() {
                this.form.post('/api/insurances')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addinsurancerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Insurancer created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateInsurancer() {
                this.form.put('/api/insurances/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addinsurancerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Insurancer details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editInsurancer(insurancer) {
                this.editmode = true;
                this.form.fill(insurancer);
                $('#addinsurancerModal').modal('show');
            },

            getAllAssets() {
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getMedicinesList').then((data) => {this.medicines = data.data }).catch();
            },
            deleteInsurancer(id) {
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
                        this.form.delete('/api/insurances/'+id)
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
                axios.get('/api/findInsurances?q='+this.asearch)
                    .then((data) => {
                        this.insurancers = data.data;
                    })
                    .catch();
            });
            this.showInsurancers();
            Fire.$on('AfterCreate', () => {
                this.showInsurancers();
            });
            //this.getAllAssets();
        }
    }
</script>
