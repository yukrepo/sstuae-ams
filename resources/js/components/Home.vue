<template>
    <div>
        <div class="container-fluid">
            <div class="row synop mb-3">

                <div class="col-md-12 col-sm-12 col-12 m-b-20 text-center">
                    <div class="center">
                        <div class="circle1"></div>
                        <div class="circle2"></div>
                        <div class="circle3"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,
                synops: '',
                products: '',
                imgurl: 'images/',
                appointments:'',
                profile:'',
                user:'',
                clistSlots:[],
                listSlots:[],
            }
        },
        methods: {
            
        },
        mounted() {
            
        }
    }
</script>
