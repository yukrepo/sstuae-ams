<template>
    <div>
        <div class="content">
            <div class="container text-center p-b-25">
                <div class="text-right border-bottom m-b-10 p-b-10">
                    <button class="btn btn-lg wf-250 btn-green-theme float-left" v-show="course.status_id < 5" @click="makeCompleteInvoice"><i class="fas fa-invoice"></i> Make It Invoice</button>
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
                                        <img class="social" :src="imgurl+'facebook.png'" alt="">
                                        <img class="social" :src="imgurl+'instagram.png'" alt="">
                                        <img class="social" :src="imgurl+'youtube.png'" alt=""> {{ configs.facebook_page }}
                                    </li>
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
                            <h2>
                                <span>Course Invoice</span>
                                <p class="float-right text-right m-t-10 m-b-0" style="font-size:13px;"> Invoice No #{{ activeID }} </p>
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
                                        <b>REFERENCE :</b> {{ course.doctor | capitalize}}
                                    </p>
                                </div>
                                <div class="col-4 text-center">
                                    <p class="bottom-bordered m-0">
                                        <b>Insured BY : {{ customer.insurancer | capitalize}}</b>
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
                                        <th class="desc">TREATMENT</th>
                                        <th>Price (RO)</th>
                                        <th>Count</th>
                                        <th>SubTotal (RO)</th>
                                        <th>Discount (RO)</th>
                                        <th>Net Amount (RO)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(treatment, key) in treatments" :key="key">
                                        <td>{{ key+1 }}</td>
                                        <td>{{ treatment.treatment }} Sessions</td>
                                        <td style="text-align:right">{{ treatment.cost | formatOMR }}</td>
                                        <td style="text-align:right">{{ treatment.qty }}</td>
                                        <td style="text-align:right">{{ treatment.subtotal | formatOMR }}</td>
                                        <td style="text-align:right">{{ treatment.discount | formatOMR }}</td>
                                        <td style="text-align:right">{{ treatment.total | formatOMR }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total m-0">
                                    <tbody>
                                        <tr style="font-weight: 600; color: #333; font-size: 1em;">
                                            <td class="unit"><b>Sub Total </b>:</td>
                                            <td class="total">{{ sub_total | formatOMR }}</td>
                                        </tr>
                                        <tr style="font-weight: 600; color: #333; font-size: 1em;" v-show="discount_value">
                                            <td class="unit"><b>{{ discount }} </b>:</td>
                                            <td class="total">{{ discount_value | formatOMR }}</td>
                                        </tr>
                                        <tr style="font-weight: 600; color: #333; font-size: 1.25em;">
                                            <td class="unit"><b>NET PAYABLE By  {{ customer.insurancer | capitalize}}</b>:</td>
                                            <td class="total">{{ total | formatOMR }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                {{ word_amount }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                    <section>
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
        <div class="modal fade" id="setInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setInvocieModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="updateInvoice">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Course Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <span class="mydatepicker  m-r-10">
                                <date-picker v-model="aform.invoice_date" lang="en" type="datetime" format="YYYY-MM-DD HH:mm:ss" confirm></date-picker>
                            </span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 1">
                            <button type="submit" class="btn btn-wide btn-success" v-else>Submit</button>
                        </div>
                    </form>
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
                loader: false,
                loaderurl: '/svg/loop.gif',
                svgurl: '/svg/',
                docurl: '/files/docs/',
                imgurl: '/images/',
                editmode: false,
                invoice:'',
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: '',
                active_location: '',
                course:'',
                discount:'',
                discount_value:'',
                treatments:'',
                listSlots: [],
                customer: {},
                medicines:[],
                profile:'',
                invoice:'',
                configs:'',
                word_amount: '',
                total:'',
                sub_total:'',
                discount:'',
                discount_value:'',
                aform: new Form({
                    type:6,
                    invoice_date:'',
                    total:'',
                    invoice_number:'',
                    ins_disc_value:'',
                    ins_disc:'',
                    sub_total:'',
                    ins_amount:'',
                    rawdata:'',
                }),
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
                axios.get('/api/get-configs').then(response => { this.configs = response.data; });
            },
            makeCompleteInvoice() {
                $('#setInvocieModal').modal('show');
            },
            getInvoice() {
                this.$Progress.start();
                let $aform = this.aform;
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    axios.get('/api/courses/'+data.course_code).then((cdata) => {
                        this.course = cdata.data;
                        $aform.invoice_number = cdata.data.primary_invoice;
                        $aform.course_code = cdata.data.course_code;
                        axios.post('/api/set-insured-treatments', {
                            appointment_code:cdata.data.consult_code,
                            invoice_number:cdata.data.invoice
                        }).then((data2) => {
                            this.treatments =  data2.data.treatments;
                            this.discount_value =  data2.data.discount_value;
                            this.discount =  data2.data.discount;
                            this.total =  data2.data.total;
                            this.sub_total =  data2.data.sub_total;

                            $aform.sub_total = data2.data.sub_total;
                            $aform.total = 0;
                            $aform.ins_amount = data2.data.total;
                            $aform.ins_disc_value = data2.data.discount_value;
                            $aform.ins_disc = data2.data.discount;
                            $aform.rawdata = data2.data.treatments;

                            this.RialBaisa(data2.data.total); }).catch();
                    });
                    axios.get('/api/customers/'+data.user_id).then((udata) => {this.customer = udata.data[0]; }).catch();
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
            },
            updateInvoice() {
                if(this.aform.invoice_date == '') {
                    swal.fire('Missing Values', 'Please enter invoice date.', 'error');
                        return false;
                }
                this.loader = 1;
                this.aform.post('/api/invoices/update-insurance-course-invoice')
                .then((data) => {
                    this.loader = false;
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print Invoice',
                        cancelButtonText: 'Stay On The Page',
                    }).then((result) => {
                        if (result.value) {
                            $('#setInvocieModal').modal('hide');
                            this.getInvoice();
                            let route = this.$router.resolve({path: '/invoices/print-course-invoice/'+data.data.invoice});
                            window.open(route.href, '_blank');
                        }
                    }).catch(error => {
                        this.loader = false;
                        swal.showValidationMessage(`Request failed: ${error}`);
                    });
                })
                .catch(error => {
                    this.loader = false;
                    swal.fire(`Request failed: ${error.message}`);
                });
            },

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
