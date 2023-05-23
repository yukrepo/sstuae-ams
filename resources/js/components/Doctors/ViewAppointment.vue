<template>
    <div>
         <div class="content">
            <div class="container-fluid">
                <div class="form-group">
                    <div class="button-group row">
                        <span v-if="appointment.ainvoice" class="col-md-6 col-xs-12 text-left">
                            <a class="btn btn-sm btn-success" :href="'/appointments/print-perscription/'+appointment.appointment_code">Print Prescription</a>
                            <a v-show="todayreferer" class="btn btn-sm btn-warning" :href="'/doctors/prescribe/'+appointment.appointment_code">Edit Prescription</a>
                        </span>
                        <span v-else class="col-md-6 col-xs-12 text-left">

                        </span>
                        <span class="col-md-6 col-xs-12 text-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary wf-200 dropdown-toggle" data-toggle="dropdown">Return To Page </button>
                                <div class="dropdown-menu rdroplink wf-200">
                                   <a class="dropdown-item text-outline-primary" :href="'/doctors/appointments-upcoming-list/'+currentYear">Upcoming Appointment </a>
                                   <a class="dropdown-item text-outline-secondary" href="/doctors/appointments-day-schedule"> Todays Appointment </a>
                                   <a class="dropdown-item text-outline-danger" :href="'/doctors/appointments-history/'+currentYear">Appointments History</a>
                                </div>
                            </div>

                        </span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Appointment View</h3>
                    </div>
                    <div class="card-body p-t-15 p-b-15 p-r-15 p-l-15">
                        <div class="form-group">
                            <h5>Primary Appointment Details</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Visit Type</th>
                                        <th>Type</th>
                                        <th>Consulatation</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ appointment.appointment_code }}</td>
                                        <td>{{ appointment.visit_type }}</td>
                                        <td>{{ appointment.appointment_type }}</td>
                                        <td>{{ appointment.reason }}</td>
                                        <td>{{ appointment.date | setdate }}</td>
                                        <td>{{ appointment.time_slots }}</td>
                                        <td> <button class="btn btn-sm" :class="appointment.status_css">{{ appointment.status }} </button> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <h5>Patient Details</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Married</th>
                                        <th>D.O.B. (Age)</th>
                                        <th>Address</th>
                                        <th>Insurance</th>
                                        <th>Identity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span v-if="customer.gender == 1">
                                                <img class="img-icon" :src="svgurl+'boy.svg'" alt="Male">
                                            </span>
                                            <span v-else-if="customer.gender == 2">
                                                <img class="img-icon" :src="svgurl+'girl.svg'" alt="Female">
                                            </span>
                                        {{ customer.username }}</td>
                                        <td>{{ customer.first_name }} {{ customer.last_name }}</td>
                                        <td>{{ customer.mobile }}</td>
                                        <td>{{ (customer.email)?customer.email:'--' }}</td>
                                        <td>{{ customer.married | capitalize }}</td>
                                        <td>{{ customer.date_of_birth | setdate }} {{ getAge(customer.date_of_birth) }}</td>
                                        <td>{{ customer.address }}, {{ customer.city }}</td>
                                        <td><img class="btn-img" v-img:docurl+customer.insurance_copy :src="this.docurl+customer.insurance_copy"> {{ customer.insurancer | capitalize }} ({{ customer.policy_number }})
                                        </td>
                                        <td><img class="btn-img" v-img:docurl+customer.identity_copy :src="docurl+customer.identity_copy"> {{ customer.id_type | capitalize }} ({{ customer.verification_number }})</td>
                                        <td>
                                            <button class="btn btn-sm" :class="customer.css">{{ customer.status  }} </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <h5>Prescription Details</h5>
                            <div class="row">
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="3">General Examination</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(oe_category, okey) in JSON.parse(appointment.oe_categories)" :key="'o-'+okey">
                                                <td>{{ oe_category.name }}</td>
                                                <td>{{ oe_category.result }}</td>
                                                <td>{{ oe_category.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Diagnosis</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(diagnosis, dkey) in JSON.parse(appointment.diagnosis)" :key="'d-'+dkey">
                                                <td>{{ diagnosis.name }}</td>
                                                <td>{{ diagnosis.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Symptoms</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(symptom, skey) in JSON.parse(appointment.symptoms)" :key="'s-'+skey">
                                                <td>{{ symptom.name }}</td>
                                                <td>{{ symptom.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Lab Tests</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(test, tekey) in JSON.parse(appointment.tests)" :key="'te-'+tekey">
                                                <td>{{ test.type }} - {{ test.name }}</td>
                                                <td>{{ test.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Drug History</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(dhistory, tkey) in JSON.parse(appointment.drug_history)" :key="'t-'+tkey">
                                                <td>{{ tkey.replace('_', ' ') | capitalize }}</td>
                                                <td>{{ dhistory }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Remarks</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2">{{ (appointment.dr_remark)?appointment.dr_remark:'-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Followup Reminder</td>
                                                <td>{{ appointment.reminder_date | setdate }} <br>
                                                    {{ (appointment.reminder_days)?appointment.reminder_days+' days':'' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="3">Medicines</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(medicine, mkey) in JSON.parse(appointment.medicines)" :key="'m-'+mkey">
                                                <td>{{ medicine.name }}</td>
                                                <td>{{ medicine.qty | freeNumber }} Tabs, {{ medicine.days | freeNumber }} days - {{ medicine.dtab }}</td>
                                                <td>{{ medicine.remark }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 m-b-10">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr><th colspan="2">Therapies</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(therapy, tkey) in JSON.parse(appointment.therapies)" :key="'t-'+tkey">
                                                <td>{{ therapy.qty }} {{ therapy.name }} In {{ therapy.days }} days</td>
                                                <td>{{ therapy.remark }}</td>
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
    </div>
</template>
<script>
import moment from 'moment';
    export default {
        data() {
            return {
                svgurl: '/svg/',
                docurl: '/files/docs/',
                editmode: false,
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: '',
                active_location: '',
                appointment:'',
                listSlots: [],
                customer: {},
                medicines:[],
                todayreferer:false
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return '('+diffDuration.years()+' yrs)';
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
            },
            getAllAssets() {
                axios.get('/api/getOeCategoriesSelectList').then((data) => {this.oecategories = data.data }).catch();
                axios.get('/api/getDiseasesSelectList').then((data) => {this.diseases = data.data }).catch();
                axios.get('/api/getTreatmentsSelectList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getSymptomsSelectList').then((data) => {this.symptoms = data.data }).catch();
                axios.get('/api/getMedicinesSelectList').then((data) => {this.medicines = data.data }).catch();
                axios.get('/api/getXTestsSelectList').then((data) => { this.xtests = data.data }).catch();
                axios.get('/api/getDTestsSelectList').then((data) => { this.dtests = data.data }).catch();
            },
            showAppointment() {
                this.$Progress.start();
                axios.get('/api/appointments/view/'+this.activeID).then(({ data }) => {
                    this.appointment = data[0];
                    let today = new Date();
                    var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                    if(moment(data[0].date).isSame(a) == true) {
                        this.todayreferer = true;
                    } else {
                        this.todayreferer = false;
                    }
                    axios.get('/api/customers/'+this.appointment.user_id)
                        .then((data) => {this.customer = data.data[0] })
                        .catch();
                    this.$Progress.finish();
                });

            },
            getAppointmentHistory() {
                this.$Progress.start();
                axios.get('/api/appointments/user-history/'+this.activeID).then(({ data }) => {
                    //console.log(data);
                    this.appointment_histories = data;
                    this.$Progress.finish();
                });
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            setID() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
                console.log(this.activeId);
            }
        },
         beforeMount() {
            this.setID();
        },
        mounted() {
            this.showAppointment();

        }

    }
</script>
