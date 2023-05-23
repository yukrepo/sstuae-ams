<template>
    <div class="content pt-1">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Discounts List
                        <div class="card-tools">
                            <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addDiscount"><i class="fas fa-plus fa-fw"></i></button>
                        </div>
                    </h3>
                </div>
                <div class="card-body p-0 day-content-box">
                    <table id="stock-alert" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="wf-50">SNo</th>
                                <th>Location</th>
                                <th>Title</th>
                                <th>From Date</th>
                                <th>Till Date</th>
                                <th>TimeSlots</th>
                                <th>Value</th>
                                <th>Applicable On</th>
                                <th class="wf-100">Added On</th>
                                <th class="wf-50">Status</th>
                                <th class="wf-75">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(discount, count) in discounts.data" :key="discount.id">
                                <td>{{ (discounts.current_page - 1)*(discounts.per_page) + count + 1 }}</td>
                                <td>{{ discount.location.clinic_name }}</td>
                                <td>{{ discount.title | capitalize}}</td>
                                <td>{{ discount.start_date | setdate }}</td>
                                <td>{{ discount.end_date | setdate }}</td>
                                <td>
                                    <span v-if="discount.time_slots != 0">
                                        Limited Slots
                                    </span>
                                    <span v-else>
                                        All Slots
                                    </span>
                                </td>
                                <td>
                                    <span v-if="checkPercent(discount.value)">
                                        <b>{{ discount.value }}</b>
                                    </span>
                                    <span v-else>
                                        <b>{{ discount.value | formatOMR }}</b>
                                    </span>
                                </td>
                                <td>
                                    <label class="status-label btn-dark-theme" v-show="discount.consultation == 1"> C </label>
                                    <label class="status-label btn-dark-theme" v-show="discount.treatment == 1"> T </label>
                                    <label class="status-label btn-dark-theme" v-show="discount.pharmacy == 1"> M </label>
                                    <label class="status-label btn-dark-theme" v-show="discount.pcourse == 1"> PC </label>
                                    <label class="status-label btn-dark-theme" v-show="discount.dcourse== 1"> DC </label>
                                </td>
                                <td>{{ discount.created_at | setdate }}</td>
                                <td align="center" v-if="discount.status_id == 2">
                                    <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                </td>
                                <td align="center" v-else>
                                    <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" @click="editDiscount(discount)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" @click="deleteDiscount(discount.id)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer" v-if="discounts.total > 1">
                    <pagination class="m-0 float-right" :limit="10" :data="discounts" @pagination-change-page="getResults" :show-disabled="true">
                    </pagination>
                </div>
            </div>
        </div>
        <div class="modal fade" id="adddiscountModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddiscountModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateDiscount() : createDiscount()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="adddiscountModalTitle">Add Discount</h5>
                            <h5 class="modal-title" v-show="editmode" id="adddiscountModalTitle">Update Discount's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location_id" class="control-label">Location</label>
                                        <select v-model="form.location_id" name="location_id" class="form-control" :class="{ 'is-invalid': form.errors.has('location_id') }">
                                            <option disabled value="">Select Location</option>
                                            <option v-for="location in locations" :key="location.id" v-bind:value="location.id">
                                                {{ location.clinic_name }}
                                            </option>
                                        </select>
                                        <has-error :form="form" field="location_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="title" class="control-label">title</label>
                                        <input v-model="form.title" type="text" name="title" id="title" placeholder="enter title"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                        <has-error :form="form" field="title"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_Date" class="control-label">start Date</label>
                                        <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.start_date" :auto-submit="true"></vp-date-picker>
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date" class="control-label">end date</label>
                                         <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.end_date" :auto-submit="true"></vp-date-picker>
                                    </div>
                                    <div class="form-group">
                                        <label for="value" class="control-label">Discount Value</label>
                                        <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="applicable" class="control-label">Applicable On</label>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label for="consultation" class="control-label">Consultation</label>
                                                <select name="consultation" id="consultation" v-model="form.consultation" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="treatment" class="control-label">treatment</label>
                                                <select name="treatment" id="treatment" v-model="form.treatment" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="pharmacy" class="control-label">pharmacy</label>
                                                <select name="pharmacy" id="pharmacy" v-model="form.pharmacy" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="pcourse" class="control-label">Prescribe Course</label>
                                                <select name="pcourse" id="pcourse" v-model="form.pcourse" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="dcourse" class="control-label">Direct Course</label>
                                                <select name="dcourse" id="dcourse" v-model="form.dcourse" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="type" class="control-label">Time Slots</label>
                                        <select name="type" v-model="form.type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                            <option disabled value="">Select time slots</option>
                                            <option value="1">For All Time Slots</option>
                                            <option value="2">For Selected Time Slots</option>
                                        </select>
                                        <span class="m-t-15" v-show="form.type == 2">
                                            <select name="time_slots" v-model="form.time_slots" class="form-control" multiple style="height:225px">
                                                <option v-for="(slot, count) in slots" :key="count" :value="count">
                                                    {{ slot.time }} - {{ slot.closing }}
                                                </option>
                                            </select>
                                        </span>
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
                slots: {},
                discounts: {},
                options:{format: 'YYYY-MM-DD'},
                locations:this.$store.getters.locations,
                cform: new Form({
                    discount1:'',
                    discount2:''
                }),
                form: new Form({
                    id:'',
                    value:'',
                    title:'',
                    start_date:'',
                    end_date:'',
                    time_slots:[],
                    type:'',
                    status_id:'2',
                    location_id:'1',
                    consultation:'0',
                    pharmacy:'0',
                    treatment:'0',
                    pcourse:'0',
                    dcourse:'0'
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/discounts?page=' + page)
                    .then(response => {
                        this.discounts = response.data;
                    });
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            },
            showDiscounts() {
                this.$Progress.start();
                axios.get('/api/discounts').then(({ data }) => {
                    this.discounts = data;
                    this.$Progress.finish();
                });
            },
            addDiscount() {
                this.editmode = false;
                this.form.reset();
                $('#adddiscountModal').modal('show');
            },
            createDiscount() {
                this.form.post('/api/discounts')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiscountModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Discount created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateDiscount() {
                this.form.put('/api/discounts/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddiscountModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Discount details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editDiscount(discount) {
                this.editmode = true;
                if(discount.time_slots == 0) {
                    discount.type = 1;
                   discount.time_slots = [];
                } else {
                    discount.type = 2;
                    if(Array.isArray(discount.time_slots) == false) {
                        discount.time_slots = discount.time_slots.split(',');
                    }
                }
                //console.log(discount);
                this.form.fill(discount);
                $('#adddiscountModal').modal('show');
            },
            deleteDiscount(id) {
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
                        this.form.delete('/api/discounts/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            },
            timeSlots() {
                axios.get('/api/get-all-time-slots').then(({ data }) => (this.slots = data));
            },
        },
        created() {
            this.showDiscounts();
            Fire.$on('AfterCreate', () => {
                this.showDiscounts();
            });
            this.timeSlots();
        }
    }
</script>
