<template>
    <div>
        <div class="content courseDiv">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-1">
                            <input type="text" class="form-control" v-model="sform.course_code" placeholder="Course ID">
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.doctor_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Doctor</option>
                                <option v-for="doctor in sdoctors" :key="doctor.id" v-bind:value="doctor.id">
                                    {{ doctor.name | capitalize }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="sform.status_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Status</option>
                                <option value="2">Active</option>
                                <option value="4">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <img class="img-icon" :src="loaderurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Search" v-else>
                            <button class="btn btn-sm btn-dark" @click="sform.reset()" type="button">Reset</button>
                            <button class="btn btn-sm btn-danger float-right" @click="clearFilter" type="button">Clear</button>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm wf-50" v-for="actyear in yearList"  v-on:click="changeYear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Course's List
                                    <div class="card-tools">

                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                 <table id="coursetable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>Location</th>
                                            <th>Course ID</th>
                                            <th>Doctor</th>
                                            <th>User</th>
                                            <th>Insurance</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Started On</th>
                                            <th class="wf-85">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(course, sn) in courses.data" :key="course.id">
                                           <td>{{ (courses.current_page - 1)*(courses.per_page) + sn + 1 }}</td>
                                           <td> {{ course.clinic_name }} </td>
                                            <td>{{ course.course_code }}</td>
                                            <td>{{ course.doctor }}</td>
                                            <td><button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewCustomer(course.user_id)">{{ course.first_name }} {{ (course.last_name)?course.last_name:'' }}</button></td>
                                            <td>
                                                <span v-if="course.insurance_id == 1">
                                                    <label class="status-label-full bg-purple">Cash</label>
                                                </span>
                                                <span v-else-if="course.ins_approval == 1">
                                                    <label class="status-label-full bg-danger">Approval Pending</label>
                                                </span>
                                                <span v-else-if="course.ins_approval == 2">
                                                    <label class="status-label-full bg-success">Invoice Approved</label>
                                                </span>
                                                <span v-else> ---- </span>
                                            </td>
                                            <td>{{ (course.remark)?course.remark.substr(0,10)+'...':'--' }}</td>
                                            <td>{{ course.created_at | setdate }}</td>
                                            <td>
                                                <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" :href="'/appointments/courses/view/'+course.course_code"><i class="fas fa-desktop"></i></a>
                                                <span class="btn-group editbox" role="group" v-show="((course.insurance_id > 1) && (course.ins_approval == 1))">
                                                    <button type="button" class="btn btn-purple btn-sm dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fas fa-thumbs-up"></i>
                                                    </button>
                                                    <span class="dropdown-menu dropdown-menu-right m-0 p-0" aria-labelledby="btnGroupDrop1">
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="approvalInvoiceCourse(course.consult_code, course.course_code, course.insurance_id)">Approve Invoice</button>
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="makeCashCourse(course.course_code)"> make It Cash </button>
                                                        <button class="dropdown-item text-left bb-1 p-2" @click="deleteCourse(course.course_code)"> Cancel Course </button>
                                                    </span>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :limit="12" :data="courses" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="setCourseModel"  data-backdrop="static" data-keyboard="false" aria-labelledby="setCourseModelTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <div class="modal-header">
                            <h5 class="modal-title" id="setCourseModelTitle">Course Approval</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body" v-if="loading">
                            <p class="text-center p-4">
                                <img :src="loadingurl" alt="loading...">
                            </p>
                        </div>
                        <div class="modal-body" v-else>
                            <form @submit.prevent="approveCourse">
                                <div class="form-group">
                                    <label for="approval_code" class="control-label">Approval Code</label>
                                    <input type="text" name="approval_code" id="approval_code" class="form-control" v-model="aiForm.approval_code" :class="{ 'is-invalid': aiForm.errors.has('approval_code') }">
                                    <has-error :form="aiForm" field="approval_code"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="treatment" class="control-label">Treatments</label>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead class="text-uppercase">
                                            <tr>
                                                <th>SNo</th>
                                                <th>Treatment</th>
                                                <th>Count</th>
                                                <th>Approved</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(treatment, tid) in aiForm.treatments" :key="'ai'+tid">
                                                <th>{{ tid+1 }}</th>
                                                <th>{{ treatment.name }}</th>
                                                <th>{{ treatment.days }}</th>
                                                <th><input type="number" name="approved" id="approved" :max="treatment.days" v-model="aiForm.treatments[tid]['approved']" class="form-control"></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group text-right border-top pt-2">
                                    <input type="submit" value="Approve Course" class="btn btn-md btn-primary wf-200">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="userModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Customer Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>User ID</b><br>{{ customer.username }}</p></li>
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name+' '+customer.last_name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ customer.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.mobile }}</p></li>
                            <li><p class="alert m-0"><b>Nationality</b><br>{{ customer.nationality }}</p></li>
                            <li><p class="alert m-0"><b>City</b><br>{{ customer.city }}</p></li>
                            <li><p class="alert m-0"><b>Address</b><br>{{ customer.address }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ customer.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Identity Card</b><br>{{ customer.verification_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.identity_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Insurance</b><br>
                                <span v-if="customer.insurance_id == 1">
                                    {{ customer.insurancer }}
                                </span>
                                <span v-else>
                                    {{ customer.insurancer }}<br>
                                    {{ customer.policy_number }}
                                    <button class="btn inacn-btn btn-success" v-if="customer.insurance_verified == 1">Verified</button>
                                    <button class="btn inacn-btn btn-danger" v-else>Not Verified</button>
                                </span>
                                </p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ customer.status }}</p></li>
                        </ul>
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
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                courses:{},
                loader:false,
                loading:false,
                loaderurl:'/svg/loop.gif',
                loadingurl:'/images/spinner.gif',
                sform: new Form({
                        doctor_id:'',
                        user_id:'',
                        course_code:'',
                        search:'',
                        year:'',
                        ins_type:'',
                        status_id:''
                }),
                aiForm: new Form({
                        course_code:'',
                        consult_code:'',
                        approval_code:'',
                        treatments:'',
                        insurance_id:''
                }),
                sdoctors:[],
                customers: [],
                customer:''
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            getResults(page = 1) {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                if(this.sform.search == 1) {
                    this.sform.post('/api/findPrescbiredCourses?page=' + page)
                    .then(({data}) => {this.courses = data;});
                } else {
                    axios.get('/api/courses/get-yearwise/'+checkYear+'?page=' + page)
                    .then(response => {
                        this.courses = response.data;
                    });
                }
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
            },
            showCourses() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/courses/get-yearwise/'+checkYear).then(({ data }) => {
                    this.courses = data;
                    this.$Progress.finish();
                });
            },
            deleteCourse(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "Please enter the reason before deleting the course.",
                    footer: "<span class='text-danger'>All the appointments and invoices under this course will be cancelled and You won't be able to revert this!</span>",
                    type: 'question',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'off'  },
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Delete it!',
                    cancelButtonText: 'Not Now',
                    preConfirm: (reason) => {
                        return axios.post('/api/delete-course', {
                            course_code: id,
                            reason:reason
                        })
                        .then(() => {
                            swal.fire('Deleted!', 'Record has been deleted.', 'success');
                            Fire.$emit('changeyear');
                        })
                        .catch(error => {
                            swal.showValidationMessage(`${error}`)
                        })
                    },
                }).then(() => {
                            swal.fire('Deleted!', 'Record has been deleted.', 'success');
                        })
                        .catch(() => {
                            swal.fire('Oops!', 'Report to administrator .', 'error');
                        });
            },
            approvalInvoiceCourse(id, cid, iid) {
                this.loading = true;
                $('#setCourseModel').modal('show');
                axios.get('/api/get-course-treatments-only/'+id+'/'+cid+'/'+iid).then((response) => {
                    this.aiForm.fill(response.data);
                    this.loading = false;
                });
            },
            approveCourse() {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You have received approval from insurance company",
                    footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#C70039',
                    cancelButtonColor: '#0DC957',
                    confirmButtonText: 'Yes, Approve it!',
                    cancelButtonText: 'Not Now',
                }).then((result) => {
                    if (result.value) {
                        this.aiForm.post('/api/courses/approve-invoice')
                        .then(() => {
                            swal.fire('Approved!', 'Invoice has been approved.', 'success');
                            $('#setCourseModel').modal('hide');
                            this.aiForm.reset();
                            this.showCourses();
                        })
                        .catch(() => {
                            swal.fire('Not Done!', 'Course can not be updated. Please try again later.', 'error');
                        });
                    }
                });
            },
            makeCashCourse(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You want to convert this course in Cash.",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, Make it!',
                    cancelButtonText: 'Not Now',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/api/courses/make-cash-course', {
                            course_code:id
                        })
                        .then(() => {
                            swal.fire('Done!', 'Course has been updated as in Cash Mode.', 'success');
                            this.showCourses();
                        })
                        .catch(() => {
                            swal.fire('Not Done!', 'Course can not be converted in Cash Mode. Please try again later.', 'error');
                        });
                    }
                });
            },
            yearsList() {
                axios.get('/api/getAllYearsList').then(({ data }) => (this.yearList = data));
            },
            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/appointments/courses/'+year);
                Fire.$emit('changeyear');
            },
            createCourse() {
                $('#setCourseModel').modal('show');
            },
            searchAssets() {
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.sdoctors = data.data }).catch();
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
            },
            clearFilter() {
                this.sform.reset();
                this.showCourses();
            },
            CancelCourse() {

            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                this.sform.year = this.activeYear;
                this.courses = {};
                this.sform.post('/api/findPrescbiredCourses')
                    .then(({data}) => { this.courses = data; this.loader = false; })
                    .catch(() => { this.loader = false; });
            },
        },
        beforeMount() {
            this.yearsList();
            this.searchAssets();
            let activeId = this.$route.path.split("/");
            this.activeYear = activeId[3];
        },
        mounted() {
            this.showCourses();
            Fire.$on('changeyear', () => {
                this.showCourses();
            });
        }

    }
</script>
