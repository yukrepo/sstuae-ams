<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Packages List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addTherapyPackage"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Location</th>
                                            <th>Package Name</th>
                                            <th>Treaments</th>
                                            <th>Consulation</th>
                                            <th>Cost</th>
                                            <th>Timeline</th>
                                            <th>Remark</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="therapy_package in therapy_packages.data" :key="therapy_package.id">
                                            <td>{{ therapy_package.id }}</td>
                                            <td> {{ therapy_package.location }} </td>
                                            <td>{{ therapy_package.name }}<br>
                                                Treatments: <b>{{ therapy_package.treatments}}</b><br>
                                                Consultation: <b>{{ therapy_package.consultation}}</b>
                                            </td>
                                            <td>
                                                <span v-for="(test, tekey) in JSON.parse(therapy_package.treatments_Summary)" :key="'te-'+tekey">
                                                {{test.count+' '+test.name}}{{ (test.type == 1)?' - Payable':' - Free' }}<br>
                                                </span>
                                            <td>
                                                <span v-for="(ctest, cekey) in JSON.parse(therapy_package.consultation_Summary)" :key="'te-'+cekey">
                                                {{ctest.count+' '+ctest.name}}{{ (ctest.type == 1)?' - Payable':' - Free' }}<br>
                                                </span>
                                            </td>
                                            <td>{{ therapy_package.cost | formatOMR }}</td>
                                            <td>{{ therapy_package.timeline+' days' }}</td>
                                            <td>{{ therapy_package.remark }}</td>
                                            <td>{{ therapy_package.created_at | setdate }}</td>
                                            <td align="center" v-if="therapy_package.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editTherapyPackage(therapy_package)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteTherapyPackage(therapy_package.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="therapy_packages.total > 1">
                                <pagination class="m-0 float-right" :data="therapy_packages" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="addtherapy_packageModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addtherapy_packageModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateTherapyPackage() : createTherapyPackage()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addtherapy_packageModalTitle">Add Package</h5>
                            <h5 class="modal-title" v-show="editmode" id="addtherapy_packageModalTitle">Update Package's Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>DETAILS</h5>
                                    <hr>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Name</label>
                                        <input v-model="form.name" type="text" name="name" id="name" placeholder="enter full name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="timeline" class="control-label">Duration (No Of Days)</label>
                                        <input v-model="form.timeline" type="text" name="timeline" id="timeline" placeholder="enter no of days for package"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('timeline') }">
                                        <has-error :form="form" field="timeline"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estimation" class="control-label">Estimated Cost</label>
                                                    <input v-model="form.estimation" type="text" name="estimation" id="estimation" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" @click="calculateCost" class="btn btn-sm btn-dark btn-block">Calculate</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="cost" class="control-label">Package Cost</label>
                                        <input v-model="form.cost" type="text" name="cost" id="cost" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea v-model="form.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                        <has-error :form="form" field="remark" rows="2"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="status_id" class="control-label">Status</label>
                                        <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                            <option value="">Select Status</option>
                                            <option value="2">Active</option>
                                            <option value="3">Deactive</option>
                                        </select>
                                        <has-error :form="form" field="status_id"></has-error>
                                    </div>
                                    <hr>
                                    <!-- <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="cost" class="control-label">cost</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input v-model="form.cost" type="text" name="cost" id="cost"                          class="form-control" :class="{ 'is-invalid': form.errors.has('cost') }" readonly="readonly">
                                                <has-error :form="form" field="cost"></has-error>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-primary btn-sm" type="button" @click="getPrice">Calculate Cost</button>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="col-md-8">
                                    <h5>TREATMENTS</h5>
                                    <table class="table table-bordered table-stripped table-condensed">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th>SNo</th>
                                                <th>Treatment</th>
                                                <th>Count</th>
                                                <th>Type</th>
                                                <th><button type="button" @click="addRow" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(counter, cID) in thcountlist" :key="cID" :id="'row'+cID">
                                                <td>{{ counter+1 }}</td>
                                                <td>
                                                    <model-select :options="treatments"
                                                            name="therapy[]['id']"
                                                            v-model="form.therapy__id[getIndex(counter, cID)]"
                                                            placeholder="select therapy">
                                                    </model-select>
                                                </td>
                                                <td>
                                                    <input v-model="form.therapy__count[getIndex(counter, cID)]" type="text" name="therapy[]['count']" id="therapy_count" placeholder="enter no of treatments"
                                            class="form-control">
                                                </td>
                                                <td>
                                                    <select name="therapy[]['type']" id="therapy_type" v-model="form.therapy__type[getIndex(counter, cID)]" class="form-control">
                                                        <option value="1">Payable</option>
                                                        <option value="0">Free</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeRow(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <hr>
                                    <h5>Complimentary Consulation</h5>
                                    <table class="table table-bordered table-stripped table-condensed">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th>SNo</th>
                                                <th class="opselector">Consulation</th>
                                                <th>Count</th>
                                                <th>Type</th>
                                                <th class="w-30"><button type="button" @click="addCRow" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(counter, cID) in cthcountlist" :key="cID" :id="'row'+cID">
                                                <td>{{ counter+1 }}</td>
                                                <td>
                                                    <model-select :options="consultations"
                                                            name="consult[]['id']"
                                                            v-model="form.consult__id[getIndex(counter, cID)]"
                                                            placeholder="select consultation">
                                                    </model-select>
                                                </td>
                                                <td>
                                                    <input v-model="form.consult__count[getIndex(counter, cID)]" type="text" name="consult[]['count']" id="consult_count" placeholder="enter no of consultation"
                                            class="form-control">
                                                </td>
                                                <td>
                                                    <select name="consult[]['type']" id="consult_type" v-model="form.consult__type[getIndex(counter, cID)]" class="form-control">
                                                        <option value="1">Payable</option>
                                                        <option value="0">Free</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeCRow(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

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
                therapy_packages: {},
                medicines:'',
                treatments:[],
                consultations:[],
                thcountlist:[0,1,2],
                cthcountlist:[0],
                form: new Form({
                    id:'',
                    name:'',
                    timeline:'',
                    remark: '',
                    cost:'',
                    estimation:'',
                    treatments:'',
                    consultations:'',
                    therapy__id: [],
                    therapy__count: [],
                    therapy__type: [],
                    consult__id: [],
                    consult__count: [],
                    consult__type: [],
                    status_id:''
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            addRow(){ let bcount = this.thcountlist.length; this.thcountlist.push(bcount); },
            removeRow(barkey){ this.$delete(this.thcountlist, barkey); },
            addCRow(){ let bcount = this.cthcountlist.length; this.cthcountlist.push(bcount); },
            removeCRow(barkey){ this.$delete(this.cthcountlist, barkey); },
            getResults(page = 1) {
                axios.get('/api/packages?page=' + page)
                    .then(response => {
                        this.therapy_packages = response.data;
                    });
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            calculateCost(){
                this.form.post('/api/getPackageCost')
                    .then(response => {  this.form.estimation = response.data; });
            },
            getPrice(typ, cid) {
                if(typ == 1){
                    let treatment = this.form.therapy__id[cid];
                    let cnt = this.form.therapy__count[cid];
                    axios.get('/api/getTreatmentCost/' + treatment)
                        .then(response => {
                            if(cnt >= 1){
                                this.form.therapy__cost[cid] = response.data * cnt;
                            } else {
                                this.form.therapy__cost[cid] = response.data;
                                this.form.therapy__count[cid] = 1;
                            }
                        });
                }
            },
            showTherapyPackages() {
                this.$Progress.start();
                axios.get('/api/packages').then(({ data }) => {
                    this.therapy_packages = data;
                    this.$Progress.finish();
                });
            },
            addTherapyPackage() {
                this.editmode = false;
                this.form.reset();
                $('#addtherapy_packageModal').modal('show');
            },
            createTherapyPackage() {
                this.form.post('/api/packages')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addtherapy_packageModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Package created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateTherapyPackage() {
                this.form.put('/api/packages/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addtherapy_packageModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'TherapyPackage details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editTherapyPackage(therapy_package) {
                this.editmode = true;
                this.form.reset();
                this.form.id = therapy_package.id;
                this.form.name = therapy_package.name;
                this.form.timeline = therapy_package.timeline;
                this.form.remark = therapy_package.remark;
                this.form.cost = therapy_package.cost;
                this.form.estimation = therapy_package.estimation;
                this.form.treatments = therapy_package.treatments;
                this.form.consultations = therapy_package.consultations;
                this.form.status_id = therapy_package.status_id;
                let $frm = this.form;
                let ts = JSON.parse(therapy_package.treatments_Summary);
                if(ts.length >= 1){
                    ts.forEach(function(element, i) {
                        if(i >= 3) { $ths.addRow(); }
                        $frm.therapy__id.push(element.id);
                        $frm.therapy__count.push(element.count);
                        $frm.therapy__type.push(element.type);
                    });
                }
                let cs = JSON.parse(therapy_package.consultation_Summary);
                if(cs.length >= 1){
                    cs.forEach(function(element, i) {
                        if(i >= 1) { $ths.addRow(); }
                        $frm.consult__id.push(element.id);
                        $frm.consult__count.push(element.count);
                        $frm.consult__type.push(element.type);
                    });
                }
                //this.convertStringToArray(therapy_package);
                $('#addtherapy_packageModal').modal('show');
            },

            getAllAssets() {
                axios.get('/api/getTreatmentsSelectList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsSelectList').then((data) => {this.consultations = data.data }).catch();
            },
            deleteTherapyPackage(id) {
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
                        this.form.delete('/api/packages/'+id)
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
            this.showTherapyPackages();
            Fire.$on('AfterCreate', () => {
                this.showTherapyPackages();
            });
            this.getAllAssets();
        }
    }
</script>
