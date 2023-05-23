<template>
    <div class="cal-page">
        <div class="content-header">
            <div class="mb-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Appointments</li>
                </ol>
            </div>
        </div>
        <div class="content custom-cal">
            <dr-appointment-calendar></dr-appointment-calendar>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,

            }
        },
        methods: {

        },
        created() {

        }
    }
</script>
