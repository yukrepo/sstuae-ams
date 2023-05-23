<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Doctors</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Doctors/Therapists List
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-primary mr-1"><i class="fas fa-download fa-fw"></i></button>
                                        <button class="btn btn-secondary btn-sm mr-1" type="button" @click="addDoctor"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Location</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Contact No</th>
                                            <th>Email</th>
                                            <th>Qualifications</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="doctor in doctors.data" :key="doctor.id">
                                            <td>{{ doctor.id }}</td>
                                            <td>{{ doctor.location }}</td>
                                            <td>{{ doctor.designation }}</td>
                                            <td>{{ doctor.name }}</td>
                                            <td>{{ doctor.contact_no }}</td>
                                            <td>{{ doctor.email }}</td>
                                            <td>{{ doctor.qualification }}</td>
                                            <td>{{ doctor.created_at | setdate }}</td>
                                            <td align="center" v-if="doctor.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editDoctor(doctor)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteDoctor(doctor.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="doctors.total > 1">
                                <pagination class="m-0 float-right" :data="doctors" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="adddoctorModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddoctorModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateDoctor() : createDoctor()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addDoctorModalTitle">Add Doctor</h5>
                            <h5 class="modal-title" v-show="editmode" id="addDoctorModalTitle">Update Doctor's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Name</label>
                                        <input v-model="form.name" type="text" name="name" id="name" placeholder="enter full name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_no" class="control-label">Contact No</label>
                                        <input v-model="form.contact_no" type="text" name="contact_no" id="contact_no" placeholder="enter mobile number"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('contact_no') }">
                                        <has-error :form="form" field="contact_no"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="control-label">Email</label>
                                        <input v-model="form.email" type="email" name="email" id="email" placeholder="enter email"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="qualification" class="control-label">Qualifications</label>
                                        <textarea v-model="form.qualification" name="qualification" rows="4" id="qualification" placeholder="enter social links seprated by ,"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('qualification') }"></textarea>
                                        <has-error :form="form" field="qualification"></has-error>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="location_id" class="control-label">Office Location</label>
                                        <select v-model="form.location_id" name="location_id" id="location_id" class="form-control" :class="{ 'is-invalid': form.errors.has('location_id') }">
                                            <option disabled value="">Select Location</option>
                                            <option value="1">Muscat Oman</option>
                                        </select>
                                        <has-error :form="form" field="location_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="designation_type_id" class="control-label">Designation</label>
                                        <select v-model="form.designation_type_id" name="designation_type_id" id="designation_type_id" class="form-control" :class="{ 'is-invalid': form.errors.has('designation_type_id') }">
                                            <option disabled value="">Select Designation</option>
                                            <option value="1">Doctor</option>
                                            <option value="2">Therapist</option>
                                        </select>
                                        <has-error :form="form" field="designation_type_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="about" class="control-label">about</label>
                                        <textarea v-model="form.about" name="about" rows="4" id="about" placeholder="enter bio"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('about') }"></textarea>
                                        <has-error :form="form" field="about"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="status_id" class="control-label">Status</label>
                                        <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                            <option disabled value="">Select Status</option>
                                            <option value="2">Active</option>
                                            <option value="3">Deactive</option>
                                        </select>
                                        <has-error :form="form" field="status_id"></has-error>
                                    </div>
                                </div>
                            </div>
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
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,
                doctors: {},
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
            getResults(page = 1) {
                axios.get('/api/doctors?page=' + page)
                    .then(response => {
                        this.doctors = response.data;
                    });
            },
            showDoctors() {
                this.$Progress.start();
                axios.get('/api/doctors').then(({ data }) => {
                    this.doctors = data;
                    this.$Progress.finish();
                });
            },
            addDoctor() {
                this.editmode = false;
                this.form.reset();
                $('#adddoctorModal').modal('show');
            },
            createDoctor() {
                this.form.post('/api/doctors')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddoctorModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Doctor created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateDoctor() {
                this.form.put('/api/doctors/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#adddoctorModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Doctor details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editDoctor(doctor) {
                this.editmode = true;
                this.form.fill(doctor);
                $('#adddoctorModal').modal('show');
            },
            deleteDoctor(id) {
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
                        this.form.delete('/api/doctors/'+id)
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
            this.showDoctors();
            Fire.$on('AfterCreate', () => {
                this.showDoctors();
            });
        }
    }
</script>
