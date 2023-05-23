<template>
    <div>
        <div class="content view-page">
            <div class="container-fluid p-2">
                <div class="card full-tabber">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-pills" id="pills-tab-desc" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-dstab-1" data-toggle="pill" href="#pills-dstab1" role="tab" aria-controls="pills-dstab1" aria-selected="true">Personal Details</a>
                            </li>
                          <!--   <li class="nav-item">
                                <a class="nav-link" id="pills-dstab-2" data-toggle="pill" href="#pills-dstab2" role="tab" aria-controls="pills-dstab2" aria-selected="false">Appointment Requests </a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link" id="pills-dstab-4" data-toggle="pill" href="#pills-dstab4" role="tab" aria-controls="pills-dstab4" aria-selected="false">Appointment History </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-dstab-3" data-toggle="pill" href="#pills-dstab3" role="tab" aria-controls="pills-dstab3" aria-selected="true">Courses History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-dstab-5" data-toggle="pill" href="#pills-dstab5" role="tab" aria-controls="pills-dstab5" aria-selected="true">Invoice History</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content p-0 card-body" id="pills-tabContent-desc">
                        <div class="tab-pane fade show active" id="pills-dstab1" role="tabpanel" aria-labelledby="pills-dstab1-tab">
                            <div class="row">
                                <div class="col-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Personal Profile</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-hover table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><b>User ID</b></td>
                                                        <td>{{ customer.username }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>First Name</b></td>
                                                        <td>{{ customer.first_name | capitalize }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Last Name</b></td>
                                                        <td>{{ customer.last_name | capitalize }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Mobile</b></td>
                                                        <td>{{ customer.mobile }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Email</b></td>
                                                        <td>{{ customer.email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Gender</b></td>
                                                        <td v-if="customer.gender == 1">
                                                        {{ customer.married | capitalize }} ( Male )
                                                        </td>
                                                        <td v-else-if="customer.gender == 2">
                                                            {{ customer.married | capitalize }} ( Female )
                                                        </td>
                                                        <td v-else>
                                                            {{ customer.married | capitalize }} ( -- )
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Alternate No</b></td>
                                                        <td>{{ customer.contact_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Date Of Birth</b></td>
                                                        <td>{{ customer.date_of_birth | setdate }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Company Name</b></td>
                                                        <td>{{ (customer.company_name)?customer.company_name:'--' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Address</b></td>
                                                        <td>{{ customer.address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>City</b></td>
                                                        <td>{{ customer.city | capitalize }} - {{ customer.zipcode }} ({{ customer.country }})</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">Insurance Details</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-hover table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><b>insurance company</b></td>
                                                        <td>{{ customer.insurancer | capitalize }}</td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td><b>card number</b></td>
                                                        <td>{{ customer.policy_number }}
                                                            <button class="btn inacn-btn btn-success" v-if="customer.insurance_verified == 1">Verified</button>
                                                            <button class="btn inacn-btn btn-danger" v-else>Not Verified</button>
                                                        </td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td><b>Insurance Copy</b></td>
                                                        <td><img class="img-doc"  v-img:docurl+customer.insurance_copy :src="this.docurl+customer.insurance_copy"></td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td><b>Effective Date</b></td>
                                                        <td>{{ customer.effective_date | setdate }}</td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td><b>Expiry Date</b></td>
                                                        <td>{{ customer.expiry_date | setdate }}</td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td><b>Consultation Deductable</b></td>
                                                        <td>
                                                            <span v-if="checkPercent(customer.consult_deductable)">
                                                                {{ customer.consult_deductable }}
                                                            </span>
                                                            <span v-else>
                                                                {{ customer.consult_deductable | formatOMR }}
                                                            </span>
                                                        </td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td><b>Treatment Deductable</b></td>
                                                        <td>
                                                            <span v-if="checkPercent(customer.treatment_deductable)">
                                                                {{ customer.treatment_deductable }}
                                                            </span>
                                                            <span v-else>
                                                                {{ customer.treatment_deductable | formatOMR }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Pharmacy Deductable</b></td>
                                                        <td>
                                                            <span v-if="checkPercent(customer.pharmacy_deductable)">
                                                                {{ customer.pharmacy_deductable }}
                                                            </span>
                                                            <span v-else>
                                                                {{ customer.pharmacy_deductable | formatOMR }}
                                                            </span>
                                                        </td>
                                                    </tr> -->
                                                   <!--  <tr>
                                                        <td><b>Co-Insurance</b></td>
                                                        <td>
                                                            <span v-if="checkPercent(customer.co_insurance)">
                                                                {{ customer.co_insurance }}
                                                            </span>
                                                            <span v-else>
                                                                {{ customer.co_insurance | formatOMR }}
                                                            </span>
                                                        </td>
                                                    </tr> -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="card  m-b-20">
                                        <div class="card-header">
                                            <h3 class="card-title">Identity Details</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-hover table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Nationality</b></td>
                                                        <td>{{ (customer.nationality)?customer.nationality:'--' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Identity Type</b></td>
                                                        <td>{{ customer.id_type | capitalize }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Identity No</b></td>
                                                        <td>{{ customer.verification_number }}
                                                            <button class="btn inacn-btn btn-success" v-if="customer.identity_verified == 1">Verified</button>
                                                            <button class="btn inacn-btn btn-danger" v-else>Not Verified</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>identity copy</b></td>
                                                        <td><img class="img-doc" v-img:docurl+customer.identity_copy :src="docurl+customer.identity_copy"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <!-- <div class="card-header">
                                            <h3 class="card-title">Consent Form</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped" v-if="customer.sign">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2" class="text-center">
                                                            <img :src="'/files/consents/'+customer.sign" alt="" class="img-fluid" style="height:150px">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-center">Form Signed On <b>{{ customer.sign_date | setdate }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="w-50">
                                                            <a :href="'/print/consent-form/'+customer.id+'/ar'" target="_blank" class="btn btn-sm btn-dark btn-block">
                                                                <i class="fa fa-download"></i> Arabic
                                                            </a>
                                                        </th>
                                                        <th class="w-50">
                                                            <a :href="'/print/consent-form/'+customer.id+'/en'" target="_blank" class="btn btn-sm btn-dark btn-block">
                                                                <i class="fa fa-download"></i> English
                                                            </a>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="p-4" v-else>
                                                <a href="https://forms.srisritattva.me" class="btn btn-primary btn-sm" target="_blank">Click here To Sign Consent Form</a>
                                            </div>
                                        </div> -->
                                    </div>

                                </div>
                                <div class="col-3">
                                    <div class="m-b-10">
                                        <button class="btn btn-sm btn-wide btn-secondary btn-block"> Send Reset Password Link</button>
                                        <button class="btn btn-sm btn-wide btn-danger btn-block"> Block This User</button>
                                        <button class="btn btn-sm btn-wide btn-success btn-block"> View Message Conversation</button>
                                        <!-- <tr>
                                            <td>
                                                <h5 class="m-t-5 m-b-20">Send User A private Message</h5>
                                                <form>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="msg_type" placeholder="enter messsage type or subject">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="message" rows="4" id="message" class="form-control" placeholder="enter message"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-block btn-primary btn-sm" name="Send message">
                                                    </div>
                                                </form>
                                            </td>
                                        </tr> -->
                                    </div>
                                    <div class="card m-b-20">
                                        <div class="card-header">
                                            <h3 class="card-title">Account Synopsis</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-hover table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%"><b>Account Status</b></td>
                                                        <td :class="customer.css">
                                                        <span class="text-white"> {{ customer.status  }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Joined On</b></td>
                                                        <td> {{ customer.created_at | setdate }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Last Modification</b></td>
                                                        <td> {{ customer.updated_at | setdate }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Source Of Registration</b></td>
                                                        <td> {{ customer.source }} </td>
                                                    </tr>
                                                    <tr v-if="customer.admin_id >= 1">
                                                        <td><b>Registered By</b></td>
                                                        <td> {{ customer.admin }} </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <!--  <div class="tab-pane fade" id="pills-dstab2" role="tabpanel" aria-labelledby="pills-dstab2-tab">
                        </div> -->
                        <div class="tab-pane fade p-0" id="pills-dstab3" role="tabpanel" aria-labelledby="pills-dstab3-tab">
                            <course-history :user_id="id" :bus="bus"></course-history>
                        </div>
                        <div class="tab-pane fade p-0" id="pills-dstab4" role="tabpanel" aria-labelledby="pills-dstab4-tab">
                            <appointment-history :user_id="id" :bus="bus"></appointment-history>
                        </div>
                        <div class="tab-pane fade p-0" id="pills-dstab5" role="tabpanel" aria-labelledby="pills-dstab5-tab">
                            <invoice-history :user_id="id" :bus="bus"></invoice-history>
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
                bus: new Vue(),
                svgurl: '/svg/',
                docurl: '/files/docs/',
                editmode: false,
                id: '',
                customer:'',
                form: new Form({
                    id:'',
                    name:'',
                    contact_no:'',
                    email: '',
                    designation_type_id:'',
                    location_id:'',
                    qualification:'',
                    about:'',
                    status_id:''
                })
            }
        },
        methods: {
            showCustomer() {
                let activeId = this.$route.path.split("/");
                this.$Progress.start();
                axios.get('/api/customers/'+activeId[3]).then(({ data }) => {
                    this.customer = data[0];
                    this.$Progress.finish();
                });
            },
            checkPercent(val) {
                if((val == '') ||(val == null)){
                    return false;
                } else {
                    return val.includes('%');
                }
            }

        },
        beforeMount() {
            let activeId = this.$route.path.split("/");
            this.id = activeId[3];
        },
        mounted() {
            this.showCustomer();
        }
    }
</script>
