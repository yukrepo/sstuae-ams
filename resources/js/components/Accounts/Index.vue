<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Logins
                                <div class="card-tools">
                                    <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addAdmin"><i class="fas fa-plus fa-fw"></i></button>
                                </div>
                            </h3>
                        </div>
                        <div class="card-body p-0 content-box-180">
                            <table id="stock-alert" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Contact No</th>
                                        <th>Email</th>
                                        <th style="width: 100px;">Added On</th>
                                        <th style="width: 50px;">Status</th>
                                        <th style="width: 125px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(admin, counter) in admins.data" :key="counter">
                                        <td>{{ (admins.current_page - 1)*(admins.per_page) + counter + 1 }}</td>
                                        <td>{{ admin.location | capitalize }}</td>
                                        <td>{{ admin.admin_type | capitalize }}</td>
                                        <td>{{ admin.name | capitalize }}</td>
                                        <td>{{ admin.username }}</td>
                                        <td>{{ (admin.contact_no)?admin.contact_no:'--' }}</td>
                                        <td>{{ (admin.email)?admin.email:'--' }}</td>
                                        <td>{{ admin.created_at | setdate }}</td>
                                        <td align="center" v-if="admin.publish == 1">
                                            <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                        </td>
                                        <td align="center" v-else>
                                            <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                        </td>
                                        <td>
                                            <span v-if="admin.id != 1">
                                                <button class="btn btn-purple btn-sm" @click="setReportdmin(admin)"><i class="fas fa-file-excel"></i></button>
                                                <button class="btn btn-warning btn-sm" @click="editAdmin(admin)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteAdmin(admin.id)"><i class="fas fa-trash"></i></button>
                                            </span>
                                            <span v-else>
                                                <label class="status-label-full bg-pink">Not updatable</label>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer" v-if="admins.total > 1">
                            <pagination class="m-0 float-right" :data="admins" @pagination-change-page="getResults" :show-disabled="true">
                                <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addadminModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addadminModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateAdmin() : createAdmin()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addadminModalTitle">Add New Admin</h5>
                            <h5 class="modal-title" v-show="editmode" id="addadminModalTitle">Update Admin</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="location_id" class="control-label">Location</label>
                                    <select v-model="form.location_id" name="location_id" class="form-control" :class="{ 'is-invalid': form.errors.has('location_id') }">
                                        <option disabled value="">Select Location</option>
                                        <option v-for="location in locations" :key="location.id" v-bind:value="location.id">
                                            {{ location.clinic_name }}
                                        </option>
                                    </select>
                                    <has-error :form="form" field="location_id"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="admin_type_id" class="control-label">Admin Category</label>
                                    <select v-model="form.admin_type_id" name="admin_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('admin_type_id') }" @change="addName()">
                                        <option disabled value="">Select Admin Category</option>
                                        <option v-for="admin_type in admin_types" :key="admin_type.id" v-bind:value="admin_type.id">
                                            {{ admin_type.type | capitalize }}
                                        </option>
                                    </select>
                                    <has-error :form="form" field="admin_type_id"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <span v-show="form.admin_type_id == 2">
                                        <label for="relative_id" class="control-label">Doctors/Therapists</label>
                                        <select v-model="form.relative_id" name="relative_id" class="form-control" :class="{ 'is-invalid': form.errors.has('relative_id') }" @change="addName()">
                                            <option disabled value="">Select Doctor/Therapist</option>
                                            <option v-for="doctor in doctors" :key="doctor.id" v-bind:value="doctor.id">
                                                {{ doctor.name | capitalize }}
                                            </option>
                                        </select>
                                        <has-error :form="form" field="relative_id"></has-error>
                                    </span>
                                </div>
                                <div class="form-group col-4">
                                    <label for="name" class="control-label">Name</label>
                                    <input v-model="form.name" type="text" name="name" id="name" placeholder="enter name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" v-bind:readonly="(this.form.admin_type_id == 2)?true:false">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="email" class="control-label">Email</label>
                                    <input v-model="form.email" type="email" name="email" id="email" placeholder="enter email"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                    <has-error :form="form" field="email"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="contact_no" class="control-label">Contact Number</label>
                                    <input v-model="form.contact_no" type="text" name="contact_no" id="contact_no" placeholder="enter contact no"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('contact_no') }">
                                    <has-error :form="form" field="contact_no"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="username" class="control-label">Username</label>
                                    <input v-model="form.username" type="text" name="username" id="username" placeholder="enter username"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                                    <has-error :form="form" field="username"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="password" class="control-label">Password</label>
                                    <input v-model="form.password" autocomplete="current-password" type="password" name="password" id="password" placeholder="enter password iff you want to change" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                    <has-error :form="form" field="password"></has-error>
                                </div>
                                <div class="form-group col-4">
                                    <label for="publish" class="control-label">Status</label>
                                    <select v-model="form.publish" name="publish" id="publish" class="form-control" :class="{ 'is-invalid': form.errors.has('publish') }">
                                        <option value="" disabled>Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Deactive</option>
                                    </select>
                                    <has-error :form="form" field="publish"></has-error>
                                </div>
                            </div>
                            <hr>
                            <p class="text-danger">
                                <b>Notes</b>
                                <ul>
                                    <li>Username must be in small letters and only alphanumeric( No special chars).</li>
                                    <li>Name can not be edited for doctors' login.</li>
                                </ul>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button v-show="!editmode" type="submit" class="btn btn-wide btn-success"> Create </button>
                            <button v-show="editmode" type="submit" class="btn btn-wide btn-success"> Update </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fullbarmodal fade" id="setReportModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setReportModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="setReportModalTitle"> Set Report Permission For {{ rform.name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="wf-75">SNO</th>
                                    <th class="wf-250">Report Type</th>
                                    <th>Report Name</th>
                                    <th class="wf-100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(rep, rno) in reports" :key="'rno'+rno">
                                    <td> {{ rno+1 }} </td>
                                    <td> {{ rep.type }} </td>
                                    <td> {{ rep.name }} </td>
                                    <td>
                                        <button @click="togglePermission(rep.id)"  v-if="rform.reports.indexOf(rep.id) >= 0" type="button" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                        <button @click="togglePermission(rep.id)" v-else type="button" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="updatePermission()" class="btn btn-sm btn-dark wf-200">Update Report Permissions</button>
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
                admins: {},
                admin_types: {},
                locations: this.$store.getters.locations,
                doctors: {},
                reports:{},
                form: new Form({
                    id:'',
                    admin_type_id:'',
                    publish:'',
                    username:'',
                    email:'',
                    contact_no:'',
                    name:'',
                    password:'',
                    doctors:'',
                    location_id:'',
                    relative_id:''
                }),
                rform: new Form({
                    id:'',
                    name:'',
                    reports:[]
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/admins?page=' + page)
                    .then(response => {
                        this.admins = response.data;
                    });
            },
            getAdminTypes() {
                axios.get('/api/getAdminTypesList').then(({ data }) => (this.admin_types = data));
            },
            getLocations() {
                axios.get('/api/getReports').then(({ data }) => (this.reports = data));
            },
            getDoctors() {
                axios.get('/api/getDoctorsList').then(({ data }) => (this.doctors = data));
            },
            showAdmins() {
                this.$Progress.start();
                axios.get('/api/admins').then(({ data }) => {
                    this.admins = data;
                    this.$Progress.finish();
                });
            },
            addName() {
                if((this.form.relative_id != '') && (this.form.admin_type_id == 2)){
                    axios.get('/api/doctors/'+this.form.relative_id).then(({ data }) => (this.form.name = data.name));
                }
                else{
                    this.form.name = '';
                }
            },
            addAdmin() {
                this.editmode = false;
                this.form.reset();
                $('#addadminModal').modal('show');
            },
            createAdmin() {
                this.form.post('/api/admins')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addadminModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Admin created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {
                    swal.fire({
                        type:'error',
                        title:'Not done',
                        text: 'Your request can not be completed. Please try again'
                    });
                });
            },
            updateAdmin() {
                this.form.put('/api/admins/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addadminModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Admin updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updatePermission() {
                this.rform.post('/api/admin-reports')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#setReportModal').modal('hide');
                    this.rform.reset();
                    toast.fire({
                        type:'success',
                        title:'Admin reports updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            editAdmin(admin) {
                this.editmode = true;
                this.form.fill(admin);
                $('#addadminModal').modal('show');
            },
            togglePermission(rid) {
                if(this.rform.reports.indexOf(rid) >= 0) {
                    this.rform.reports.splice(this.rform.reports.indexOf(rid), 1);
                } else {
                    this.rform.reports.push(rid)
                }
            },
            setReportdmin(admin) {
                admin.reports = (admin.reports)?admin.reports.split(','):[];
                admin.reports = admin.reports.map(Number);
                this.rform.fill(admin);
                $('#setReportModal').modal('show');
            },
            deleteAdmin(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('/api/admins/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            }
        },
        created() {
            this.getAdminTypes();
            this.getLocations();
            this.getDoctors();
            this.showAdmins();
            Fire.$on('AfterCreate', () => {
                this.showAdmins();
            });
        }
    }
</script>
