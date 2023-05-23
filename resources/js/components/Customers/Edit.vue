<template>
    <div>
        <div class="content bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form @submit.prevent="editCustomer()">
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Edit Customer</h3>
                                </div>
                                <div class="full-form-body">
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="first_name" class="control-label">First Name</label>
                                                <input type="text" v-model="form.first_name" name="first_name" id="first_name" class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }">
                                                <has-error :form="form" field="first_name"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="last_name" class="control-label">Last Name</label>
                                                <input type="text" v-model="form.last_name" name="last_name" id="last_name" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }">
                                                <has-error :form="form" field="last_name"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="mobile" class="control-label">Mobile Number</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">971</span>
                                                    </div>
                                                    <input type="text" v-model="form.mobile" name="mobile" id="mobile" class="form-control" :class="{ 'is-invalid': form.errors.has('mobile') }">
                                                    <has-error :form="form"  maxlength="9" field="mobile"></has-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="email" class="control-label">Email</label>
                                                <input type="email" v-model="form.email" name="email" id="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="date_of_birth" class="control-label">Date Of Birth</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.date_of_birth" :auto-submit="true"></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="gender" class="control-label">Gender</label>
                                                <select v-model="form.gender" name="gender" id="gender" class="form-control" :class="{ 'is-invalid': form.errors.has('gender') }">
                                                    <option disabled value="">Select Gender</option>
                                                    <option value="0">Unknown</option>
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                </select>
                                                <has-error :form="form" field="gender"></has-error>
                                            </div>
                                        </div>
                                        <!-- <div class="col-2">
                                            <div class="form-group">
                                                <label for="married" class="control-label">Marrital Status</label>
                                                <select v-model="form.married" name="married" id="married" class="form-control" :class="{ 'is-invalid': form.errors.has('married') }">
                                                    <option disabled value="">Select Status</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="divorced">Divorced</option>
                                                </select>
                                                <has-error :form="form" field="married"></has-error>
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="contact_no" class="control-label">Whatsapp Number</label>
                                                <input type="text" v-model="form.contact_no" name="contact_no" id="contact_no" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address" class="control-label">Address</label>
                                                <input type="text" v-model="form.address" name="address" id="address" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="city" class="control-label">City</label>
                                                <input type="text" v-model="form.city" name="city" id="city" class="form-control">
                                            </div>
                                        </div>
                                       <!--  <div class="col-2">
                                            <div class="form-group">
                                                <label for="zipcode" class="control-label">Zipcode</label>
                                                <input type="text" v-model="form.zipcode" name="zipcode" id="zipcode" class="form-control">
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="country" class="control-label">Country</label>
                                                <input type="text" readonly value="UAE" class="form-control">
                                            </div>
                                        </div>
                                         <div class="col-2">
                                            <div class="form-group">
                                                <label for="nationality_id" class="control-label">nationality</label>
                                                <select v-model="form.nationality_id" name="nationality_id" class="form-control" :class="{ 'is-invalid': form.errors.has('nationality_id') }">
                                                    <option disabled value="">Select Nationality</option>
                                                    <option v-for="nation in nationalities" :key="nation.id" v-bind:value="nation.id">
                                                        {{ nation.nationality }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="nationality_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="company_name" class="control-label">Company name</label>
                                                <input type="text" v-model="form.company_name" name="company_name" id="company_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="identity_type_id" class="control-label">Identity Type</label>
                                                <select v-model="form.identity_type_id" name="identity_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('identity_type_id') }">
                                                    <option disabled value="">Select Identity Type</option>
                                                    <option v-for="indentity in indentities" :key="indentity.id" v-bind:value="indentity.id">
                                                        {{ indentity.value }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="identity_type_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="verification_number" class="control-label">ID Number</label>
                                                <input type="text" v-model="form.verification_number" name="verification_number" id="verification_number" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="identity_copy" class="control-label">Identity Copy</label>
                                                <input type="file" @change="uploadIdCopy" placeholder="upload identity copy" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2" v-show="form.oidentity_copy">
                                            <label for="insurance_copy" class="control-label">Uploaded Identity Copy</label>
                                            <img class="img-doc"  v-img:docurl+form.oidentity_copy :src="this.docurl+form.oidentity_copy" >
                                        </div>
                                        <div class="col-2">
                                            <!-- <div class="form-group">
                                                <label for="concern_form" class="control-label">concern form</label>
                                                <input type="file" @change="uploadCFCopy" placeholder="upload concern form" class="form-control">
                                            </div> -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="insurance_id" class="control-label">Payment Mode</label>
                                                <select v-model="form.insurance_id" name="insurance_id" class="form-control" :class="{ 'is-invalid': form.errors.has('insurance_id') }">
                                                    <option disabled value="">Select Insurance Company</option>
                                                    <option v-for="insurance in insurancers" :key="insurance.id" v-bind:value="insurance.id">
                                                        {{ insurance.name }}
                                                    </option>
                                                </select>
                                                <has-error :form="form" field="insurance_id"></has-error>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="policy_number" class="control-label">Card Number</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false" v-model="form.policy_number" name="policy_number" id="policy_number" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="effective_date" class="control-label">Effective Date</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.effective_date" :auto-submit="true" :disabled="(this.form.insurance_id == 1)?true : false" ></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="expiry_date" class="control-label">Expiry</label>
                                                <vp-date-picker locale="en" format="YYYY-MM-DD" v-model="form.expiry_date" :auto-submit="true" :disabled="(this.form.insurance_id == 1)?true : false"></vp-date-picker>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group" v-show="(this.form.insurance_id == 1)?false : true">
                                                <label for="insurance_copy" class="control-label">Insurance Copy</label>
                                                <input type="file"  @change="uploadICopy" placeholder="upload insurance copy" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2" v-show="form.oinsurance_copy">
                                            <label for="insurance_copy" class="control-label">Uploaded Insurance Copy</label>
                                            <img class="img-doc"  v-img:docurl+form.oinsurance_copy :src="this.docurl+form.oinsurance_copy" >
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row customer-div">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="consult_deductable" class="control-label">Consultation Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.consult_deductable" name="consult_deductable" id="consult_deductable" class="form-control">
                                            </div>
                                        </div>
                                       <!--  <div class="col-2">
                                            <div class="form-group">
                                                <label for="treatment_deductable" class="control-label">treatment Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.treatment_deductable" name="treatment_deductable" id="treatment_deductable" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="pharmacy_deductable" class="control-label">pharmacy Deductable</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.pharmacy_deductable" name="pharmacy_deductable" id="pharmacy_deductable" class="form-control">
                                            </div>
                                        </div> -->
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="co-insurance" class="control-label">Co insurance</label>
                                                <input type="text" :readonly="(this.form.insurance_id == 1)?true : false"  v-model="form.co_insurance" name="co-insurance" id="co-insurance" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-10">

                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-block btn-sm btn-dark">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                svgurl: '/svg/',
                docurl: '/files/docs/',
                id: '',
                editmode: false,
                customers: {},
                indentities: {},
                insurancers: {},
                nationalities: {},
                genders: ['Unknown', 'Male', 'Female'],
                form: new Form({
                    id:'',
                    user_id:'',
                    first_name:'',
                    last_name:'',
                    mobile:'',
                    email: '',
                    gender:'',
                    date_of_birth:'',
                    contact_no:'',
                    married:'',
                    company_name:'',
                    city:'',
                    zipcode:'',
                    address:'',
                    nationality:'',
                    policy_number:'',
                    verification_number:'',
                    insurance_id:'',
                    identity_type_id:'',
                    insurance_copy:'',
                    identity_copy:'',
                    pharmacy_deductable:'',
                    consult_deductable:'',
                    treatment_deductable:'',
                    co_insurance:'',
                    effective_date:'',
                    expiry_date:'',
                    nationality_id:'',
                    concern_form:''
                })
            }
        },
        methods: {
            uploadICopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.insurance_copy = reader.result;
                }
                reader.readAsDataURL(file);
            },
            uploadIdCopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.identity_copy = reader.result;
                }
                reader.readAsDataURL(file);
            },
            uploadCFCopy(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.concern_form = reader.result;
                }
                reader.readAsDataURL(file);
            },
            getCustomer() {
                this.$Progress.start();
                let activeId = this.$route.path.split("/");
                this.id = activeId[3];
                axios.get('/api/customers/'+activeId[3]).then(({ data }) => {
                    this.form.fill(data[0]);
                    this.form.oidentity_copy = this.form.identity_copy;
                    this.form.oinsurance_copy = this.form.insurance_copy;
                    this.form.identity_copy = '';
                    this.form.insurance_copy = '';
                    this.form.mobile = data[0].mobile.substr(3);
                });
                this.$Progress.finish();
            },
            getAssets() {
                axios.get('/api/getInsurancersList').then(({ data }) => (this.insurancers = data));
                axios.get('/api/getIdentitiesList').then(({ data }) => (this.indentities = data));
                axios.get('/api/getNationalityList').then(({ data }) => (this.nationalities = data));
            },
            editCustomer() {
                this.form.put('/api/customers/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    swal.fire({
                        type: 'success',
                        title: 'Success',
                        text: 'Details has been updated successfully.',
                    }).then((result) => {
                         this.$router.push('/customers/all');
                    });
                    this.$Progress.finish();
                }).catch(() => {
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again.'
                    });
                });
            },
        },
        created() {
            this.getAssets();
            this.getCustomer();
        }
    }
</script>
