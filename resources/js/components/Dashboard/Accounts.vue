<template>
    <div>
        <div class="row synop mb-3">
            <div class="col-md-12 col-sm-12 col-12">
               <h4>Welcome {{ user.name | capitalize }},</h4>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                imgurl: 'images/',
            }
        },
        computed: {
            user() {
                return this.$store.getters.user
            }
        },
       
        mounted() {
            this.$store.commit('setUser');
        }
    }
</script>