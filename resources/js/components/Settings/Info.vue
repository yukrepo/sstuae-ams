<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                    <li class="breadcrumb-item active">Software Details</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid profile-data">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile p-b-0">
                                <div class="text-center">
                                    <span class="company-img-box">
                                        <img class="profile-user-img" :src="profile.company_logo" :alt="profile.company_name">
                                    </span>
                                </div>
                                <h3 class="profile-username text-center">{{ profile.company_name }}</h3>
                                <p class="text-muted text-center p-b-10 p-l-0">Since {{ profile.company_start_year }}</p>
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>REGISTERED NAME</b> <a class="float-right">{{ profile.company_registered_name }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>GSTIN</b> <a class="float-right">{{ profile.gstin }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>TIN</b> <a class="float-right">{{ profile.tin }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Official Details</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="fa fa-map-marker mr-1"></i> Official Address</strong>
                                <p class="text-muted">{{ profile.official_address }},<br>{{ profile.official_city }}, {{ profile.official_state }} - {{ profile.official_pincode }}</p>
                                <hr>
                                <strong><i class="fa fa-envelope mr-1"></i> Contact Email</strong>
                                <p class="text-muted">{{ profile.contact_email }}</p>
                                <hr>
                                <strong><i class="fa fa-envelope mr-1"></i> Contact Numbers</strong>
                                <p class="text-muted">{{ profile.contact_number }}</p>
                                <hr>
                                <strong><i class="fa fa-user mr-1"></i> Contact Person</strong>
                                <p class="text-muted">{{ profile.contact_person }} ({{ profile.contact_person_designation }})</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Software Details</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="fa fa-code-branch mr-1"></i> Software Version</strong>
                                <p class="text-muted">{{ profile.software_version }}</p>
                                <hr>
                                <strong><i class="fa fa-calendar mr-1"></i> Software Installation Date</strong>
                                <p class="text-muted">01.02.2019</p>
                                <hr>
                                <strong><i class="fa fa-calendar mr-1"></i> Software Expiry Date</strong>
                                <p class="text-muted">31.01.2020</p>
                                <hr>
                                <strong><i class="fa fa-envelope-open-text mr-1"></i>Activation Key</strong>
                                <p class="text-muted">DE45-2W12</p>
                                <hr>
                                <strong><i class="fa fa-user mr-1"></i> Installed By</strong>
                                <p class="text-muted">{{ profile.software_installer }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Support Details</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="fa fa-eye mr-1"></i> Visit Status</strong>
                                <p class="text-muted">12 / 12</p>
                                <hr>
                                <strong><i class="fa fa-comments mr-1"></i> Chat Support</strong>
                                <p class="text-muted"> -- </p>
                                <hr>
                                <strong><i class="fa fa-at mr-1"></i> Email Support</strong>
                                <p class="text-muted"> -- </p>
                                <hr>
                                <strong><i class="fa fa-phone mr-1"></i>Telephonic Support</strong>
                                <p class="text-muted"> -- </p>
                                <hr>
                                <strong><i class="fa fa-desktop mr-1"></i> Remote Support</strong>
                                <p class="text-muted">12 / 12</p>
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
                 imgurl: 'images/',
                profile: {},
            }
        },
        methods: {
            getProfile() {
                axios.get('/api/get-profile')
                    .then(response => {
                        this.profile = response.data;
                    });
            }
        },
        created() {
            this.getProfile();
        }
    }
</script>
