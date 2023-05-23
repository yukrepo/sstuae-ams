<template>
    <div>
        <div class="content-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/doctors">Home</router-link></li>
                <li class="breadcrumb-item">Appointments</li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </div>
         <div class="content">
            <div class="container-fluid">
                <div class="form-group">
                    <div class="button-group">
                        <router-link class="btn btn-sm btn-primary" :to="'/appointments/view/'+invoice.appointment_id">View Appointment </router-link>
                        <router-link class="btn btn-sm btn-success" :to="'/appointments/print-perscription/'+invoice.appointment_id">Print Prescription</router-link>
                        <router-link class="btn btn-sm btn-danger" :to="'/appointments/print-pharmacy-invoice/'+appointment.pinvoice" v-if="appointment.pinvoice">Print Pharmacy Invoice </router-link>
                        <router-link class="btn btn-sm btn-green-theme-outline" to="/appointments/day-schedule">Todays Appointment </router-link>
                        <router-link class="btn btn-sm btn-outline-primary" :to="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </router-link>
                        <router-link class="btn btn-sm btn-outline-secondary" :to="'/appointments/history/'+currentYear">History </router-link>
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
                                    <span v-if="appointment.appointment_type_id == 1">Consultation Invoice </span>
                                    <span v-else-if="appointment.appointment_type_id == 2">Treatment Invoice </span>
                                    <p class="float-right text-right m-t-10 m-b-0" style="font-size:13px;"> Invoice #{{ invoice.invoice_number }} </p>
                                </h2>
                                <br>
                                <div class="row">
                                    <div class="col-4">
                                        <p class="bottom-bordered">
                                            <b>PATIENT ID :</b> {{ customer.username }}
                                        </p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="bottom-bordered">
                                            <b>NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}
                                        </p>
                                    </div>
                                    <div class="col-4 text-right">
                                        <p class="bottom-bordered">
                                            <b>AGE :</b> {{ getAge(customer.date_of_birth) }}
                                            <span v-if="customer.gender == 1"> (Male) </span>
                                            <span v-else-if="customer.gender == 2"> (Female) </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <p class="bottom-bordered m-0">
                                            <b>REFERENCE :</b> {{ appointment.doctor | capitalize}}
                                        </p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="bottom-bordered m-0" v-if="invoice.inc_deduction_value != 0">
                                            <b>Insured BY : {{ customer.insurancer | capitalize}}</b>
                                        </p>
                                        <p class="bottom-bordered m-0" v-else>
                                            <b v-if="invoice.payment_mode == 'cc_dc'">PAID BY:  VISA CARD</b>
                                            <b v-else>PAID BY : {{invoice.payment_mode }}</b>
                                        </p>

                                    </div>
                                    <div class="col-4 text-right">
                                        <p class="bottom-bordered m-0">
                                            <b>DATE-TIME : {{ invoice.created_at | setfulldate }}</b>
                                        </p>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped text-left m-0">
                                    <thead>
                                        <tr>
                                            <th style="width:70px; text-align:left">SNo</th>
                                            <th class="desc">PARTICULARS</th>
                                            <th class="total">AMOUNT (RO)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td class="desc">
                                                <h6 class="m-0">{{ appointment.reason }}</h6>
                                                <span v-show="appointment.visit_type_id == 2">
                                                    Free Followup
                                                </span>
                                            </td>
                                            <td class="total" v-if="invoice.showdiscount == 1">
                                                {{ invoice.amount | formatOMR }}
                                            </td>
                                            <td class="total" v-else>
                                                {{ (invoice.amount - invoice.ins_disc_value) | formatOMR }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="no-break">
                                    <table class="grand-total m-0">
                                        <tbody>
                                            <tr>
                                                <td class="unit"><b>TOTAL </b>:</td>
                                                <td class="total" v-if="invoice.showdiscount == 1">
                                                    {{ invoice.amount | formatOMR }}</td>
                                                <td class="total" v-else>
                                                    {{ (invoice.amount - invoice.ins_disc_value) | formatOMR }}
                                                </td>
                                            </tr>
                                            <tr v-show="invoice.bliss_discount_value > 0">
                                                <td class="unit">{{ invoice.bliss_discount }} :</td>
                                                <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                            </tr>
                                            <tr v-show="(invoice.ins_disc_value != 0) && (invoice.showdiscount == 1)">
                                                <td class="unit"><b>{{ customer.insurancer | capitalize}} Discount </b>:</td>
                                                <td class="total"> {{ invoice.ins_disc_value | formatOMR }}</td>
                                            </tr>
                                            <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.bliss_discount_value">
                                                <td class="unit"><b>NET PAYABLE</b>:</td>
                                                <td class="total"> {{ (invoice.amount - invoice.bliss_discount_value)  | formatOMR }}</td>
                                            </tr>
                                            <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.insurance_id > 1">
                                                <td class="unit"><b>NET PAYABLE (A) </b>:</td>
                                                <td class="total"> {{ (invoice.amount - invoice.ins_disc_value) | formatOMR }}</td>
                                            </tr>
                                            <tr style="font-weight: 600; color: #333; font-size: 1.18em;">
                                                <td class="unit" v-if="invoice.insurance_id == 1">
                                                    <b>Paid By Customer</b>:
                                                </td>
                                                <td class="unit" v-else>
                                                    <b>Deductible Received (B) </b>:
                                                </td>
                                                <td class="total"> {{ invoice.payable_amount | formatOMR }}</td>
                                            </tr>
                                            <tr v-show="invoice.inc_deduction_value != 0">
                                                <td class="unit"><b>Payable By {{ customer.insurancer | capitalize}} (A-B)</b>:</td>
                                                <td class="total"> {{ invoice.inc_deduction_value | formatOMR }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="no-break">
                                    <table class="grand-total m-0">
                                        <tbody>
                                            <tr>
                                                <td align="left">
                                                    <b>{{ word_amount }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section>
                            <div style="padding:15px">
                                <div v-show="invoice.payable_amount > 0">
                                    <div class="text-left"><strong>PAYMENT DETAILS</strong> (Paid By Customer)</div>
                                    <div class="notice text-left">
                                        <div class="pay"><span>Payment Method:</span>
                                        <span v-if="invoice.payment_mode == 'cc_dc'"> VISA CARD</span>
                                        <span v-else>{{invoice.payment_mode }}</span>
                                        </div>
                                        <div class="pay" v-show="(invoice.payment_mode != 'cash')"><span>Txn No:</span> {{ invoice.txn_id }}</div>
                                        <div class="pay"><span>Created By:</span> {{ invoice.admin | capitalize }}</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section v-show="appointment.appointment_type_id == 1">
                            <div style="padding: 20px;">
                                <span style="width:45%; display: inline-block;" v-show="customer.insurance_copy">
                                    <img class="print-img"  v-img:docurl+customer.insurance_copy :src="this.docurl+customer.insurance_copy">
                                </span>
                                <span style="width:45%; display: inline-block;" v-show="customer.identity_copy">
                                    <img class="print-img" v-img:docurl+customer.identity_copy :src="docurl+customer.identity_copy">
                                </span>
                            </div>
                        </section>
                        <section>
                            <div style="padding: 20px;">
                                <span style="width:48%; display: inline-block; text-align: left;">
                                    Customer Signatory
                                </span>
                                <span style="width:48%; display: inline-block; text-align: right;">
                                    Authorised Signatory &amp; Stamp
                                </span>
                            </div>
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
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: this.$route.params.id,
                active_location: '',
                appointment:'',
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
            getInvoice() {
                this.$Progress.start();
                axios.get('/api/invoices/view/'+this.$route.params.id).then(({ data }) => {
                    this.invoice = data;
                    //let cost = data.payable_amount.toFixed(3);
                    if(data.inc_deduction_value > 0) {
                        this.RialBaisa(data.inc_deduction_value);
                    } else {
                        this.RialBaisa(data.payable_amount);
                    }
                    axios.get('/api/appointments/view/'+data.appointment_id).then(({ data }) => {
                        this.appointment = data[0];
                        axios.get('/api/customers/'+data[0].user_id)
                            .then((data) => {this.customer = data.data[0];   })
                            .catch();
                    });
                    this.$Progress.finish();
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
            Rial(amount) {
                var words = new Array();
                words[0] = 'Zero';
                words[1] = 'One';
                words[2] = 'Two';
                words[3] = 'Three';
                words[4] = 'Four';
                words[5] = 'Five';
                words[6] = 'Six';
                words[7] = 'Seven';
                words[8] = 'Eight';
                words[9] = 'Nine';
                words[10] = 'Ten';
                words[11] = 'Eleven';
                words[12] = 'Twelve';
                words[13] = 'Thirteen';
                words[14] = 'Fourteen';
                words[15] = 'Fifteen';
                words[16] = 'Sixteen';
                words[17] = 'Seventeen';
                words[18] = 'Eighteen';
                words[19] = 'Nineteen';
                words[20] = 'Twenty';
                words[30] = 'Thirty';
                words[40] = 'Forty';
                words[50] = 'Fifty';
                words[60] = 'Sixty';
                words[70] = 'Seventy';
                words[80] = 'Eighty';
                words[90] = 'Ninety';
                var op;
                amount = amount.toString();
                var atemp = amount.split(".");
                var number = atemp[0].split(",").join("");
                var n_length = number.length;
                var words_string = "";
                if (n_length <= 9) {
                    var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                    var received_n_array = new Array();
                    for (var i = 0; i < n_length; i++) {
                        received_n_array[i] = number.substr(i, 1);
                    }
                    for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                        n_array[i] = received_n_array[j];
                    }
                    for (var i = 0, j = 1; i < 9; i++, j++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            if (n_array[i] == 1) {
                                n_array[j] = 10 + parseInt(n_array[j]);
                                n_array[i] = 0;
                            }
                        }
                    }
                    let value = "";
                    for (var i = 0; i < 9; i++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            value = n_array[i] * 10;
                        } else {
                            value = n_array[i];
                        }
                        if (value != 0) {
                            words_string += words[value] + " ";
                        }
                        if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Crores ";
                        }
                        if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Lakhs ";
                        }
                        if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Thousand ";
                        }
                        if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                            words_string += "Hundred and ";
                        } else if (i == 6 && value != 0) {
                            words_string += "Hundred ";
                        }
                    }
                    words_string = words_string.split(" ").join(" ");
                }
                return words_string;
            },
            RialBaisa(n) {
                let nums;
                let op; let amt;
                nums = n.toString().split(".");
                var whole = this.Rial(nums[0])
                if (nums[1] == null) nums[1] = 0;
                if (nums[1].length == 1) nums[1] = nums[1] + '00';
                if (nums[1].length == 2) nums[1] = nums[1] + '0';

                if (nums.length == 2) {
                    if (nums[0] <= 9) {
                        nums[0] = nums[0] * 10
                    } else {
                        nums[0] = nums[0]
                    };
                    var fraction = this.Rial(nums[1]);
                    if (whole == '' && fraction == '') {
                        op = 'Zero only';
                    }
                    if (whole == '' && fraction != '') {
                        op = 'Baiza ' + fraction + ' only';
                    }
                    if (whole != '' && fraction == '') {
                        op = 'OMR ' + whole + 'and Baiza Zero only'
                    }
                    if (whole != '' && fraction != '') {
                        op = 'OMR ' + whole + 'and Baiza ' + fraction + ' only';
                    }
                    amt = n;
                    if (amt > 999999999.99) {
                        op = 'Oops!!! The amount is too big to convert';
                    }
                    if (isNaN(amt) == true) {
                        op = 'Error : Amount in number appears to be incorrect. Please Check.';
                    }
                    this.word_amount = op;
                }
            }

        },
        created() {
            this.getInvoice();
            this.getProfile();
        }
    }
</script>
