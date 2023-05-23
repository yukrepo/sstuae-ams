<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Appointment's Report
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-primary mr-1"><i class="fas fa-download fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">

                            </div>

                        </div>
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
            }
        },
        methods: {

        },
        created() {

        }
    }
</script>
