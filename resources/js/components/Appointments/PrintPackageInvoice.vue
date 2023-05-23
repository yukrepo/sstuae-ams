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
                                        <img class="social" :src="imgurl+'facebook.png'" alt=""> {{ configs.facebook_page }} </li>
                                    <li class="text-right">
                                        <img class="social" :src="imgurl+'twitter.png'" alt=""> {{ configs.twitter_page }} </li>
                                    <li class="text-right">
                                        <img class="social" :src="imgurl+'whatsapp.png'" alt=""> {{ configs.whatsapp_number }} </li>
                                </ul>
                            </div>

                        </div>
                    </header>

                    <section class="bodybox">
                        <div class="container introbox">
                            <h2>Cash Invoice </h2>
                            <p class="text-right"> Invoice No: {{ invoice.invoice_number }} </p>
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
                                <div class="col-md-7">
                                    <p class="bottom-bordered m-0">
                                        <b>Reference :</b> {{ course.course_code }}
                                    </p>
                                </div>
                                <div class="col-md-5 text-right">
                                    <p class="bottom-bordered m-0">
                                        <b>DATE-TIME : {{ invoice.created_at | setfulldate }}</b>
                                    </p>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped text-left m-0">
                                <thead>
                                    <tr>
                                        <th style="width:70px">SNo</th>
                                        <th class="desc">PARTICULARS</th>
                                        <th class="total">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td class="desc">
                                            <b>{{ therapy_package.name | uppercase }}</b>
                                            <br>
                                            <small>
                                                <span v-for="(test, tekey) in therapy_package.alltreatments" :key="'ite-'+tekey">
                                                    <b>{{ test.count }} </b> {{test.name}}<br>
                                                </span>
                                                <span v-for="(ctest, cekey) in therapy_package.allconsultation" :key="'icte-'+cekey">
                                                    <b>{{ ctest.count }} </b> {{ctest.name}}<br>
                                                </span>
                                            </small>
                                        </td>
                                        <td class="total">{{ invoice.amount | formatOMR }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        <tr style="font-weight: 600; color: #333; font-size: 1.18em;">
                                            <td class="unit"><b>TOTAL PAYABLE </b>:</td>
                                            <td class="total"> {{ (invoice.amount)  | formatOMR }}</td>
                                        </tr>
                                        <tr style="font-weight: 600; color: #333; font-size: 1.18em;">
                                            <td class="unit"><b>Paid By Customer </b>:</td>
                                            <td class="total"> {{ invoice.payable_amount | formatOMR }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"> {{ word_amount }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                    <section v-if="invoice.payment_status <= 1 || invoice.partialpay.length == []">
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
                    </section>
                    <section v-else-if="invoice.payment_status >= 2">
                        <div class="row m-0">
                            <div class="col-6 text-left pl-4">
                                <div v-show="invoice.payable_amount > 0">
                                    <div class="text-left"><strong>PAYMENT DETAILS</strong></div>
                                    <div class="notice text-left">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left; background:#e4e4e4; color:#000">SNo</th>
                                                    <th style="text-align:left; background:#e4e4e4; color:#000">Date</th>
                                                    <th style="text-align:left; background:#e4e4e4; color:#000">Amount (RO)</th>
                                                    <th style="text-align:left; background:#e4e4e4; color:#000">Pay Mode</th>
                                                    <th style="text-align:left; background:#e4e4e4; color:#000">Txn</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <tr v-for="(pay_detail, okey) in invoice.partialpay" :key="'p-'+okey">
                                                     <td>{{ okey+1 }}</td>
                                                     <td>{{ pay_detail.date }}</td>
                                                     <td>{{ pay_detail.paid_amount | formatOMR }}</td>
                                                     <td>{{ pay_detail.pay_mode }}</td>
                                                     <td>{{ pay_detail.txn }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="padding: 100px 15px 10px;">
                                        Customer Signatory
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="padding: 100px 15px 10px;">
                                        Authorised Signatory &amp; Stamp
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
                courseyear:'',
                therapy_package:'',
                currentYear: new Date().getFullYear(),
                activeID: '',
                active_location: '',
                appointment:'',
                listSlots: [],
                customer: {},
                medicines:[],
                course:'',
                profile:'',
                invoice:'',
                configs:'',
                appointments:'',
                word_amount:''
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
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    this.RialBaisa(data.payable_amount);
                    this.courseyear = '20'+data.course_code.substr(1,2);
                    axios.get('/api/courses/get-package-detail/'+data.course_code)
                        .then((data2 ) => {
                            console.log(data2);
                            this.course = data2.data.course;
                            this.therapy_package = data2.data.therapy_package;
                         //   axios.get('/api/packages/'+data2.data.course.package_id).then((data4) => { this.therapy_package = data4.data;}).catch();
                    });
                    axios.get('/api/customers/'+data.user_id)
                        .then((data) => {this.customer = data.data[0] })
                        .catch();
                });
                this.$Progress.finish();
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
        beforeMount() {
            let activeId = this.$route.path.split("/");
            this.activeID = activeId[3];
            this.getProfile();
        },
        mounted() {
            this.getInvoice();
        }

    }
</script>
