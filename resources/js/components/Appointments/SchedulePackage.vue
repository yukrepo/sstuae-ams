<template>
    <div>
        <div class="container-fluid m-t-10">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create A Scheduled Package</h3>
                        </div>
                        <div class="card-body p-3">
                            <form @submit.prevent="createSchedule()">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="user_id" class="control-label">Search patient</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <model-select :options="customers"
                                                        name="user_id"
                                                        id="user_id"
                                                        aria-required="true"
                                                        v-model="form.user_id"
                                                        placeholder="search patient">
                                                    </model-select>
                                                    <has-error :form="form" field="user_id"></has-error>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="btn btn-success btn-sm" v-if="form.user_id" type="button" @click="viewCustomer(form.user_id)">View Details</button>
                                                    <button class="btn btn-primary btn-sm" v-else disabled type="button">View Details</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="package_id" class="control-label">Choose Package</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" readonly v-model="form.package_name" class="form-control" :class="{ 'is-invalid': form.errors.has('package_id') }">
                                                    <has-error :form="form" field="package_id" ></has-error>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="btn btn-success btn-sm" @click="showPackages" v-if="form.package_id" type="button">Change Package</button>
                                                    <button class="btn btn-primary btn-sm" @click="modifyPackage" v-else type="button">Select Package</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="amount" class="control-label">Payable Amount</label>
                                            <input type="text" readonly v-model="form.amount" class="form-control">
                                        </div>
                                        <div class="form-group" v-show="form.package_id">
                                            <label for="start_date" class="control-label">Start Date</label>
                                            <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.start_date" :auto-submit="true" @change="getEndDate"></vp-date-picker>
                                        </div>
                                        <div class="form-group" v-show="form.start_date">
                                            <label for="end_date" class="control-label">End Date</label>
                                            <input type="text" readonly v-model="form.end_date" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="remark" class="control-label">Remark</label>
                                            <textarea v-model="form.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                            <has-error :form="form" field="remark"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="status_id" class="control-label">Status</label>
                                            <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                                <option value="2">Active</option>
                                                <option value="3">Deactive</option>
                                            </select>
                                            <has-error :form="form" field="status_id"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <img class="img-icon" :src="loaderurl" v-if="loader">
                                            <button class="btn btn-block btn-dark btn-md" type="submit" v-else>Schedule Package Now</button>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-8">
                                        <div class="sch-schdular">
                                            <label for="" class="control-label d-block">Schedule Appointments <i class="text-danger float-right">(Schedule Package to create appointments)</i> </label>
                                            <span v-show="form.package_id" class="float-right">
                                                Treatments: <b>{{ tpackage.treatments }}</b>
                                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                                Consultation: <b>{{ tpackage.consultation }}</b>
                                            </span>
                                            <div class="schbody">
                                                <table class="table table-condensed table-striped table-bordered schedule-table">
                                                    <thead>
                                                        <tr>
                                                            <th>SNo</th>
                                                            <th>Treatment/Consultation</th>
                                                            <th>Date</th>
                                                            <th>DR/Therapist</th>
                                                            <th>Available Slots</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-show="form.package_id">
                                                        <tr v-for="(pkg, pky) in tpackage.bookings" :key="'p'+pky">
                                                            <td>{{ pky+1 }}</td>
                                                            <td>
                                                                {{ pkg.tname }}
                                                            </td>
                                                            <td v-if="form.start_date">
                                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.appointment__date[getIndex(pkg, pky)]" :auto-submit="true" :min="form.start_date" :max="form.end_date" @change="onDateSelect(pkg.tid, pkg.ttype, getIndex(pkg, pky))"></vp-date-picker>

                                                            </td>
                                                            <td v-else>--</td>
                                                            <td v-if="pkg.ttype == 't' && form.appointment__date[getIndex(pkg, pky)]">
                                                                <div class="input-group">
                                                                    <select v-model="form.appointment__doctor_id[getIndex(pkg, pky)]" :name="'appointment[doctor_id]['+pky+']'" class="form-control" @change="getTimeSlots(2, getIndex(pkg, pky))">
                                                                        <option disabled value="">Select Therapist</option>
                                                                        <option v-for="doctor in therapists" :key="doctor.id" v-bind:value="doctor.id">
                                                                            {{ doctor.name | capitalize }}
                                                                        </option>
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                            <div class="input-group-text">
                                                                            <input type="radio" v-model="form.appointment__checked[getIndex(pkg, pky)]">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td v-else-if="pkg.ttype == 'c' && form.appointment__date[getIndex(pkg, pky)]">
                                                                <div class="input-group">
                                                                    <select v-model="form.appointment__doctor_id[getIndex(pkg, pky)]" :name="'appointment[doctor_id]['+pky+']'" class="form-control" @change="getTimeSlots(1, getIndex(pkg, pky))">
                                                                        <option disabled value="">Select Doctor</option>
                                                                        <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                                            {{ doctor.name | capitalize }}
                                                                        </option>
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                            <div class="input-group-text">
                                                                            <input type="radio" v-model="form.appointment__checked[getIndex(pkg, pky)]">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td v-else>
                                                                --
                                                            </td>

                                                            <td v-if="pkg.ttype == 't'">
                                                                <div class="row">
                                                                    <div class="col-4 p-r-0">
                                                                        <div class="input-group">
                                                                            <select v-model="form.appointment__stid[getIndex(pkg, pky)]" :name="'appointment[stid]['+pky+']'" class="form-control" @change="getClosings(1, getIndex(pkg, pky))">
                                                                                <option disabled value="">Select Start Time</option>
                                                                                <option v-for="(timeslot, key) in start_times[getIndex(pkg, pky)]" :key="key" v-bind:value="key">
                                                                                    {{ timeslot }}
                                                                                </option>
                                                                            </select>
                                                                            <div class="input-group-append">
                                                                                <div class="input-group-text">
                                                                                    <input type="radio" v-model="form.appointment__schecked[getIndex(pkg, pky)]">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4 p-r-0">
                                                                            <div class="input-group">
                                                                            <select v-model="form.appointment__etid[getIndex(pkg, pky)]" :name="'appointment[etid]['+pky+']'" class="form-control" @change="getRooms(getIndex(pkg, pky))">
                                                                                <option disabled value="">Select End Time</option>
                                                                                <option v-for="(timeslot, key) in end_times[getIndex(pkg, pky)]" :key="key" v-bind:value="key" v-if="key >= form.appointment__stid[getIndex(pkg, pky)]">
                                                                                    {{ timeslot }}
                                                                                </option>
                                                                            </select>
                                                                            <div class="input-group-append">
                                                                                <div class="input-group-text">
                                                                                    <input type="radio" v-model="form.appointment__echecked[getIndex(pkg, pky)]">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <select v-model="form.appointment__rid[getIndex(pkg, pky)]" :name="'appointment[rid]['+pky+']'" class="form-control">
                                                                            <option disabled value="">Select Room</option>
                                                                            <option v-for="(room, key) in rooms[getIndex(pkg, pky)]" :key="key" v-bind:value="key">
                                                                                {{ room }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td v-else-if="pkg.ttype == 'c'">
                                                                <div class="row">
                                                                    <div class="col-6 p-r-0">
                                                                        <div class="input-group">
                                                                            <select v-model="form.appointment__stid[getIndex(pkg, pky)]" :name="'appointment[stid]['+pky+']'" class="form-control" @change="getClosings(1, getIndex(pkg, pky))">
                                                                                <option disabled value="">Select Start Time</option>
                                                                                <option v-for="(timeslot, key) in start_times[getIndex(pkg, pky)]" :key="key" v-bind:value="key">
                                                                                    {{ timeslot }}
                                                                                </option>
                                                                            </select>
                                                                            <div class="input-group-append">
                                                                                <div class="input-group-text">
                                                                                    <input type="radio" v-model="form.appointment__schecked[getIndex(pkg, pky)]">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <select v-model="form.appointment__etid[getIndex(pkg, pky)]" :name="'appointment[etid]['+pky+']'" class="form-control">
                                                                            <option disabled value="">Select End Time</option>
                                                                            <option v-for="(timeslot, key) in end_times[getIndex(pkg, pky)]" :key="key" v-bind:value="key" v-if="key >= form.appointment__stid[getIndex(pkg, pky)]">
                                                                                {{ timeslot }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td v-else>--</td>
                                                            <td>
                                                                <select v-model="form.appointment__book_status[getIndex(pkg, pky)]" :name="'appointment[book_status]['+pky+']'" class="form-control">
                                                                    <option value="0">Do Not Book</option>
                                                                    <option value="2">Book It Now</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="PackageModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="PackageModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editmode" id="PackageModalTitle">Choose Package</h5>
                        <h5 class="modal-title" v-show="editmode" id="PackageModalTitle">Change Package</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="doc-schdular">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SNo</th>
                                        <th>Package</th>
                                        <th>Threatments</th>
                                        <th>Consultation</th>
                                        <th>Cost</th>
                                        <th>Duration</th>
                                        <th>Remark</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="dbody">
                                    <tr v-for="(therapy_package, tkey) in therapy_packages.data" :key="therapy_package.id">
                                        <td>{{ ++tkey }}</td>
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
                                        <td>
                                            <button type="button" v-if="form.package_id == therapy_package.id" class="btn btn-success btn-sm">Selected</button>
                                            <button type="button" v-else class="btn btn-primary btn-sm" @click="selectPackage(therapy_package.id)">Select</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="userModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Customer Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>User ID</b><br>{{ customer.username }}</p></li>
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name }} {{ customer.last_name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ customer.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.contact_no }}</p></li>
                            <li><p class="alert m-0"><b>City</b><br>{{ customer.city }}</p></li>
                            <li><p class="alert m-0"><b>Address</b><br>{{ customer.address }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ customer.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Identity Card</b><br>{{ customer.verification_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.identity_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Insurance</b><br>{{ customer.policy_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.insurance_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ customer.status }}</p></li>
                        </ul>
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
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                customers:[],
                tcount:[],
                customer:{},
                therapy_packages:'',
                tpackage:'',
                therapists:'',
                doctors:'',
                start_times:[],
                end_times:[],
                rooms:[],
                loader:false,
                loaderurl:'/svg/loop.gif',
                profile:'',
                iClass: 'form-control form-control-sm',
                form: new Form({
                    id:'',
                    user_id:'',
                    package_id:'',
                    package_name:'',
                    package_days:'',
                    remark:'',
                    amount:'',
                    status_id:2,
                    start_date:'',
                    end_date:'',
                    course_code:''
                }),
                capform: new Form({
                    date:'',
                    type_id:'',
                    tid:'',
                    doctor_id:'',
                    checked:'',
                    schecked:'',
                    echecked:'',
                    rid:'',
                    book_status:'',
                    stid:'',
                    etid:''
                })
            }
        },
        methods: {
            customFormatter(date) {
                return moment(date.getTime()).format('YYYY-MM-DD');
            },
            getIndex(list, id) {
                return id;
            },
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
            },
            setRange(lcount) {
                const arry = [];
                for (let i = 1; i <= lcount; i++) {
                    arry.push(i);
                }
                return arry;
            },
            getAssets() {
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                axios.get('/api/getPackagesList').then((data) => {  this.therapy_packages = data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {  this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {  this.therapists = data.data }).catch();
                axios.get('/api/get-active-user').then(response => { this.profile = response.data; });
            },
            showPackages() {
                $('#PackageModal').modal('show');
            },
            modifyPackage() {
                this.editmode = true;
                $('#PackageModal').modal('show');
            },
            selectPackage(pid) {
                axios.get('/api/getPackageDetail/'+pid)
                    .then((data) => {
                        this.tpackage = data.data;
                        this.form.package_id = data.data.id;
                        this.form.package_name = data.data.name;
                        this.form.amount = data.data.cost;
                        this.form.package_days = data.data.timeline;
                        this.getEndDate();
                    })
                    .catch();
                $('#PackageModal').modal('hide');
            },
            getEndDate() {
               let days = parseInt(this.form.package_days - 1) * 60 * 60 * 24 * 1000;
               let sdate = new Date(this.form.start_date);
               let edate = new Date(sdate.getTime() + days);
                this.form.end_date = edate.getFullYear()+'-'+("0" + parseInt(edate.getMonth()+1)).slice(-2)+'-'+("0" + edate.getDate()).slice(-2);
            },
            onDateSelect(tid, ttype, rkey){
                this.form.appointment__checked[rkey] = '';
                this.form.appointment__tid[rkey] = tid;
                if(ttype == 'c'){ this.form.appointment__type_id[rkey] = 1; }
                else{ this.form.appointment__type_id[rkey] = 2; }
            },
            getTimeSlots(atype, rkey) {
                this.form.appointment__schecked[rkey] = '';
                let did = this.form.appointment__doctor_id[rkey];
                let adate = this.form.appointment__date[rkey];
                axios.get('/api/appointments/get-doctor-appointment-timings?q='+adate+'&at='+atype+'&lid='+this.profile.location_id+'&did='+did).then(({ data }) => {
                    this.start_times[rkey] = data['start_slots'];
                    this.form.appointment__schecked[rkey] = '';
                });
                return true;
            },
            getClosings(atype, rkey){
                this.form.appointment__echecked[rkey] = '';
                let did = this.form.appointment__doctor_id[rkey];
                let st = this.form.appointment__stid[rkey];
                let adate = this.form.appointment__date[rkey];
                axios.get('/api/appointments/get-end-timings?q='+adate+'&at='+atype+'&lid='+this.profile.location_id+'&did='+did+'&st='+st).then(({ data }) => {
                    this.end_times[rkey] = data;
                    this.form.appointment__echecked[rkey] = '';
                });
            },
            getRooms(rkey){
                let ddate = this.form.appointment__date[rkey];
                let st = this.form.appointment__stid[rkey];
                let et = this.form.appointment__etid[rkey];
                axios.get('/api/appointments/get-rooms?q='+ddate+'&lid='+this.profile.location_id+'&st='+st+'&et='+et).then(({ data }) => {
                    this.rooms[rkey] = data;
                });
            },
            createSchedule() {
                this.loader = true;
                this.form.post('/api/courses/create-schedule-package/'+this.activeYear)
                .then((data) => {
                    swal.fire({
                        title: 'Creating course',
                        text: "Your course and appointment are being created. You will be redirected as process completes.",
                        type: 'success',
                        confirmButtonColor: '#06A23F',
                    }).then((result) => {
                        this.loader = false;
                        //this.$router.push('/appointments/course-packages/view/'+data.data);
                        let route = this.$router.resolve({path: '/appointments/course-packages/view/'+data.data});
                        window.open(route.href, '_self');
                    });
                })
                .catch((response) => {
                    this.loader = false;
                });
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id).then((data) => {this.customer = data.data[0] }).catch();
            },
        },
        beforeMount() {
            let activeId = this.$route.path.split("/");
            this.activeYear = activeId[3];
        },
        mounted() {
            this.getAssets();
        }

    }
</script>
