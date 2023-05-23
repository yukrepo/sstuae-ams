<template>
    <div class="content-box-100 custom-cal ">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Set Shift Availability and Off Days </h3>
                    </div>
                    <div class="card-body p-3">
                        <form @submit.prevent="createHoliday()">
                            <div class="form-group">
                                <label for="type" class="control-label">Select Type </label>
                                <select name="type" v-model="form.type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="" disabled>Select Type</option>
                                    <option value="1">Shift Timings</option>
                                    <option value="2">Weekly Off</option>
                                    <option value="3">Approved Leave/ Day Off</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="days" class="control-label">Select Date Range</label>
                                <div class="row">
                                    <div class="col-md-6 p-r-5">
                                        <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.start_date" :auto-submit="true"></vp-date-picker>
                                    </div>
                                    <div class="col-md-6 p-l-5">
                                        <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.end_date" :auto-submit="true"></vp-date-picker>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="days" class="control-label">Select Day <small class="text-danger">(Use ctrl for multiselect)</small> </label>
                                <select name="days" v-model="form.days" class="form-control" multiple :class="{ 'is-invalid': form.errors.has('days') }" style="height:155px">
                                    <option value="8">Select All Days</option>
                                    <option v-for="(wday, wcount) in weekdays" :key="wcount" :value="wcount">
                                        {{ wday }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group" >
                                <button class="btn btn-block btn-dark btn-sm" @click="chooseSlot" type="button"> Select Slots</button>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="control-label">Remark</label>
                                <textarea v-model="form.remark" name="remark" rows="3" class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                            </div>
                            <div class="form-group">
                                <button  type="submit" class="btn btn-wide btn-success"> Submit </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <availability-admin-calendar :doctor_id="doctor_id" :bus="bus"></availability-admin-calendar>
            </div>
        </div>
        <div class="modal fade" id="timeslotsModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="timeslotModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeslotModalTitle">TileSlots</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-scroll-with-foot">
                            <div class="row m-0">
                                <div class="col-md-6 p-r-5 p-l-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Active Timing Slots
                                                <button type="button" @click="chooseNoneActive" class="float-right btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                                <button type="button" @click="chooseAllActive" class="float-right btn btn-success btn-sm m-r-5"> <i class="fas fa-check"></i> </button>
                                            </h3>
                                        </div>
                                        <div class="card-body p-0 content-box">
                                            <table id="stock-alert" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="wf-50 text-center">SNo</th>
                                                        <th>TimeSlot</th>
                                                        <th class="wf-100 text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(time, count) in ontimings" :key="time.id">
                                                        <td class="pf-3 text-center">{{ count + 1 }}</td>
                                                        <td class="pf-3">{{ time.time }} - {{ time.closing }}</td>
                                                        <td class="pf-0 text-center">
                                                            <input type="checkbox" name="slots" :id="'slot_'+time.id" class="checkboxInList" :value="time.id" v-model="form.slots">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 p-r-5 p-l-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Off Timing Slots
                                                 <button type="button" @click="chooseNoneInactive" class="float-right btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                                <button type="button" @click="chooseAllInactive" class="float-right btn btn-success btn-sm m-r-5"> <i class="fas fa-check"></i> </button>
                                            </h3>
                                        </div>
                                        <div class="card-body p-0  content-box">
                                            <table id="stock-alert" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="wf-50 text-center">SNo</th>
                                                        <th>TimeSlot</th>
                                                        <th class="wf-50 text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(time, count) in offtimings" :key="time.id">
                                                        <td class="pf-3 text-center">{{ count + 1 }}</td>
                                                        <td class="pf-3">{{ time.time }} - {{ time.closing }}</td>
                                                        <td class="pf-0 text-center">
                                                            <input type="checkbox" name="slots" :id="'slot_'+time.id" class="checkboxInList" :value="time.id" v-model="form.slots">
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
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-success" type="button" data-dismiss="modal" aria-label="Close">Done Selection</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
    export default {
        data() {
            return {
                bus: new Vue(),
                doctor_id: '',
                doctor:'',
                offtimings: {},
                ontimings: {},
                options:{format: 'YYYY-MM-DD', minDate:moment(new Date())},
                editmode: false,
                weekdays: ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                slots:[],
                form: new Form({
                    id:'',
                    type:'',
                    location_id:'',
                    admin_id:'',
                    doctor_id: '',
                    remark:'',
                    days:[],
                    start_date:'',
                    end_date:'',
                    slots:[],
                    status_id:4,
                }),
            }
        },
        methods: {
            createHoliday() {
                this.form.doctor_id = this.doctor_id;
                this.form.post('/api/availability/create-absent-by-admin')
                .then(() => {
                    this.form.reset();
                    $('#addApponitModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Details has been added successfully'
                    });
                    this.bus.$emit('submit');
                })
                .catch(() => {
                });
            },
            chooseSlot() {
                this.showTimeSlots();
                $('#timeslotsModal').modal('show');
            },
            chooseAllInactive() {
                this.offtimings.forEach(element => {
                    if(this.form.slots.includes(element.id) == false) {
                        this.form.slots.push(element.id);
                    }
                });
            },
            chooseNoneInactive() {
                this.offtimings.forEach(element => {
                    if(this.form.slots.includes(element.id) == true) {
                        this.form.slots.splice(this.form.slots.indexOf(element.id), 1);
                    }
                });
             },
            chooseAllActive() {
                this.ontimings.forEach(element => {
                    if(this.form.slots.includes(element.id) == false) {
                        this.form.slots.push(element.id);
                    }
                });
            },
            chooseNoneActive() {
                this.ontimings.forEach(element => {
                    if(this.form.slots.includes(element.id) == true) {
                        this.form.slots.splice(this.form.slots.indexOf(element.id), 1);
                    }
                });
             },
            showTimeSlots() {
                axios.get('/api/get-off-time-slots').then(({ data }) => {  this.offtimings = data; });
                axios.get('/api/get-on-time-slots').then(({ data }) => {  this.ontimings = data; });
            },
            timeSlots() {
                axios.get('/api/get-all-time-slots').then(({ data }) => (this.slots = data));
            },
            getDoctor() {
                axios.get('/api/doctors/'+this.doctor_id).then((data) => { this.doctor = data.data });
            },
            setDoctor() {
                let actiivedoc = this.$route.path.split("/");
                this.doctor_id = actiivedoc[3];
            }
        },
        beforeMount() {
            this.setDoctor();
        },
        mounted() {
            this.getDoctor();
            this.timeSlots();
        }
    }
</script>
