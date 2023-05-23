<template>
    <label class="status-label-full-outline" :class="csslabel">{{ title }}</label>
</template>
<script>
export default {
    props: {
        type:[String, Number]
    },
    computed: {
        csslabel() {
            let arr = this.$root.$data.invoice_type_labels;
            let cl;
            if(this.type <= arr.length) {
                cl = arr[this.type]
            } else {
                cl = 'text-warning';
            }
            return cl;
        },
        title() {
            let arr = this.$root.$data.invoice_types;
            let cl;
            if(this.type <= arr.length) {
                cl = arr[this.type]
            } else {
                cl = 'Unknown';
            }
            return cl;
        }
    },
}
</script>