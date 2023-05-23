<template>
    <div>
        <div class="content-header">
            <ol class="breadcrumb m-b-0">
                <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                <li class="breadcrumb-item active">Appointments</li>
            </ol>
        </div>
        <div class="content custom-cal">
            <appointment-calendar></appointment-calendar>
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
