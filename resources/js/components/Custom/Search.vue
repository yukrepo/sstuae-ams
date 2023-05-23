<template>
    <div>
        <form action="">
            <label for="search" class="search-label"> Filter Records</label>
            <input class="search-control" type="text" id="searchbox" :placeholder="placer" v-model="search" @keyup="searchIt">
        </form>
    </div>
</template>
<script>
    export default {
        props: ['placer'],
        data() {
            return {
                search: '',
            }
        },
        methods: {
            searchIt() {
                this.$parent.asearch = this.search;
                Fire.$emit('searching');
            },
        },
        created() {

        }
    }
</script>
