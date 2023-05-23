<template>
    <div>
         <div class="content">
            <div class="container text-center p-b-25">
                <div class="text-right border-bottom m-b-10 p-b-10">
                    <button class="btn btn-lg wf-250 btn-dark-theme" @click="printpage"><i class="fas fa-print"></i> Take Print Now</button>
                </div>
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
                                <p class="float-right text-right m-t-10 m-b-0" style="font-size:13px;"> Ref No #{{ course.course_code }} </p>
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
                                <div class="col-md-6">
                                    <p class="bottom-bordered m-0">
                                        <b>REFERENCE :</b> {{ course.doctor | capitalize}}
                                    </p>
                                </div>
                                <!-- <div class="col-md-4 text-center">
                                        <p class="bottom-bordered m-0" v-if="invoice.insurance_id != 1">
                                        <b>Insured BY : {{ customer.insurancer | capitalize}}</b>
                                    </p>
                                    <p class="bottom-bordered m-0" v-else>
                                        <b>PAID BY : {{invoice.payment_mode }}</b>
                                    </p>
                                </div> -->
                                <div class="col-md-6" style="text-align:right">
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
                                        <th style="text-align:left">Status</th>
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
                                        <td style="text-align:left"> {{ appoint.status }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <section>
                            <div style="padding:15px;">
                                <div class="text-left" v-show="invoice.insurance_id == 1">PAYMENT STATUS:
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
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: '',
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
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
                axios.get('/api/get-configs').then(response => { this.configs = response.data; });
            },
            getList() {
                axios.get('/api/courses/'+this.activeID).then(({ data }) => {
                    this.course = data;
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
           /*  setInvoiceData() {
                let $ye = this; let $aform = this.aform; let subtotal = 0;
                this.aform.user_id = this.course.user_id;
                this.aform.course_code = this.course.course_code;
                this.aform.insurance_id = this.course.insurance_id;
                if(this.course.insurance_id == 1) {
                    axios.post('/api/appointments/get-bulk-cash-invoices', {
                        appointments:this.course.appointments
                    }).then((data) => {
                        $ye.acountlist = data.data.countlist;
                        data.data.appointments.forEach((element, key) => {
                            $aform.apt__reason.push(element.reason);
                            $aform.apt__apcode.push(element.appointment_code);
                            $aform.apt__datetime.push(element.date+' '+element.time);
                            $aform.apt__cost.push(element.cost);
                            subtotal = subtotal + element.cost;
                        });
                        $aform.sub_total = subtotal;
                        $aform.total = subtotal;
                    });
                }
                else {
                    this.discount = {'title':'No discount available', 'value':0};
                    axios.post('/api/appointments/get-bulk-insurance-invoices', {
                        appointments:this.course.appointments,
                        insurance_id: this.course.insurance_id
                    }).then((data) => {

                    })
                }

            }, */

        },
        beforeMount() {
            this.getProfile();
        },
        mounted() {
            this.getList();
        }
    }
</script>
