<template>
    <div
        class="sidebar-modal"
        id="paymentModal"
        :class="[show_payment ? 'active' : '']"
    >
        <div class="sidebar-modal-header">
            <h5 class="sidebar-modal-title text-uppercase">
                Invoice Payment Txns
                <button
                    type="button"
                    class="sidebar-modal-close float-right"
                    @click="closePayment()"
                >
                    <i class="fa fa-times"></i>
                </button>
            </h5>
        </div>
        <div class="sidebar-modal-body" v-if="loader == 2">
            <p class="text-center p-3">
                <img :src="loaderurl" alt="Loading..." />
            </p>
        </div>
        <div class="sidebar-modal-body" v-else>
            <div class="row m-0 border-bottom">
                <div class="col-6 p-2 bg-white border-right border-bottom">
                    Invoice Number :-
                    <b>#{{ active_invoice.invoice_number }}</b>
                </div>
                <div class="col-6 p-2 bg-white text-right border-bottom">
                    Invoiced Total :-
                    <b
                        >{{ currency }}
                        {{ active_invoice.total_amount | formatOMR }}</b
                    >
                </div>
                <div class="col-12 p-2">
                    <h5 class="fw-600 m-0">Payment Txns</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table
                    class="
                        table table-striped table-condensed table-bordered
                        m-0
                    "
                >
                    <thead class="bg-white">
                        <tr>
                            <th class="wf-50">SNo</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Date</th>
                            <th class="wf-100">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="loader == 3">
                        <tr>
                            <td colspan="4">
                                <p class="m-0 text-danger">
                                    No payment received yet
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="payments.length == 0">
                        <tr>
                            <td colspan="4">
                                <p class="m-0 text-danger">
                                    No payment received yet
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr
                            v-for="(payment, pkey) in payments"
                            :key="'pay' + pkey"
                            :class="
                                activeLabel == payment.id
                                    ? 'second-label-active'
                                    : ''
                            "
                        >
                            <td>{{ pkey + 1 }}</td>
                            <td>
                                {{ currency }}{{ payment.amount | formatOMR }}
                            </td>
                            <td>
                                {{
                                    payment.mode.replace("+", " + ")
                                        | capitalize
                                }}
                            </td>
                            <td>{{ payment.date }}</td>
                            <td>
                                <button
                                    class="btn btn-secondary btn-sm"
                                    v-if="activeLabel == payment.id"
                                    type="button"
                                    @click="activeLabel = ''"
                                >
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                                <button
                                    class="btn btn-primary btn-sm"
                                    v-else
                                    type="button"
                                    @click="activeLabel = payment.id"
                                >
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button
                                    class="btn btn-danger btn-sm"
                                    type="button"
                                    @click="
                                        CancelPayment(
                                            payment.id,
                                            payment.created_at
                                        )
                                    "
                                >
                                    <i class="fa fa-times"></i>
                                </button>
                                <div class="second-label-div">
                                    <p>
                                        <label class="second-label"
                                            >Txn Date</label
                                        >
                                        <span>
                                            {{ payment.date | setdate }}</span
                                        >
                                    </p>
                                    <p>
                                        <label class="second-label"
                                            >Paid Amount</label
                                        >
                                        <span>
                                            {{ currency }}
                                            {{
                                                payment.amount | formatOMR
                                            }}</span
                                        >
                                    </p>
                                    <p>
                                        <label class="second-label"
                                            >Received By</label
                                        >
                                        <span> {{ payment.admin }}</span>
                                    </p>
                                    <p>
                                        <label class="second-label"
                                            >Payment Mode</label
                                        >
                                        <span>
                                            {{
                                                payment.mode.replace("+", " + ")
                                                    | capitalize
                                            }}</span
                                        >
                                    </p>
                                    <p>
                                        <label class="second-label"
                                            >Txn No</label
                                        >
                                        <span>
                                            {{
                                                payment.txnid
                                                    ? payment.txnid
                                                    : "-"
                                            }}</span
                                        >
                                    </p>
                                    <p>
                                        <label class="second-label"
                                            >Remark</label
                                        >
                                        <span>
                                            {{
                                                payment.remark
                                                    ? payment.remark
                                                    : "-"
                                            }}</span
                                        >
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-2" v-if="active_invoice.remaing_balance > 0">
                <fieldset v-if="enablepay">
                    <div class="row m-0">
                        <div class="col-6">
                            <div class="form-group">
                                <label
                                    for="remaing_balance"
                                    class="control-label"
                                    >Remaining Balance ({{ currency }})</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="active_invoice.remaing_balance"
                                    readonly
                                />
                            </div>
                            <div class="form-group">
                                <label for="amount" class="control-label"
                                    >Paid Amount ({{ currency }})</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="pform.amount"
                                    :class="{
                                        'is-invalid':
                                            pform.errors.has('amount'),
                                    }"
                                />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="payment_mode" class="control-label"
                                    >Payment Mode</label
                                >
                                <select
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            pform.errors.has('payment_mode'),
                                    }"
                                    v-model="pform.payment_mode"
                                    name="payment_mode"
                                >
                                    <option
                                        :value="pk"
                                        v-for="(pmode, pk) in allmodes"
                                        :key="'pmode' + pk"
                                    >
                                        {{ pmode }}
                                    </option>
                                </select>
                            </div>
                            <div
                                class="form-group"
                                v-show="
                                    pform.payment_mode == 'epay' ||
                                    pform.payment_mode == 'visa' ||
                                    pform.payment_mode == 'cash+visa'
                                "
                            >
                                <label for="txn_id" class="control-label"
                                    >Transaction Number</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            pform.errors.has('txn_id'),
                                    }"
                                    v-model="pform.txn_id"
                                    name="txn_id"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="remark" class="control-label"
                                    >Remarks</label
                                >
                                <textarea
                                    name="remark"
                                    id="remark"
                                    v-model="pform.remark"
                                    class="form-control"
                                ></textarea>
                            </div>
                            <div class="form-group">
                                <button
                                    class="btn btn-sm btn-primary"
                                    type="button"
                                    @click="checkSubmit()"
                                >
                                    <i class="fa fa-save"></i> Submit Payment
                                </button>
                                <button
                                    class="btn btn-sm btn-warning"
                                    type="button"
                                    @click="enablepay = false"
                                >
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <button
                    class="btn btn-sm btn-primary"
                    type="button"
                    v-else
                    @click="enablePayButton"
                >
                    <i class="fa fa-plus"></i> Add New Payment
                </button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        payments: [Object, Array],
        showpayment: [Boolean],
        active_invoice: [Object, Array],
        current_page: [String, Number],
    },
    data() {
        return {
            loader: false,
            loaderurl: "/images/spinner.gif",
            currency: this.$root.$data.currency,
            activeLabel: "",
            enablepay: false,
            allmodes: this.$root.$data.payment_modes,
            pform: new Form({
                invoice_number: "",
                amount: "",
                remaing_balance: "",
                payment_mode: "",
                txn_id: "",
                remark: "",
            }),
        };
    },
    computed: {
        show_payment: {
            get: function () {
                return this.showpayment;
            },
        },
    },
    methods: {
        closePayment() {
            this.$parent.closePayment();
        },
        CancelPayment(pid, pdate) {
            swal.fire({
                title: "Are you sure?",
                text: "Please enter the reason before cancelling the payment transaction.",
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
                        .post("/api/cancel-invoice-payment", {
                            payment_id: pid,
                            date: pdate,
                            reason: reason,
                        })
                        .then((response) => {
                            this.$parent.showInvoices(this.current_page);
                            this.$parent.showPayment(
                                this.active_invoice.invoice_number,
                                this.active_invoice.total_amount
                            );
                        })
                        .catch((err) => {
                            console.log(err);
                            swal.showValidationMessage(`${err}`);
                        });
                },
            });
        },
        checkSubmit() {
            let pmodes = ["visa", "epay", "cash+visa"];
            if (this.pform.amount == "" || this.pform.payment_mode == "") {
                this.$toaster.error(
                    "Paying amount and payment mode is required."
                );
                return false;
            }
            if (this.pform.amount > this.pform.remaing_balance) {
                this.$toaster.error(
                    "Paying amount must not be more than remaining balance."
                );
                return false;
            }
            if (
                pmodes.includes(this.pform.payment_mode) &&
                (this.pform.txn_id === null ||
                    this.pform.txn_id === "" ||
                    this.pform.txn_id === 0)
            ) {
                this.$toaster.error(
                    "Transaction number is required for this payment mode."
                );
                return false;
            }
            if (
                this.pform.payment_mode == "credit" &&
                (this.pform.remark === null || this.pform.remark === "")
            ) {
                this.$toaster.error(
                    "Remark is required for this payment mode."
                );
                return false;
            }
            this.addPayment();
        },
        addPayment() {
            this.pform
                .post("/api/add-invoice-payment")
                .then((response) => {
                    this.pform.reset();
                    this.enablepay = false;
                    if (response.data.type == 1) {
                        this.$parent.showInvoices(this.current_page);
                    }
                    this.$parent.showPayment(
                        this.active_invoice.invoice_number,
                        this.active_invoice.total_amount
                    );
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        enablePayButton() {
            this.enablepay = true;
            this.pform.remaing_balance = this.active_invoice.remaing_balance;
            this.pform.invoice_number = this.active_invoice.invoice_number;
        },
    },
    mounted() {},
};
</script>
