<template>
    <table class="table table-bordered table-striped text-left mt-1 mb-0">
        <thead>
            <tr>
                <th style="text-align:left" class="wf-50">SNo</th>
                <th style="text-align:left">Amount</th>
                <th style="text-align:left">Txn Mode</th>
                <th style="text-align:left">Txn Date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(txn, k) in txns" :key="'txn'+k">
                <td style="text-align:left; border:1px solid #e4e4e4"> {{ k+1 }}  </td>
                <td style="text-align:left; border:1px solid #e4e4e4"> {{ $root.$data.currency }} {{ txn.amount | formatOMR }}  </td>
                <td style="text-align:left; border:1px solid #e4e4e4"> {{ txn.mode | capitalize }} <span v-show="txn.txnid">{{'TXN: '+ txn.txnid }}</span>  </td>
                <td style="text-align:left; border:1px solid #e4e4e4"> {{txn.date | setdate}}  </td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    props: {
        payments:[Array, Object],
    },
    computed: {
        txns() {
            return this.payments.payments
        }
    },
    methods: {
       
    }
}
</script>
