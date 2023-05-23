<template>
    <div>
        <div class="mt-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Upcoming Appointments
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
                                            <th style="width: 100px;">Added On</th>
                                            <th style="text-align:center; width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(appointment, sn) in appointments" :key="appointment.id">
                                            <td>{{ ++sn }}</td>
                                            <td>{{ appointment.appointment_code }}</td>
                                            <td>{{ appointment.visit_type }}</td>
                                            <td>{{ appointment.appointment_type }}</td>
                                            <td>{{ appointment.date | setdate }}</td>
                                            <td>{{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }}</td>
                                           <td>{{ appointment.first_name | capitalize}} </td>
                                            <td>{{ appointment.reason }}</td>
                                            <td>{{ appointment.created_at | setdate }}</td>
                                            <td align="center">
                                                <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
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
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.name }}</p></li>
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
    export default {
        data() {
            return {
                currentYear: new Date().getFullYear(),
                yearList:[],
                activeYear: [],
                appointments:'',
                customer: {},
                listSlots: [],
                clistSlots: [],
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            searchAssets() {
                let activeId = this.$route.path.split("/");
                this.activeYear = activeId[3];
            },
            showAppointments() {
                this.$Progress.start();
                axios.get('/api/appointments/get-personal-upcomings-yearwise/'+this.activeYear).then(({ data }) => {
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
                axios.get('/api/getNxtYearsList').then(({ data }) => (this.yearList = data));
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();

            },

            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/doctors/appointments-upcoming-list/'+year);
                Fire.$emit('changeyear');
            }
        },
        beforeMount() {
            this.searchAssets();
            axios.get('/api/get-active-user').then(response => {this.profile = response.data;});
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
