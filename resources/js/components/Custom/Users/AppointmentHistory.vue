<template>
    <div>
        <div class="p-2 border-bottom text-center">
            <div class="btn-group" role="group" aria-label="First group">
                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
            </div>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="wf-50">SNo</th>
                        <th>ID</th>
                        <th>Location</th>
                        <th>Visit Type</th>
                        <th>App. Type</th>
                        <th>Date</th>
                        <th>Timing</th>
                        <th>Treatment</th>
                        <th>Doctor</th>
                        <th class="wf-100">Added On</th>
                        <th class="text-left wf-120">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(appointment, sn) in appointments" :key="appointment.id">
                        <td>{{ sn + 1 }}</td>
                        <td>{{ appointment.appointment_code }}</td>
                        <td>{{ appointment.location }} </td>
                        <td>{{ appointment.visit_type }}</td>
                        <td>{{ appointment.appointment_type }}</td>
                        <td>{{ appointment.date | setdate }}</td>
                        <td>{{ listSlots[appointment.start_timeslot]+' - '+listSlots[appointment.end_timeslot] }}</td>
                        <td>{{ appointment.reason }}</td>
                        <td>{{ appointment.doctor }}</td>
                        <td>{{ appointment.created_at | setdate }}</td>
                        <td>
                            <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                        </td>
                       <!--  <td>
                            <router-link class="btn btn-primary btn-sm" :to="'/appointments/view/'+appointment.appointment_code"><i class="fas fa-desktop"></i></router-link>
                            <button class="btn btn-danger btn-sm" @click="deleteAppointment(appointment.appointment_code)"><i class="fas fa-trash"></i></button>
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['user_id', 'bus'],
        data() {
            return {
                startYear: 2018,
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                appointments:{},
                listSlots: '',
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            showAppointments() {
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/appointments/get-patient-history-yearwise/'+this.user_id+'/'+checkYear).then(({ data }) => {
                    this.appointments = data;
                    this.$Progress.finish();
                });
            },
            yearsList() {
                axios.get('/api/getAllYearsList').then(({ data }) => (this.yearList = data));
            },
            changeYear(year) {
                this.activeYear = year;
                Fire.$emit('changeyear');
            }
        },
        beforeMount() {
            this.activeYear = this.currentYear;
        },
        mounted() {

},
        created() {
            this.yearsList();
            this.showAppointments();
            Fire.$on('changeyear', () => {
                this.showAppointments();
            });
        }

    }
</script>
