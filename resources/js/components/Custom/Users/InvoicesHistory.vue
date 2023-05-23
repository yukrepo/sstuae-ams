<template>
    <div>
        <div class="p-2 border-bottom text-center">
            <div class="btn-group" role="group" aria-label="First group">
                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeIYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
            </div>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="wf-75">SNo</th>
                        <th>Invoice Type</th>
                        <th>Invoice Number</th>
                        <th>Amount</th>
                        <th>Insurance</th>
                        <th>Created By</th>
                        <th class="wf-120">Invoice Date</th>
                        <th class="text-center wf-100">Status</th>
                        <th class="wf-175">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(invoice, sn) in invoices" :key="invoice.id" :class="[(invoice.cancel != '0') ? 'bg-lightpink' : '' ]">
                        <td>{{  sn + 1 }}</td>
                        <td v-if="invoice.invoice_type >= 8">
                            <label class="status-label-full-outline text-warning">Unknown</label>
                        </td>
                        <td v-else>
                            <label class="status-label-full-outline text-dark">{{ invoice_types[invoice.invoice_type] }}</label>
                        </td>
                        <td class="font-weight-bold">{{ invoice.invoice_number }}</td>
                        <td>{{ invoice.amount | formatOMR }}</td>
                        <td v-if="invoice.cancel != '0'">
                            --
                        </td>
                        <td v-else-if="invoice.insurance_id == 1">
                            <label class="status-label-full bg-primary">Cash</label>
                        </td>
                        <td v-else>
                            <span v-if="invoice.ins_clearance == 1">
                                <label class="status-label-full bg-teal">cleared</label>
                            </span>
                            <span v-else>
                                <label class="status-label-full bg-pink">pending</label>
                            </span>
                        </td>
                            <td>{{ invoice.admin | capitalize }}</td>
                        <td>{{ invoice.invoice_date | setdate }}</td>
                        <td v-if="invoice.cancel != '0'">
                            --
                        </td>
                        <td align="center" v-else-if="invoice.payment_status == 1">
                            <label class="status-label-full bg-teal">Paid</label>
                        </td>
                        <td align="center" v-else>
                            <label class="status-label-full bg-pink">Pending</label>
                        </td>
                        <td>
                            <span v-if="invoice.invoice_type == 1">
                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/invoices/print-uinvoice/'+invoice.invoice_number">Customer</router-link>
                                <router-link target="_blank" class="btn btn-dark btn-sm" :to="'/invoices/print-iinvoice/'+invoice.invoice_number" v-show="invoice.insurance_id != 1">Insurance</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 2">
                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/invoices/print-uinvoice/'+invoice.invoice_number">Customer</router-link>
                                <router-link target="_blank" class="btn btn-dark btn-sm" :to="'/invoices/print-iinvoice/'+invoice.invoice_number" v-show="invoice.insurance_id != 1">Insurance</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 3">
                                <router-link target="_blank" class="btn btn-dark btn-sm" :to="'/invoices/print-pharmacy-iinvoice/'+invoice.invoice_number">Print</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 4">
                                <router-link target="_blank" class="btn btn-dark btn-sm" :to="'/invoices/print-pharmacy-uinvoice/'+invoice.invoice_number">Print</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 5">
                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/invoices/print-coursep-invoice/'+invoice.invoice_number">Customer</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 6">
                                <router-link target="_blank" class="btn btn-dark btn-sm" :to="'/invoices/print-course-invoice/'+invoice.invoice_number">Print</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 7">
                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/invoices/print-dpinvoice/'+invoice.invoice_number">Customer</router-link>
                            </span>
                            <span v-else-if="invoice.invoice_type == 8">
                                <router-link target="_blank" class="btn btn-primary btn-sm" :to="'/invoices/print-dcourse-invoice/'+invoice.invoice_number">Customer</router-link>
                            </span>
                            <span v-else>

                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['user_id', 'bus'],
        data() {
            return {
                startYear: 2018,
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                invoices:{},
                invoice_types:['Unknown', 'Consultation', 'Treatment', 'Ins Pharmacy', 'Cash Pharmacy', 'Package', 'Prescibed', 'Direct Medicine', 'Direct Course'],
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            showAppointments() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/invoices/get-patient-history-yearwise/'+this.user_id+'/'+checkYear).then(({ data }) => {
                    this.invoices = data;
                    this.$Progress.finish();
                });
            },
            yearsList() {
                axios.get('/api/getAllYearsList').then(({ data }) => (this.yearList = data));
            },
            changeIYear(year) {
                this.activeYear = year;
                Fire.$emit('changeIyear');
            }
        },
        mounted() {
            this.activeYear = this.currentYear;
        },
        created() {
            this.yearsList();
            this.showAppointments();
            Fire.$on('changeIyear', () => {
                this.showAppointments();
            });
        }

    }
</script>
