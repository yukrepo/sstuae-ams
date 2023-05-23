<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" v-model="sform.username" placeholder="User ID">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" v-model="sform.mobile" placeholder="Mobile No">
                        </div>
                        <div class="col-md-2">
                            <input type="email" class="form-control" v-model="sform.email" placeholder="Email">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" v-model="sform.first_name" placeholder="First name">
                        </div>
                        <div class="col-md-4">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Customers List </h3>
                            </div>
                            <div class="card-body p-0 table-responsive content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-60">SNo</th>
                                            <th>M/F</th>
                                            <th>Source</th>
                                            <th>Patient ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>D.O.B.</th>
                                            <th class="wf-100">Joined On</th>
                                            <th class="wf-100 text-center">Status</th>
                                            <th class="wf-180">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(customer, sn) in customers" :key="customer.id">
                                            <td>{{ sn + 1 }}</td>
                                            <td v-if="customer.gender == 1">
                                                    <img class="img-icon" :src="svgurl+'boy.svg'" alt="Male">
                                            </td>
                                            <td v-else-if="customer.gender == 2">
                                                    <img class="img-icon" :src="svgurl+'girl.svg'" alt="Female">
                                            </td>
                                            <td v-else>
                                                    --
                                            </td>
                                            <td> <span class="text-uppercase font-weight-bold"> {{ customer.source }} </span></td>
                                            <td>{{ customer.username }}</td>
                                            <td>{{ customer.first_name | capitalize }}</td>
                                            <td>{{ customer.last_name | capitalize }}</td>
                                            <td>{{ customer.mobile }}</td>
                                            <td>{{ customer.email }}</td>
                                            <td>{{ customer.dob | setdate }}</td>
                                            <td>{{ customer.created_at | setdate }}</td>
                                            <td align="center">
                                                <label class="status-label-full" :class="customer.status_css">{{  customer.status }}</label>
                                            </td>
                                            <td>
                                                <span v-if="customers.length < 2">
                                                    <button class="btn btn-sm btn-danger">No Action Required</button>
                                                </span>
                                                <span v-else>
                                                    <button v-if="checkSelect(customer.id)" class="btn btn-secondary btn-sm" @click="deselectCustomer(customer.id)"> Selected</button>
                                                    <button v-else class="btn btn-outline-secondary btn-sm" @click="selectCustomer(customer.id)"> Select</button>
                                                    <button class="btn btn-primary btn-sm" v-if="form.primary_user == customer.id" @click="resetPrimary()"> Primary</button>
                                                    <button class="btn btn-outline-primary btn-sm" v-else @click="setPrimary(customer.id)"> Set Primary</button>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right" v-show="customers.length > 1">
                                <button class="btn btn-green-theme btn-sm" type="button" @click="syncContact">Sync Selected Contacts</button>
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
                svgurl: '/svg/',
                editmode: false,
                customers: {},
                loader:false,
                loaderurl:'/svg/loop.gif',
                sform: new Form({
                    username:'',
                    mobile:'',
                    email: '',
                    first_name:''
                }),
                form: new Form({
                    primary_user:'',
                    all_users:[]
                })
            }
        },
        methods: {
            makeSearch() {
                if((this.sform.username == '') && (this.sform.mobile == '') && (this.sform.email == '') && (this.sform.first_name == '')) {
                    swal.fire('Please', 'Your search form is empty.', 'error');
                    return false;
                }
                this.loader = true;
                this.customers = {};
                this.form.reset();
                this.sform.post('/api/find-merger-customers')
                    .then(({data}) => {
                        this.customers = data;
                        this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                    });
            },
            checkSelect (val) {
                return this.form.all_users.includes(val);
            },
            selectCustomer(id) {
                this.form.all_users.push(id);
            },
            setPrimary(id) {
                this.form.primary_user = id;
            },
            resetPrimary() {
                this.form.primary_user = '';
            },
            deselectCustomer(id) {
                let array = this.form.all_users;
                let index = array.indexOf(id);
                if (index > -1) {
                    this.form.all_users.splice(index, 1);
                }
            },
            syncContact() {
                if(this.form.primary_user == ''){
                    swal.fire('Please', 'You have to select atleast One primary profile.', 'error');
                    return false;
                }
                if(this.form.all_users.length < 2){
                    swal.fire('Please', 'You have to select atleast 2 records for merging.', 'error');
                    return false;
                }
                swal.fire({
                    title: 'Are you sure?',
                    text: "You want to merge these user accounts in the selected primary account.",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Do it!',
                    cancelButtonText: 'Not Now',
                     showLoaderOnConfirm: true,
                    preConfirm: (course_id) => {
                       return axios.post('/api/users/merge-records', {
                            primary_user:this.form.primary_user,
                            all_users:this.form.all_users,
                        })
                        .then(response => {
                            if(response.data.status == 'error') {
                                throw new Error(response.data.message);
                            } else {
                                swal.fire('Done!', 'User accounts has been merged successfully. Please check there.', 'success');
                                this.customers = {};
                                this.form.reset();
                                this.sform.reset();
                            }
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                });
            }
        },
        mounted() {

        }
    }
</script>
