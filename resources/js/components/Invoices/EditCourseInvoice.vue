<template>
    <div>
         <div class="content">
            <div class="container-fluid">
                <div class="py-1">
                    <div class="button-group">
                        <a class="btn btn-sm btn-primary" :href="'/appointments/courses/view/'+invoice.course_code">View Course </a>
                        <span class="float-right">
                           <a class="btn btn-sm btn-outline-secondary" :href="'/invoices/courses/'+currentYear">Back To List </a>
                        </span>
                    </div>
                </div>
                <div class="p-b-25">
                    <div class="card">
                        <form @submit.prevent="updateInvoice">
                            <div class="card-header">
                                <h5 class="card-title">Update Invoice</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-4">
                                        <h5 class="bottom-bordered">
                                            <b>PATIENT ID :</b> {{ customer.username }}
                                        </h5>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h5 class="bottom-bordered">
                                            <b>NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name | capitalize }}
                                        </h5>
                                    </div>
                                    <div class="col-4 text-right">
                                        <h5 class="bottom-bordered">
                                            <b>AGE :</b> {{ getAge(customer.date_of_birth) }}
                                            <span v-if="customer.gender == 1"> (Male) </span>
                                            <span v-else-if="customer.gender == 2"> (Female) </span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <h5 class="py-2">
                                            <b>REFERENCE :</b> {{ course.doctor | capitalize}}
                                        </h5>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h5 class="py-2" v-if="invoice.insurance_id != 1">
                                            <b>Insured BY : {{ customer.insurancer | capitalize}}</b>
                                        </h5>
                                        <h5 class="py-2" v-else>
                                            <b>PAID BY : {{invoice.payment_mode }}</b>
                                        </h5>

                                    </div>
                                    <div class="col-4 text-right">
                                        <h5 class="py-2">
                                            <b>DATE-TIME : {{ invoice.updated_at | setfulldate }}</b>
                                        </h5>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped text-left m-0">
                                    <thead>
                                        <tr>
                                            <th style="width:70px; text-align:left">SNo</th>
                                            <th class="desc">PARTICULARS</th>
                                            <th>Price (RO)</th>
                                            <th>Discount (RO)</th>
                                            <th class="total">AMOUNT (RO)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(rdata, rkey) in JSON.parse(invoice.rawdata)" :key="rkey">
                                            <td> {{ rkey+1 }} </td>
                                            <td class="desc">
                                                <h6 class="m-0">{{ rdata.treatment }}</h6>
                                              <!--   <p  class="m-0">( On {{ rdata.datetime }} )</p> -->
                                            </td>
                                            <td class="total">
                                                {{ (rdata.cost) | formatOMR }}
                                            </td>
                                            <td class="total">
                                                {{ (rdata.discount) | formatOMR }}
                                            </td>
                                            <td class="total">
                                                {{ (rdata.total) | formatOMR }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="no-break">
                                    <table class="grand-total m-t-10 table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td class="unit text-right"><b>TOTAL </b>:</td>
                                                <td class="total">
                                                    {{ invoice.amount | formatOMR }}
                                                </td>
                                            </tr>
                                            <tr v-show="invoice.bliss_discount_value">
                                                <td class="unit text-right">{{ invoice.bliss_discount }} :</td>
                                                <td class="total"> {{ invoice.bliss_discount_value | formatOMR }}</td>
                                            </tr>
                                            <tr style="font-weight: 600; color: #333; font-size: 1.18em;"  v-if="invoice.insurance_id == 1">
                                                <td class="unit text-right"><b>NET PAYABLE</b>(A) :</td>
                                                <td class="total"> {{ (invoice.payable_amount)  | formatOMR }}</td>
                                            </tr>
                                            <tr v-show="invoice.insurance_id > 1">
                                                <td class="unit text-right"><b>Payable By {{ customer.insurancer | capitalize}}</b>:</td>
                                                <td class="total"> {{ invoice.inc_deduction_value | formatOMR }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                            </div>
                            <div class="card-footer text-right">
                                <span class="mydatepicker  m-r-10">
                                    <date-picker v-model="aform.invoice_date" lang="en" type="datetime" format="YYYY-MM-DD HH:mm:ss" confirm></date-picker>
                                </span>
                                <span v-if="loader">
                                    <img class="wf-50" :src="loaderurl" alt="updating">
                                </span>
                                <span v-else>
                                    <button type="submit" class="btn btn-wide btn-success"> Update Invoice </button>
                                </span>
                            </div>
                        </form>
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
                activeID: '',
                active_location: '',
                course:'',
                listSlots: [],
                customer: {},
                medicines:[],
                profile:'',
                invoice:'',
                configs:'',
                loader: false,
                loaderurl: '/svg/loop.gif',
                aform: new Form({
                        user_id:'',
                        invoice_number:'',
                        invoice_date:'',
                    }),
            }
        },
        methods: {
            getIndex(list, id) {
                return list;
            },
            getProfile() {
                axios.get('/api/get-configs').then(response => { this.configs = response.data; });
            },
            getInvoice() {
                this.$Progress.start();
                axios.get('/api/invoices/view/'+this.activeID).then(({ data }) => {
                    this.invoice = data;
                    this.aform.invoice_number = data.invoice_number ;
                    this.aform.invoice_date = data.invoice_date ;
                    this.aform.user_id = data.user_id ;
                    axios.get('/api/courses/'+data.course_code).then(({ data }) => {
                        this.course = data;
                        axios.get('/api/customers/'+data.user_id)
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
            updateInvoice() {
                if(this.aform.invoice_date === null) {
                    swal.fire('Missing Values', 'Please mention invoice date', 'error');
                    return false;
                }

                this.loader = true;
                this.aform.post('/api/invoices/icmodify')
                .then((data) => {
                    this.$Progress.start();
                    swal.fire({
                        title: 'Invoice has been updated successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Print This Invoice',
                        cancelButtonText: 'Stay On This Page',
                        footer: '<a href="/invoices/appointments/'+this.currentYear+'">Go Back To List</a>'
                        }).then((result) => {
                            this.loader = false;
                            if(this.aform.insurance_id == 1) {
                                let route = this.$router.resolve({path: '/invoices/print-course-cinvoice/'+data.data.invoice});
                                window.open(route.href, '_blank');
                            } else {
                                let route = this.$router.resolve({path: '/invoices/print-course-invoice/'+data.data.invoice});
                                window.open(route.href, '_blank');
                            }
                        });
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.loader = false;
                });
            },

        },
        beforeMount() {
            this.getProfile();
            let activeId = this.$route.path.split("/");
            this.activeID = activeId[3];
        },
        mounted() {
            this.getInvoice();

        }
    }
</script>
