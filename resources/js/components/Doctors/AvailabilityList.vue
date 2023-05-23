<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Availability</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Blocked Date's List </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">SNo</th>
                                            <th>Date</th>
                                            <th>Remark</th>
                                            <th>Timings</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="text-align:center; width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(appointment, sn) in appointments.data" :key="appointment.id">
                                            <td>{{ ++sn }}</td>
                                            <td>{{ appointment.appointment_code }}</td>
                                            <td>{{ appointment.visit_type }}</td>
                                            <td>{{ appointment.appointment_type }}</td>
                                            <td>{{ appointment.date | setdate }}</td>
                                            <td>{{ setSlots(JSON.parse("[" + appointment.time_slots + "]")) }}</td>
                                           <td>{{ appointment.name }}  <button class="btn btn-primary btn-sm list-btn" @click="viewCustomer(appointment.user_id)"><i class="fas fa-laptop"></i></button></td>
                                            <td>{{ appointment.reason }}</td>
                                            <td>{{ appointment.doctor }}</td>
                                            <td>{{ appointment.created_at | setdate }}</td>
                                            <td align="center">
                                                <label class="status-label-full" :class="appointment.status_css">{{  appointment.status }}</label>
                                            </td>
                                            <td>
                                                <router-link class="btn btn-primary btn-sm" :to="'/appointments/view/'+appointment.id"><i class="fas fa-desktop"></i></router-link>
                                                <router-link class="btn btn-warning btn-sm" :to="'/appointments/edit/'+appointment.id"><i class="fas fa-edit"></i></router-link>
                                                <button class="btn btn-danger btn-sm" @click="deleteCustomer(appointment.id)"><i class="fas fa-trash"></i></button>
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
    export default {
        data() {
            return {
                editmode: false,
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/availabilities?page=' + page)
                    .then(response => {
                        this.blockdata = response.data;
                    });
            },
            getAvailabilityList() {
                this.$Progress.start();
                axios.get('/api/availabilities').then(({ data }) => {
                    this.blockdata = data;
                    this.$Progress.finish();
                });
            }
        },
        created() {
            this.getAvailabilityList();
        }
    }
</script>
