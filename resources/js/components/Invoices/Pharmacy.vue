<template>
    <div class="content">
        <invoice-search :invoice_types="invoice_types" :ryear="'/invoices/appointments/'"></invoice-search>
        <div class="container-fluid">
            <invoice-list :invoice_types="invoice_types" :field="'appointment_id'" :fieldLabel="'Appointment ID'" :aYear="activeYear" :title="title" :searchFields="searchFields"></invoice-list>
        </div>
    </div>
</template>
<script>
import InvoiceSearch from "./Includes/InvoiceSearch.vue";
import InvoiceList from "./Includes/InvoiceList.vue";

export default {
    components: {InvoiceList, InvoiceSearch },
    data() {
        return {
            activeYear: '',
            invoice_types:{4:'Pharmacy'},
            searchFields:{},
            title:'Pharmacy Invoices'
        }
    },
    beforeMount() {
        let activeId = this.$route.path.split("/");
        this.activeYear = activeId[3];
    }
}
</script>
