<template>
    <div class="content">
        <invoice-search :invoice_types="invoice_types" :ryear="'/invoices/courses/'"></invoice-search>
        <div class="container-fluid">
            <invoice-list :invoice_types="invoice_types" :field="'course_code'" :fieldLabel="'Course ID'" :aYear="activeYear" :title="title" :searchFields="searchFields"></invoice-list>
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
            invoice_types:{6:'Prescribed Course', 5:'Course Package', 8:'Direct Course'},
            searchFields:{},
            title:'Course Invoices'
        }
    },
    beforeMount() {
        let activeId = this.$route.path.split("/");
        this.activeYear = activeId[3];
    }
}
</script>