<template>
    <div class="search-box">
        <form @submit.prevent="makeSearch">
            <div class="row">
                <div class="col-md-1">
                    <input type="text" class="form-control" v-model="sform.invoice_number" placeholder="Invoice No">
                </div>
                <div class="col-md-1">
                    <select v-model="sform.invoice_type" class="form-control" @change="makeSearch">
                        <option disabled value="">Select Type</option>
                        <option :value="invoice_type_key" v-for="(invoice_type, invoice_type_key) in invoice_types" :key="'it'+invoice_type_key">
                            {{invoice_type}}
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
                </div>
                <div class="col-md-2">
                    <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="sform.date" :auto-submit="true" placeholder="select date"></vp-date-picker>
                </div>
                <div class="col-md-2">
                    <img class="img-icon" :src="loaderurl" v-if="loader">
                    <span v-else>
                        <input type="submit" class="btn btn-sm btn-primary" value="Search">
                        <button class="btn btn-sm btn-dark" @click="resetFilter" type="button">Reset</button>
                    </span>
                </div>
                <div class="col-md-3 text-right">
                    <div class="btn-group" role="group" aria-label="First group">
                        <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    props: {
        invoice_types:[Object, Array],
        ryear:[String],
    },
    data() {
        return {
            yearList: this.$store.getters.yearList,
            currentYear: new Date().getFullYear(),
            activeYear:'',
            loader:false,
            loaderurl:'/svg/loop.gif',
            customers:[],
            sform: {
                invoice_number:'',
                user_id:'',
                invoice_type:'',
                date:'',
            },
        }
    },
    methods: {
        changeYear(year) {
            if(this.activeYear != year) {
                this.activeYear = year
                this.$parent.$data.activeYear = year
                this.$router.push(this.ryear+year);
                Fire.$emit('changeyear');
            }
        },
        searchAssets() {
            let activeId = this.$route.path.split("/");
            this.activeYear = activeId[3];
            axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
        },
        makeSearch() {
            this.$parent.$data.searchFields = this.sform
            Fire.$emit('changeyear');
        },
        resetFilter() {
            this.sform.invoice_number = ''
            this.sform.user_id=''
            this.sform.invoice_type=''
            this.sform.date=''
            this.$parent.$data.searchFields = this.sform
            Fire.$emit('changeyear');
        }
    },
    created() {
       this.searchAssets();
    }
}
</script>