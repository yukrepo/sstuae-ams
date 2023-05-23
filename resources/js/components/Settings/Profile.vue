<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid profile-data">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4 mb-2">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Personal Profile</h3>
                            </div>
                            <div class="card-body view-page p-b-0">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <tr>
                                            <td><b>Location</b></td>
                                            <td>{{ profile.location | capitalize }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>User ID</b></td>
                                            <td>{{ (profile.username)?profile.username:'--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Name</b></td>
                                            <td>{{ profile.name | capitalize }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Email</b></td>
                                            <td>{{ (profile.email)?profile.email:'--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Contact No</b></td>
                                            <td>{{ (profile.contact_no)?profile.contact_no:'--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Role</b></td>
                                            <td>{{ profile.role | capitalize }}</td>
                                        </tr>
                                         <tr>
                                            <td><b>Status</b></td>
                                            <td v-if="profile.publish == 1"><span class="btn btn-sm btn-success">Active</span></td>
                                            <td v-else><span class="btn btn-sm btn-danger">Deactive</span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Is Login</b></td>
                                            <td v-if="profile.login_status == 1"><span class="btn btn-sm btn-success">Yes</span></td>
                                            <td v-else><span class="btn btn-sm btn-danger">No</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 mb-2">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Change Your Password</h3>
                            </div>
                             <div class="card-body settingForm">
                                <form @submit.prevent="updatePassword()">
                                    <div class="form-group col-12">
                                        <label for="old_password" class="control-label">Old Password</label>
                                        <input autocomplete v-model="form.old_password" type="password" name="old_password" placeholder="enter old password"
                                            class="form-control input-sm" :class="{ 'is-invalid': form.errors.has('old_password') }">
                                        <has-error :form="form" field="old_password"></has-error>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="new_password" class="control-label">New Password</label>
                                        <input autocomplete v-model="form.new_password" type="password" name="new_password" placeholder="enter new password"
                                            class="form-control input-sm" :class="{ 'is-invalid': form.errors.has('new_password') }">
                                        <has-error :form="form" field="new_password"></has-error>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="confirm_password" class="control-label">Confirm Password</label>
                                        <input autocomplete v-model="form.confirm_password" type="password" name="confirm_password" placeholder="confirm your password"
                                            class="form-control input-sm" :class="{ 'is-invalid': form.errors.has('confirm_password') }">
                                        <has-error :form="form" field="confirm_password"></has-error>
                                    </div>
                                    <div class="form-group col-12">
                                        <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader">
                                        <button type="submit" class="btn btn-wide btn-success" v-else> Update Password</button>
                                    </div>
                                </form>
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
                loader: false,
                syncer: false,
                loaderurl: '/svg/loop.gif',
                form: new Form({
                    old_password:'',
                    new_password:'',
                    confirm_password:''
                }),
            }
        },
        computed: {
            profile() {
                return this.$store.getters.user
            }
        },
        methods: {
            updatePassword(){
                this.loader = true;
                this.form.post('/api/update-password').then(() => {
                    toast.fire({type:'success',title:'password updated successfully'});
                    this.form.reset();
                    this.loader = false;
                }).catch(() => { this.loader = false; });
            },
        },
        created() {
            this.$store.dispatch('setUser');
        }
    }
</script>
