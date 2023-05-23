<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Active Timing Slots</h3>
                            </div>
                            <div class="card-body p-0 content-box">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>TimeSlot</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(time, count) in ontimings" :key="time.id">
                                            <td>{{ count + 1 }}</td>
                                            <td>{{ time.time }} - {{ time.closing }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="switchSlots(time)"><i class="fas fa-edit"></i> Switch</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Off Timing Slots</h3>
                            </div>
                            <div class="card-body p-0  content-box">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>TimeSlot</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(time, count) in offtimings" :key="time.id">
                                            <td>{{ count + 1 }}</td>
                                            <td>{{ time.time }} - {{ time.closing }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="switchSlots(time)"><i class="fas fa-edit"></i> Switch</button>
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
    </div>
</template>
<script>
import moment from 'moment';
    export default {
        data() {
            return {
                editmode: false,
                offtimings: {},
                ontimings: {},
                options:{format: 'YYYY-MM-DD', minDate:moment(new Date())},
                form: new Form({
                    id:'',
                    value:'',
                    title:'',
                    start_date:'',
                    end_date:'',
                    time_slots:[],
                    type:'',
                    status_id:'2',
                    consultation:'',
                    pharmacy:'',
                    treatment:''
                })
            }
        },
        methods: {
            showTimeSlots() {
                this.$Progress.start();
                axios.get('/api/get-off-time-slots').then(({ data }) => {  this.offtimings = data; });
                axios.get('/api/get-on-time-slots').then(({ data }) => {  this.ontimings = data; this.$Progress.finish(); });
            },
            switchSlots(slot) {
                axios.post('/api/modify-slots', {
                    id:slot.id,
                    season:slot.season
                })
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    this.$toaster.success('Timeslot has been switched.');
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
        },
        mounted() {
            this.showTimeSlots();
        },
        created() {
            Fire.$on('AfterCreate', () => {
                this.showTimeSlots();
            });
        }
    }
</script>
