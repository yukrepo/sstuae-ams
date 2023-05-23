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
                        <router-link class="btn btn-sm btn-dark" :to="'/appointments/print-invoice/'+appointment.ainvoice" v-if="appointment.ainvoice">Print Invoice </router-link>
                        <button type="button" class="btn btn-sm btn-danger" v-show="(appointment.cinvoice == activeID) && (appointment.iinvoice)" @click="switchInvoice(appointment.iinvoice)">Switch Pharmacy Invoice </button>
                        <button type="button" class="btn btn-sm btn-danger" v-show="(appointment.iinvoice == activeID) && (appointment.cinvoice)" @click="switchInvoice(appointment.cinvoice)">Switch Pharmacy Invoice </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary wf-200 dropdown-toggle" data-toggle="dropdown">
                            Return To Page </button>
                            <div class="dropdown-menu rdroplink wf-200">
                                <router-link class="dropdown-item text-outline-success" to="/appointments/day-schedule">Todays Appointment </router-link>
                                <router-link class="dropdown-item text-outline-primary" :to="'/appointments/upcoming-list/'+currentYear">Upcoming Appointments </router-link>
                                <router-link class="dropdown-item text-outline-secondary" :to="'/appointments/history/'+currentYear">History </router-link>
                            </div>
                        </div>
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
                                        <h2>Medicines Invoice </h2>
                                        <p class="text-right"> Invoice No: {{ invoice.invoice_number }} </p><br>
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
                                                <p class="bottom-bordered m-0" v-if="invoice.insurance_id > 1">
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
                                                    <th class="sno">SNo</th>
                                                    <th class="desc">PARTICULARS</th>
                                                    <th>Unit Price (RO)</th>
                                                    <th class="total">Qty</th>
                                                    <th class="total">Total (RO)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(med, key) in sale_medicines" :key="key">
                                                    <td class="sno">{{ key+1 }}</td>
                                                    <td class="desc">
                                                        <h6>{{ med.rowdata.name }}</h6>
                                                        <p class="m-0">( Batch/Exp: {{ med.rowdata.batch_no }}</p>
                                                    </td>
                                                    <td class="total">{{ med.rowdata.cost | formatOMR }}</td>
                                                    <td class="total">{{ med.rowdata.qty }}</td>
                                                    <td class="total">{{ med.rowdata.total | formatOMR }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="unit">NET AMOUNT (RO):</td>
                                                    <td class="total"> {{ invoice.amount | formatOMR }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="no-break">
                                            <table class="grand-total">
                                                <tbody>
                                                    <tr v-show="invoice.bliss_discount_value > 0">
                                                       <td class="unit">{{ invoice.bliss_discount }}:</td>
                                                        <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.bliss_discount_value > 0">
                                                        <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                                        <td class="total"> {{ (invoice.amount - invoice.bliss_discount_value)  | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-show="invoice.ins_disc_value >= 0">
                                                        <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                                        <td class="total"> {{ (invoice.amount - invoice.ins_disc_value) | formatOMR }}</td>
                                                    </tr>
                                                    <tr style="font-weight: 600; color: #333; font-size: 1.18em;">
                                                        <td class="unit"><b>Paid By Customer </b>:</td>
                                                        <td class="total"> {{ invoice.payable_amount | formatOMR }}</td>
                                                    </tr>
                                                    <tr v-show="invoice.insurance_id > 1">
                                                       <td class="unit"><b>Payable By {{ customer.insurancer | capitalize}} </b>:</td>
                                                        <td class="total"> {{ invoice.coinsurance_value | formatOMR }}</td>
                                                    </tr>
                                                    <tr >
                                                       <td colspan="2"> {{ word_amount }} </td>
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
                                                <div class="pay"><span>Payment Method:</span> {{ invoice.payment_mode }}</div>
                                                <div class="pay" v-show="(invoice.payment_mode != 'cash') || (invoice.payment_mode != 'credit')"><span>Txn No:</span> {{ invoice.txn_id }}</div>
                                                 <div class="pay"><span>Created By:</span> {{ invoice.admin | capitalize }}</div>
                                            </div>
                                        </div>
                                        <div class="row text-right" style="padding-top:30px">
                                            <div class="col-md-6">
                                                <p class="sign">Customer Signatory</p>
                                            </div>
                                            <div class="col-md-6" style="text-align:right">
                                                <p class="sign">Authorised Signatory &amp; Stamp</p>
                                            </div>
                                        </div>

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
                configs:'',
                invoice:'',
                sale:'',
                word_amount:'',
                sale_medicines:''
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
                    if(data.insurance_id > 1) {
                        this.RialBaisa(data.coinsurance_value);
                    } else {
                        this.RialBaisa(data.payable_amount);
                    }
                    axios.get('/api/appointments/view/'+data.appointment_id).then(({ data }) => {
                        this.appointment = data[0];
                        axios.get('/api/customers/'+data[0].user_id)
                            .then((data) => {this.customer = data.data[0] })
                            .catch();
                    });
                    axios.get('/api/sales/view/'+data.invoice_number).then(({ data }) => {
                        this.sale = data;
                        axios.get('/api/sale-medicines/'+data.id)
                            .then((data) => {this.sale_medicines = data.data })
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
            switchInvoice(val) {
                this.$router.push('/appointments/print-pharmacy-invoice/'+val);
                this.activeID = val;
                this.getInvoice();
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
