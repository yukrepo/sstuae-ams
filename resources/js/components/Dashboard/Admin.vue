<template>
    <div>
        <div class="row synop m-0 pt-2">
            <div class="col-md-4 col-sm-6 col-12 m-b-5 px-1">
                <div class="card m-b-10">
                    <div class="card-header">
                        <h3 class="card-title">Doctors Scoreboard</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Score</th>
                                    <th>Todays Report</th>
                                </tr>
                            </thead>
                            <tbody v-if="loader">
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">
                                            <img :src="loaderurl" alt="Loading...">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(doctor, akey) in score.doctors" :key="akey">
                                    <td> <b>{{ doctor.name | uppercase }}</b></td>
                                    <td><small>YESTERDAY APPOINTMENTS</small>: <b class="float-right">{{ doctor.dayscore }}</b> <br>
                                        <small>MONTH APPOINTMENTS</small>: <b class="float-right">{{ doctor.monthscore }}</b> </td>
                                    <td><small>DAY APPOINTMENTS</small>: <b class="float-right">{{ doctor.todayscore }}</b> <br>
                                        <small>APPOINTMENTS</small>: <b class="float-right">{{ doctor.todaycount }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card m-b-10">
                    <div class="card-header">
                        <h3 class="card-title">Therapists Scoreboard</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>APPOINTMENTS</th>
                                    <th>Todays Report</th>
                                </tr>
                            </thead>
                            <tbody v-if="loader">
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">
                                            <img :src="loaderurl" alt="Loading...">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(therapist, akey) in score.therapists" :key="akey">
                                    <td> <b>{{ therapist.name | uppercase }}</b>
                                        <img class="wf-25" :src="imgurl+'day-star.png'" v-show="(therapist.dayscore > 6.5)">
                                        <img class="wf-25" :src="imgurl+'month-star.png'" v-show="(therapist.monthscore >= 150)">
                                       </td>
                                    <td><small>YESTERDAY APPOINTMENTS</small>: <b class="float-right">{{ therapist.dayscore }}</b> <br>
                                        <small>MONTH APPOINTMENTS</small>: <b class="float-right">{{ therapist.monthscore }}</b> </td>
                                    <td><small>DAY APPOINTMENTS</small>: <b class="float-right">{{ therapist.todayscore }}</b> <br>
                                        <small>APPOINTMENTS</small>: <b class="float-right">{{ therapist.todaycount }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12 m-b-5 px-1">
                <div class="card m-b-10">
                    <div class="card-header">
                        <h3 class="card-title">Appointment Summary</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Booked</th>
                                    <th>Active</th>
                                    <th>Cancelled</th>
                                    <th>Deleted</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody v-if="loader">
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">
                                            <img :src="loaderurl" alt="Loading...">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(sapp, akey) in synops.appointments" :key="akey">
                                    <td><b>{{ sapp.year | uppercase }}</b></td>
                                    <td class="text-center"> {{ sapp.total | freeNumber }} </td>
                                    <td class="text-center"> {{ sapp.active | freeNumber }} </td>
                                    <td class="text-center"> {{ sapp.cancelled | freeNumber }} </td>
                                    <td class="text-center"> {{ sapp.deleted | freeNumber }} </td>
                                    <td class="text-center"> {{ sapp.completed | freeNumber }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card m-b-5">
                    <div class="card-header">
                        <h3 class="card-title">Customers Summary</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <tbody v-if="loader">
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">
                                            <img :src="loaderurl" alt="Loading...">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td>Total Registered </td>
                                    <td> {{ (synops.users)?synops.users.total:'' | freeNumber }} </td>
                                </tr>
                                <tr>
                                    <td>Registered By AL-Khuwair Clinic</td>
                                    <td> {{ (synops.users)?(synops.users.clinic1 + synops.users.clinic):'' | freeNumber }} </td>
                                </tr>
                                <tr>
                                    <td>Registered By KIMS Clinic</td>
                                    <td> {{ (synops.users)?synops.users.clinic2:'' | freeNumber }} </td>
                                </tr>
                                <tr>
                                    <td>Registered By APP </td>
                                    <td> {{ (synops.users)?synops.users.byapp:'' | freeNumber }} </td>
                                </tr>
                                <tr>
                                    <td>Registered by Web</td>
                                    <td> {{ (synops.users)?synops.users.byweb:'' | freeNumber }} </td>
                                </tr>
                                 <tr>
                                    <td>Registered by Other Sources</td>
                                    <td> {{ (synops.users)?(synops.users.total - (synops.users.byweb+synops.users.byapp+synops.users.clinic2+synops.users.clinic+synops.users.clinic1)):'' | freeNumber }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12 m-b-5 px-1">
                <div class="card m-b-10">
                    <div class="card-header">
                        <h3 class="card-title">New Appointment Requests From Users </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-condensed table-stripped">
                            <thead>
                                <tr>
                                    <th>SNo</th>
                                    <th>User</th>
                                    <th>Reason</th>
                                    <th>Date-Time</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody v-if="loader">
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">
                                            <img :src="loaderurl" alt="Loading...">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(appointment, akey) in appointments" :key="akey">
                                    <td>{{ akey+1 }}</td>
                                    <td>{{ appointment.first_name+' '+appointment.last_name | capitalize }}</td>
                                    <td>{{ appointment.reason }}</td>
                                    <td>{{ appointment.date | setdate }} ({{ listSlots[appointment.start_timeslot]+' - '+clistSlots[appointment.end_timeslot] }})</td>
                                    <td> {{ appointment.remark }} </td>
                                </tr>
                            </tbody>
                        </table>
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
                loader: false,
                loaderurl: '/images/spinner.gif',
                score:'',
                imgurl: '/images/',
                synops:'',
                appointments:{}
            }
        },
        computed: {
            user() {
                return this.$store.getters.user
            }
        },
        methods: {
            
            getAllScores() {
                this.loader = true;
                axios.get('/api/get-admin-score').then(response => { this.score = response.data; this.loader = false; });
                axios.get('/api/get-admin-synops').then(response => { this.synops = response.data; this.loader = false;  });
                axios.get('/api/get-new-requests').then(response => { this.appointments = response.data; this.loader = false;  });
            },
        },
        beforeMount() {
            this.$store.dispatch('setUser');
        },
        created() {
            this.getAllScores();
        }
    }
</script>
