<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ title }}</h3>
            </div>
            <div class="card-body p-0 table-responsive content-box-200">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="wf-50">SNo</th>
                            <th>Invoice Type</th>
                            <th>Invoice Number</th>
                            <th>{{ fieldLabel }}</th>
                            <th>Patient</th>
                            <th>Amount ({{ $root.$data.currency }})</th>
                            <th>Created By</th>
                            <th class="wf-120">Invoice Date</th>
                            <th class="text-center wf-120">Status</th>
                            <th class="wf-150">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="loader == 1">
                        <tr>
                            <td colspan="10">
                                <p class="text-center">
                                    <img :src="loaderurl" alt="Loading..." />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr
                            v-for="(invoice, sn) in invoices.data"
                            :key="invoice.id"
                            :class="[
                                invoice.cancel != '0' ? 'bg-lightpink' : '',
                            ]"
                        >
                            <td>
                                {{
                                    (invoices.current_page - 1) *
                                        invoices.per_page +
                                    sn +
                                    1
                                }}
                            </td>
                            <td>
                                <invoice-label
                                    :type="invoice.invoice_type"
                                ></invoice-label>
                            </td>
                            <td class="font-weight-bold">
                                {{ invoice.invoice_number }}
                            </td>
                            <td>
                                <a
                                    class="
                                        text-theme
                                        blank-btn
                                        text-uppercase
                                        font-weight-bold
                                    "
                                    target="_blank"
                                    :href="
                                        '/appointments/view/' +
                                        invoice.appointment_id
                                    "
                                    v-if="field == 'appointment_id'"
                                >
                                    {{ invoice.appointment_id }}
                                </a>
                                <a
                                    class="
                                        text-theme
                                        blank-btn
                                        text-uppercase
                                        font-weight-bold
                                    "
                                    target="_blank"
                                    :href="
                                        '/appointments/view/' +
                                        invoice.appointment_id
                                    "
                                    v-else
                                >
                                    {{ invoice.course_code }}
                                </a>
                            </td>
                            <td>
                                <a
                                    class="
                                        text-theme
                                        blank-btn
                                        text-uppercase
                                        font-weight-bold
                                    "
                                    target="_blank"
                                    :href="'/customers/view/' + invoice.user_id"
                                >
                                    {{ invoice.first_name | capitalize }}
                                </a>
                            </td>
                            <td>{{ invoice.payable_amount | formatOMR }}</td>
                            <td>{{ invoice.admin | capitalize }}</td>
                            <td>{{ invoice.invoice_date | setdate }}</td>
                            <td align="center" v-if="invoice.cancel != '0'">
                                --
                            </td>
                            <td align="center" v-else>
                                <payment-label
                                    :type="invoice.payment_status"
                                ></payment-label>
                            </td>
                            <td>
                                <a
                                    target="_blank"
                                    class="btn btn-sm btn-green-theme"
                                    :href="
                                        '/invoices/print/' +
                                        invoice.invoice_number
                                    "
                                    ><i class="fas fa-print"></i
                                ></a>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    @click="showReason(invoice)"
                                    v-if="invoice.cancel != '0'"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <span v-else>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm wf-25"
                                        @click="
                                            cancelInvoice(
                                                invoice.invoice_number
                                            )
                                        "
                                        v-show="
                                            activeEdit(
                                                invoice.cancel,
                                                invoice.created_at
                                            )
                                        "
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-purple btn-sm wf-25"
                                        @click="
                                            showPayment(
                                                invoice.invoice_number,
                                                invoice.payable_amount
                                            )
                                        "
                                    >
                                        <i class="fas fa-coins"></i>
                                    </button>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <pagination
                    class="m-0 float-right"
                    :limit="10"
                    :data="invoices"
                    @pagination-change-page="showInvoices"
                    :show-disabled="true"
                >
                </pagination>
            </div>
        </div>
        <div
            class="modal fade"
            id="reasonModal"
            data-backdrop="static"
            data-keyboard="false"
            aria-labelledby="reasonModalTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">
                            Invoice Cancellation Reason
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p
                            v-for="(reasons, rkey) in reason"
                            :key="rkey"
                            class="border-bottom"
                            v-show="rkey != 'admin'"
                        >
                            <b class="text-uppercase">{{ rkey }}</b
                            ><br />
                            <span>{{ reasons }}</span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-wide btn-danger"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <payment-list
            :showpayment="showpayment"
            :active_invoice="active_invoice"
            :payments="payments"
            :current_page="invoices.current_page"
        ></payment-list>
    </div>
</template>
<script>
import InvoiceLabel from "../Tags/InvoiceLabel.vue";
import PaymentLabel from "../Tags/PaymentLabels.vue";
import PaymentList from "./PaymentsList.vue";
import moment from "moment";
export default {
    components: { InvoiceLabel, PaymentLabel, PaymentList },
    props: {
        aYear: [String, Number],
        invoice_types: [Object, Array],
        searchFields: [Object],
        title: [String],
        fieldLabel: [String],
        field: [String],
    },
    data() {
        return {
            profile: this.$store.getters.user,
            editmode: false,
            invoices: {},
            reason: "",
            loader: false,
            loaderurl: "/images/spinner.gif",
            payments: [],
            currency: this.$root.$data.currency,
            active_invoice: {
                invoice_number: "",
                total_amount: "",
                remaing_balance: "",
            },
            activeLabel: "",
            showpayment: false,
            enablepay: false,
            allmodes: this.$root.$data.payment_modes,
        };
    },
    computed: {
        activeYear() {
            return this.aYear;
        },
    },
    watch: {
        searchFields(newValue, oldValue) {
            if (newValue != oldValue) {
                this.showInvoices();
            }
        },
    },
    methods: {
        activeEdit(cval, adate) {
            if (this.profile.admin_type_id == 1) {
                return true;
            } else {
                let today = new Date();
                let ndate = new Date(adate);
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
                var diffDuration = moment.duration(b.diff(a));
                return diffDuration.days() >= 0 && cval == "0" ? true : false;
            }
        },
        showInvoices(page = 1) {
            this.$Progress.start();
            this.loader = 1;
            let qs = Object.keys(this.searchFields)
                .map((key) => `${key}=${this.searchFields[key]}`)
                .join("&");
            let ait = Object.keys(this.invoice_types);
            axios
                .get(
                    "/api/invoices/get-invoices/" +
                        this.activeYear +
                        "?" +
                        qs +
                        "&ait=" +
                        ait +
                        "&page=" +
                        page
                )
                .then(({ data }) => {
                    this.invoices = data;
                    this.loader = false;
                    this.$Progress.finish();
                });
        },
        cancelInvoice(id) {
            swal.fire({
                title: "Are you sure?",
                text: "Please enter the reason before cancelling the invoice.",
                footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                type: "question",
                input: "text",
                inputAttributes: { autocapitalize: "off" },
                showCancelButton: true,
                confirmButtonColor: "#C70039",
                cancelButtonColor: "#0DC957",
                confirmButtonText: "Yes, Cancel it!",
                cancelButtonText: "Not Now",
                preConfirm: (reason) => {
                    return axios
                        .post("/api/cancel-invoice", {
                            invoice_number: id,
                            reason: reason,
                        })
                        .then((response) => {
                            Fire.$emit("changeyear");
                            swal.fire(
                                "Cancelled!",
                                "Invoice has been cancelled successfully.",
                                "success"
                            );
                        })
                        .catch((error) => {
                            swal.showValidationMessage(`${error}`);
                        });
                },
            });
        },
        showReason(inv) {
            this.reason = JSON.parse(inv.cancel);
            $("#reasonModal").modal("show");
        },
        closePayment() {
            this.showpayment = false;
        },
        showPayment(inv, inva) {
            this.active_invoice.invoice_number = inv;
            this.active_invoice.total_amount = inva;
            this.payments = [];
            this.loader = 3;
            axios
                .post("/api/invoice-payments", { invoice_number: inv })
                .then((response) => {
                    this.payments = response.data.payments;
                    this.active_invoice.remaing_balance =
                        response.data.remaining;
                    this.loader = false;
                })
                .catch((err) => {
                    console.log(err);
                });

            this.showpayment = true;
        },
    },
    created() {
        this.showInvoices();
        Fire.$on("changeyear", () => {
            this.showInvoices();
        });
    },
    mounted() {
        Fire.$on("close-payment", () => {
            this.closePayment();
        });
    },
};
</script>