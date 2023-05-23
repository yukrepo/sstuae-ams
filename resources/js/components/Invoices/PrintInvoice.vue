<template>
    <div class="container text-center p-3">
        <div class="text-right border-bottom m-b-10 p-b-10">
            <button class="btn btn-lg wf-250 btn-secondary" @click="viewlink"><i class="fas fa-link"></i> Get Sharable Link</button>
            <button class="btn btn-lg wf-250 btn-green-theme" @click="sharelink"><i class="fas fa-share"></i> Share On whatsapp</button>
            <button class="btn btn-lg wf-250 btn-dark-theme" @click="printpage"><i class="fas fa-print"></i> Take Print Now</button>
        </div>
        <div id="printDiv">
            <header class="clearfix">
                <div class="container">
                    <figure>
                        <img class="logo" :src="imgurl+'logo.png'" alt="">
                    </figure>
                    <div class="company-address" style="position: relative; top: -15px;">
                        <h2 class="title">{{  configs.company_name }}</h2>
                        <div style="position: relative; top: -12px">
                            <div style="float:left" class="m-l-100">
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
                    </div>
                </div>
            </header>

            <section class="bodybox">
                <div class="container introbox">
                    <div class="row d-block m-0">
                        <h2>
                           TAX Invoice 
                            <p class="float-right text-right m-t-5 m-b-0" style="font-size:13px;"> Invoice #{{ invoice.invoice_number }} </p>
                        </h2>
                    </div>
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
                            <p class="bottom-bordered m-0" v-if="invoice.reference">
                                <b>REF :</b> {{ invoice.reference }}
                            </p>
                            <p class="bottom-bordered m-0" v-else-if="invoice.invoice_type == 5">
                                <b>REF :</b> {{ invoice.course_code }}
                            </p>
                            <p class="bottom-bordered m-0" v-else>
                                <b>REF :</b> {{ (appointment.new_doctor)?appointment.new_doctor:appointment.doctor | capitalize}}
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <p class="bottom-bordered m-0" >
                                <b>Pay Status:</b> {{(invoice.payment_status == 3)?'PAID':(invoice.payment_status == 2)?'Partial':'Credit' }}
                            </p>

                        </div>
                        <div class="col-4 text-right">
                            <p class="bottom-bordered m-0">
                                <b>DATE :</b> {{ invoice.invoice_date | setdate }}
                            </p>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped text-left mt-3 mb-0">
                        <thead v-if="invoice.invoice_type == 9">
                            <tr>
                                <th style="width:30px; text-align:left">SNo</th>
                                <th class="desc">PARTICULARS</th>
                                <th class="total">UNIT PRICE</th>
                                <th class="total">Quantity</th>
                                <th class="total">TOTAL ({{$root.$data.currency}})</th>
                            </tr>
                        </thead>
                        <thead v-else>
                            <tr>
                                <th style="width:30px; text-align:left">SNo</th>
                                <th class="desc">PARTICULARS</th>
                                <th class="total">AMOUNT ({{$root.$data.currency}})</th>
                            </tr>
                        </thead>
                        <tbody v-if="invoice.invoice_type == 9">
                            <tr v-for="(rd, rk) in invoice.rawdata" :key="rk">
                                <td>{{ rk+1 }}</td>
                                <td class="desc">
                                    <h6 class="m-0">{{ rd.heading }}</h6>
                                    <span v-show="rd.text">
                                        {{ rd.text }}
                                    </span>
                                </td>
                                <td class="total">
                                    {{ rd.cost  | formatOMR }}
                                </td>
                                <td class="total">
                                    {{ rd.qty }}
                                </td>
                                <td class="total">
                                    {{ rd.total  | formatOMR }}
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="invoice.invoice_type == 5">
                            <tr>
                                <td>1</td>
                                <td class="desc">
                                    <h6 class="m-0">{{ course.package_name }}</h6>
                                    <span v-if="course.consultation_Summary">
                                        <p v-for="(ts, tk) in JSON.parse(course.consultation_Summary)" :key="'c'+tk" style="margin:5px">
                                            <b> {{ ts.count }} </b> {{ ts.name }}
                                        </p>
                                    </span>
                                    <span v-if="course.treatments_Summary">
                                        <p v-for="(ts, tk) in JSON.parse(course.treatments_Summary)" :key="'t'+tk" style="margin:5px">
                                            <b> {{ ts.count }} </b> {{ ts.name }}
                                        </p>
                                    </span>
                                    
                                </td>
                                <td class="total">
                                    {{ invoice.amount  | formatOMR }}
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td>1</td>
                                <td class="desc">
                                    <h6 class="m-0">{{ appointment.reason }}</h6>
                                    <span v-show="appointment.visit_type_id == 2">
                                        Free Followup
                                    </span>
                                </td>
                                <td class="total">
                                    {{ invoice.amount  | formatOMR }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="no-break">
                        <table class="grand-total m-0">
                            <tbody>
                                <tr>
                                    <td class="unit">SUB TOTAL:</td>
                                    <td class="total">
                                        {{ invoice.amount | formatOMR }}</td>
                                </tr>
                                <tr v-show="invoice.bliss_discount_value > 0">
                                    <td class="unit">{{ invoice.bliss_discount }} :</td>
                                    <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                </tr>
                                <tr v-show="invoice.tax_value > 0">
                                    <td class="unit">{{ configs.tax_label }} :</td>
                                    <td class="total"> {{ invoice.tax_value | formatOMR }}</td>
                                </tr>
                                <tr style="font-weight: 600; color: #333; font-size: 1.18em;" >
                                    <td class="unit"><b>NET PAYABLE</b>:</td>
                                    <td class="total"> {{ (invoice.payable_amount)  | formatOMR }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="no-break">
                        <table class="grand-total m-0">
                            <tbody>
                                <tr>
                                    <td align="left">
                                        <amount-in-words :amount="invoice.payable_amount"></amount-in-words>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section>
                <table class="grand-total m-0">
                    <tr>
                        <td style="width:70%; vertical-align:top; border:none">
                            <div style="text-align:left"><strong>PAYMENT DETAILS</strong></div>
                            <transactions :payments="payments"></transactions>
                        </td>
                        <td  style="border:none">
                             <div style="display: block; text-align: center; border: 1px solid rgb(204, 204, 204); height: 120px; padding: 10px;">
                                Authorised Signatory &amp; Stamp
                            </div>
                        </td>
                    </tr>
                </table>
            </section>
            <footer>
                <div class="container clearfix">
                    <p class="thanks">
                        Address: {{ configs.address }}<br>
                        Contact: Tel : {{ configs.contact }}, Fax : {{ configs.fax }}, email : {{ configs.email }}
                    </p>
                </div>
            </footer>
        </div>
    </div>
</template>
<script>
import moment from "moment";
import Transactions from "./Tags/Transactions.vue";
import AmountInWords from "./Tags/AmountInWord.vue";

export default {
    components: { Transactions, AmountInWords },
    data() {
        return {
            bus: new Vue(),
            svgurl: "/svg/",
            docurl: "/files/docs/",
            imgurl: "/images/",
            editmode: false,
            catchmessage: "",
            currentYear: new Date().getFullYear(),
            activeID: "",
            active_location: "",
            appointment: "",
            listSlots: [],
            customer: {},
            medicines: [],
            profile: "",
            invoice: "",
            configs: this.$store.getters.configs,
            sharable_link: "",
            wlink: "",
            payments: [],
            course: {},
        };
    },
    methods: {
        printpage() {
            this.$htmlToPaper("printDiv");
        },
        getInvoice() {
            this.$Progress.start();
            axios
                .get("/api/invoices/view/" + this.activeID)
                .then(({ data }) => {
                    this.invoice = data;
                    if (data.rawdata) {
                        this.invoice.rawdata = JSON.parse(data.rawdata);
                    }
                    if (data.appointment_id) {
                        this.getAppointment(data.appointment_id);
                    }
                    if (data.invoice_type == 5) {
                        this.getPackage(data.course_code);
                    }
                    this.getCustomer(data.user_id);
                    this.getPayment(data.invoice_number);
                    this.$Progress.finish();
                });
        },
        getAppointment(aid) {
            axios
                .get("/api/appointments/view/" + aid)
                .then(({ data }) => {
                    this.appointment = data[0];
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getCustomer(uid) {
            axios
                .get("/api/customers/" + uid)
                .then((data) => {
                    this.customer = data.data[0];
                    this.sharable_link = window.btoa(
                        this.activeID + "-" + data[0].user_id
                    );
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getPackage(cid) {
            axios
                .get("/api/course-package/" + cid)
                .then((data) => {
                    this.course = data.data;
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getPayment(iid) {
            axios
                .post("/api/invoice-payments", { invoice_number: iid })
                .then((data) => {
                    this.payments = data.data;
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getAge(date) {
            let today = new Date();
            let ndate = new Date(date);
            var a = moment([
                today.getFullYear(),
                today.getMonth(),
                today.getDate(),
            ]);
            var b = moment([
                ndate.getFullYear(),
                ndate.getMonth(),
                ndate.getDate(),
            ]);
            var diffDuration = moment.duration(a.diff(b));
            return diffDuration.years() + " yrs";
        },
        makeNumber(val) {
            if (isNaN(val)) {
                return 0;
            } else {
                return parseFloat(val);
            }
        },
        sharelink() {
            let slink =
                "Please find your Invoice on the link below -  https://srisritattva.me/panchakarma/print/view-uctinvoice/" +
                this.sharable_link;
            let rpath =
                "//wa.me/" + this.customer.mobile + "?text=" + encodeURI(slink);
            let route = this.$router.resolve({ path: rpath });
            window.open(route.href, "_blank");
        },
        viewlink() {
            $("#confirmModal").modal("show");
        },
    },
    created() {
        let activeId = this.$route.path.split("/");
        this.activeID = activeId[3];
        this.getInvoice();
    },
};
</script>
