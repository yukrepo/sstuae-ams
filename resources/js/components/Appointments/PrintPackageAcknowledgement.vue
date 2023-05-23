
<template>
    <div>
        <div class="content-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/doctors">Home</router-link></li>
                <li class="breadcrumb-item">
                    <router-link :to="'/appointments/course-packages/'+courseyear">Schedule Packages</router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link :to="'/appointments/course-packages/view/'+course.course_code">View Course</router-link>
                </li>
                <li class="breadcrumb-item active">Acknowledgement</li>
            </ol>
        </div>
         <div class="content">
            <div class="container-fluid">
                <div class="form-group">
                    <div class="button-group">
                        <router-link class="btn btn-sm btn-primary" :to="'/appointments/course-packages/view/'+course.course_code">View Course </router-link>
                        <router-link class="btn btn-sm btn-outline-secondary" :to="'/appointments/course-packages/'+courseyear">Courses List </router-link>
                        <span class="float-right">
                            <button class="btn btn-sm btn-dark-theme" @click="printpage"><i class="fas fa-print"></i> Take Print Now</button>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="container text-center p-b-25">
                    <div id="printDiv">
                        <header class="clearfix">
                            <div class="container">
                                <figure>
                                    <img class="logo" :src="imgurl+'big-logo.png'" alt="">
                                </figure>
                                <div class="company-address">
                                    <h2 class="title">{{  configs.company_name }}</h2>
                                        <ul>
                                        <li class="text-left">
                                            <img class="social" :src="imgurl+'domain.png'" alt=""> {{ configs.website }} </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'facebook.png'" alt=""> {{ configs.facebook_page }}
                                        </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'instagram.png'" alt=""> {{ configs.instagram_page }} </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'whatsapp.png'" alt=""> {{ configs.whatsapp_number }} </li>
                                    </ul>
                                </div>
                            </div>
                        </header>

                        <section class="bodybox">
                            <div class="container introbox">
                                <h2>
                                    <span>Course Acknowledgement </span>
                                </h2>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="bottom-bordered">
                                            <b>PATIENT ID :</b> {{ customer.username }}
                                        </p>
                                    </div>
                                    <div class="col-md-5 text-center">
                                        <p class="bottom-bordered">
                                            <b>NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <p class="bottom-bordered">
                                            <b>AGE :</b> {{ getAge(customer.date_of_birth) }}
                                            <span v-if="customer.gender == 1"> (M) </span>
                                            <span v-else-if="customer.gender == 2"> (F) </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <p class="bottom-bordered m-0">
                                            <b>REFERENCE :</b> {{ course.course_code | capitalize}}
                                        </p>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5" style="text-align:right">
                                        <p class="bottom-bordered m-0">
                                            <b>DATE-TIME : {{ course.created_at | setfulldate }}</b>
                                        </p>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped text-left m-0">
                                    <thead>
                                        <tr>
                                            <th  style="text-align:left">SNo</th>
                                            <th style="text-align:left" >Appt. ID</th>
                                            <th style="text-align:left" >Therapy</th>
                                            <th style="text-align:left">Date</th>
                                            <th style="text-align:left">Time</th>
                                            <th style="text-align:left">Therapist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(appoint, akey) in appointments" :key="akey">
                                            <td  style="text-align:left">{{ akey+1 }}</td>
                                            <td style="text-align:left"> {{ appoint.appointment_code }} </td>
                                            <td style="text-align:left"> {{ appoint.reason }} </td>
                                            <td style="text-align:left"> {{ appoint.date | setdate }} </td>
                                            <td style="text-align:left"> {{ appoint.time }} </td>
                                            <td style="text-align:left"> {{ appoint.doctor }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <section>
                                <div style="padding:15px;">
                                    <div class="text-left">PAYMENT STATUS:
                                        <strong>
                                            <span class="pay" v-if="course.pstatus == 1">PENDING</span>
                                            <span class="pay" v-else-if="course.pstatus == 2">PARTIALLY PAID</span>
                                            <span class="pay" v-else-if="course.pstatus == 3">PAID</span>
                                            <span class="pay" v-else>UNKNOWN</span>
                                        </strong>
                                    </div>
                                </div>
                                <div class="row m-0" style="padding:50px 20px 0;">
                                    <div class="col-md-6" style="text-align:left">
                                         <p>Customer Signatory</p>
                                    </div>
                                    <div class="col-md-6" style="text-align:right">
                                         <p class="sign">Authorised Signatory &amp; Stamp</p>
                                    </div>
                                </div>
                            </section>
                        </section>
                        <footer>
                            <div class="container clearfix">
                                <p class="thanks">
                                    Location : {{ configs.location }}<br>
                                    Address: {{ configs.address }}<br>
                                    Contact: Tel : {{ configs.contact }}, Fax : {{ configs.fax }}, email : {{ configs.email }}
                                </p>
                            </div>
                        </footer>
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
                svgurl: '/svg/',
                docurl: '/files/docs/',
                imgurl: '/images/',
                editmode: false,
                courseyear:'',
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: this.$route.params.id,
                active_location: '',
                course:'',
                appointments:'',
                listSlots: [],
                customer: {},
                medicines:[],
                profile:'',
                invoice:'',
                configs:'',
                word_amount: ''
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            printpage() {
                this.$htmlToPaper('printDiv');
            },
            getProfile() {
                axios.get('/api/get-profile').then(response => { this.profile = response.data; });
                axios.get('/api/get-configs').then(response => { this.configs = response.data; });
            },
            getList() {
                axios.get('/api/courses/view-direct-course/'+this.$route.params.id).then(({ data }) => {
                    this.course = data;
                     this.courseyear = '20'+data.course_code.substr(1,2);
                    axios.get('/api/customers/'+this.course.user_id).then((data3) => { this.customer = data3.data[0];}).catch();
                     axios.post('/api/appointments/get-bulk-appointments', {
                        appointments: data.appointments,
                    }).then((data2) => { this.appointments = data2.data; });
                });
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return diffDuration.years()+' yrs';
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },

        },
        created() {
            this.getList();
            this.getProfile();
        }
    }
</script>
