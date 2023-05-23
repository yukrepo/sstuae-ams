<template>
    <div>
        <div class="content appointmentDiv">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" v-model="sform.appointment_code" placeholder="App. ID">
                        </div>
                        <div class="col-md-2">
                            <vp-date-picker locale="en" format="YYYY-MM-DD" placeholder="Appointment Date" v-model="sform.date" :auto-submit="true" :max="max_date"></vp-date-picker>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.treatment_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Treatment/Consultation</option>
                                <option v-for="treatment in treatments" :key="treatment.id" v-bind:value="treatment.id">
                                    {{ treatment.treatment | capitalize }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
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
                                <h3 class="card-title">Appointment's History
                                    <div class="card-tools">
                                         <div class="btn-group" role="group" aria-label="First group">

                                            <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>

                                        </div>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                 <table id="upcomings" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">SNo</th>
                                            <th>ID</th>
                                            <th>Visit Type</th>
                                            <th>App. Type</th>
                                            <th>Date</th>
                                            <th>Timing</th>
                                            <th>Patient</th>
                                            <th>Treatment</th>
                                            <th>Doctor</th>
                                            <th class="wf-100">Added On</th>
                                            <th class="wf-120">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(appointment, sn) in appointments.data" :key="appointment.id">
                                            <td>{{  (appointments.current_page - 1)*(appointments.per_page) + sn + 1 }}</td>
                                            <td>{{ appointment.appointment_code }}</td>
                                            <td>{{ appointment.visit_type }}</td>
                                            <td>{{ appointment.appointment_type }}</td>
                                            <td>{{ appointment.date | setdate }}</td>
                                            <td>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                            <td><button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewCustomer(appointment.user_id)">{{ appointment.first_name | capitalize}} {{ appointment.last_name | capitalize}}</button>  </td>
                                            <td>{{ appointment.reason }}</td>
                                            <td>{{ appointment.doctor }}</td>
                                            <td>{{ appointment.created_at | setdate }}</td>
                                            <td>
                                                <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" @click="viewDetail(appointment.appointment_code)"><i class="fas fa-eye"></i></button>
                                                <a class="btn btn-primary btn-sm" :href="'/doctors/view-appointment/'+appointment.appointment_code"><i class="fas fa-laptop"></i></a>
                                            </td>
                                            <!-- <td v-else>
                                                <router-link class="btn btn-primary btn-sm" :to="'/doctors/view-appointment/'+appointment.appointment_code"><i class="fas fa-laptop"></i></router-link>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :limit="12" :data="appointments" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
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
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.mobile }}</p></li>
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
        <div class="modal fade" id="viewDetailModel"  data-backdrop="static" data-keyboard="false" >
            <div class="modal-dialog modal-full-width" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Appointment Quick View</h5>
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="modal-body popup-tabs p-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="ct-tab" data-toggle="tab" href="#ctdata" role="tab" aria-controls="cttab" aria-selected="true">Course/Treatment Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="medicine-tab" data-toggle="tab" href="#medicine" role="tab" aria-controls="medicine" aria-selected="false">Medicine Details</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="ctdata" role="tabpanel" aria-labelledby="ctdata">
                                <p v-if="loader1 == 1">
                                    <img class="wf-50" :src="loaderurl" alt="updating">
                                </p>
                                <p v-else-if="cappints.length == 0" class="text-danger">
                                    <i>No treatments was taken by customer</i>
                                </p>
                                <div v-else>
                                    <h5 class="text-uppercase">Course ID - <b>{{ cappints.course }}</b></h5>
                                    <div>
                                        <table class="table table-bordered table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="wf-75">SNo</th>
                                                    <th>ID</th>
                                                    <th>Treatment</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Therapist</th>
                                                    <th>Status</th>
                                                    <th>Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(cdata, ck) in cappints.appointments" :key="ck">
                                                    <td> {{ ck+1 }}</td>
                                                    <td>{{ cdata.appointment_code }}</td>
                                                    <td>{{ cdata.reason }}</td>
                                                    <td>{{ cdata.date | setdate }}</td>
                                                    <td>{{ listSlots[cdata.start_timeslot]+' - '+clistSlots[cdata.end_timeslot] }}</td>
                                                    <td>{{ cdata.doctor }}</td>
                                                    <td><label class="status-label-full" :class="cdata.status_css">{{  cdata.status }}</label></td>
                                                    <td>{{ (cdata.comment)?cdata.comment:'--' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="medicine" role="tabpanel" aria-labelledby="medicine-tab">
                                <p v-if="loader2 == 1">
                                    <img class="wf-50" :src="loaderurl" alt="updating">
                                </p>
                                <p v-else-if="medlists.length == 0" class="text-danger">
                                    <i>No medicines was taken by customer</i>
                                </p>
                                <div v-else>
                                    <div v-for="(mdata, mk) in medlists" :key="mk">
                                        <h5 class="text-uppercase">Invoice - <b>{{ mdata.invoice }}</b></h5>
                                        <table class="table table-bordered table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="wf-75">SNo</th>
                                                    <th>Medicine</th>
                                                    <th class="wf-75">Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(med, tk) in mdata.data" :key="tk">
                                                    <td> {{ tk+1 }} </td>
                                                    <td>{{ med.name }} - {{ med.batch_no }} </td>
                                                    <td>{{ med.qty }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Close </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
const _today = new Date();
const _todayComps = _today.getFullYear()+'-'+(_today.getMonth() + 1)+'-'+(_today.getDate()-1);
    export default {
        data() {
            return {
                startYear: 2018,
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear:'',
                appointments:{},
                customer: {},
                listSlots: [],
                clistSlots: [],
                loader: false,
                loader1: false,
                loader2: false,
                loaderurl: '/svg/loop.gif',
                cappints:'',
                medlists:'',
                sform: new Form({
                        date:'',
                        doctor_id:'',
                        treatment_id:'',
                        user_id:'',
                        appointment_code:'',
                        search:'',
                        year:''
                }),
                doctors:[],
                treatments:[],
                customers:[],
                max_date: _todayComps
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            getResults(page = 1) {
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                this.$Progress.start();
                axios.get('/api/appointments/get-history-yearwise/'+checkYear+'?page=' + page)
                    .then(({ data }) => {
                        this.appointments = data;
                        this.$Progress.finish();
                    });
            },
            showAppointments() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/appointments/get-history-yearwise/'+checkYear).then(({ data }) => {
                    this.appointments = data;
                    this.$Progress.finish();
                });
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
            },
            yearsList() {
                axios.get('/api/getPrevYearsList').then(({ data }) => (this.yearList = data));
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            deleteAppointment(id) {
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
                        this.form.delete('/api/appointments/'+id)
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
            searchAssets() {
                let activeId = this.$route.path.split("/");
                this.activeYear = activeId[3];
                axios.get('/api/getAllTreatmentsList').then(({ data }) => (this.treatments = data));
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
            },
            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/doctors/appointments-history/'+year);
                Fire.$emit('changeyear');
            },
            viewDetail(apcode){
                this.loader1 = 1;
                this.loader2 = 1;
                $('#viewDetailModel').modal('show');
                axios.get('/api/getInsMedListbyApt/'+apcode).then((data) => {this.medlists = data.data; this.loader2 = false; });
                axios.get('/api/getcourseAptListbyApt/'+apcode).then((data) => {this.cappints = data.data; this.loader1 = false; });
            },
            clearFilter() {
                this.sform.reset();
                this.showAppointments();
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                this.sform.year = this.activeYear;
                this.appointments = {};
                this.sform.post('/api/findDrHistoryAppointment')
                    .then(({data}) => {
                        this.appointments = data;
                         this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            }
        },
        beforeMount() {
            this.searchAssets();
        },
        mounted() {
            this.yearsList();
            this.showAppointments();
            Fire.$on('changeyear', () => {
                this.showAppointments();
            });
        }

    }
</script>
