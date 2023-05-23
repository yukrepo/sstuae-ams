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
                            <model-select :options="customers" v-model="sform.user_id" placeholder="search patient"> </model-select>
                        </div>
                        <div class="col-md-2">
                             <select v-model="sform.package_id" class="form-control" @change="makeSearch">
                                <option disabled value="">Select Package</option>
                                <option v-for="cpackage in packages" :key="cpackage.id" v-bind:value="cpackage.id">
                                    {{ cpackage.name  }}
                                </option>
                            </select>
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
                        <div class="col-md-3 text-right">
                            <a class="btn btn-sm btn-danger" :href="'/appointments/schedule-package/'+activeYear">New Package</a>
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
                                <h3 class="card-title">Scheduled Packages's List</h3>
                            </div>
                            <div class="card-body p-0">
                                 <table id="coursetable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>Location</th>
                                            <th>Course ID</th>
                                            <th>Package</th>
                                            <th>User</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Started On</th>
                                            <th class="wf-100">End On</th>
                                            <th class="wf-100">Amount</th>
                                            <th class="wf-100">Payment</th>
                                            <th class="wf-100">Status</th>
                                            <th class="wf-85">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(course, sn) in courses.data" :key="course.id">
                                            <td>{{ (courses.current_page - 1)*(courses.per_page) + sn + 1 }}</td>
                                            <td> {{ course.clinic_name }} </td>
                                            <td>{{ course.course_code }}</td>
                                            <td>
                                                <button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewPackage(course.package_id)">{{ course.package | uppercase }}</button>
                                            </td>
                                            <td><button class="text-theme blank-btn text-uppercase font-weight-bold" @click="viewCustomer(course.user_id)">{{ course.first_name }} {{ (course.last_name)?course.last_name:'' }}</button></td>
                                            <td>{{ course.remark }}</td>
                                            <td>{{ course.start_date | setdate }}</td>
                                            <td>{{ course.end_date | setdate }}</td>
                                            <td>{{ course.amount | formatOMR }}</td>
                                            <td>
                                                <label class="status-label-full bg-danger" v-if="course.pstatus == 1">Pending</label>
                                                <label class="status-label-full bg-primary" v-else-if="course.pstatus == 2">Partial</label>
                                                <label class="status-label-full bg-success" v-else-if="course.pstatus == 3">Paid</label>
                                                 <label class="status-label-full bg-warning text-dark" v-else>Unknown</label>
                                            </td>
                                            <td>
                                                <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" :href="'/appointments/course-packages/view/'+course.course_code"><i class="fas fa-desktop"></i></a>
                                                <button class="btn btn-danger btn-sm" @click="deleteCourse(course.course_code)"><i class="fas fa-trash"></i></button>
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
                            <li><p class="alert m-0"><b>Name</b><br>{{ customer.first_name }} {{ customer.last_name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ customer.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ customer.contact_no }}</p></li>
                            <li><p class="alert m-0"><b>City</b><br>{{ customer.city }}</p></li>
                            <li><p class="alert m-0"><b>Address</b><br>{{ customer.address }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ customer.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Identity Card</b><br>{{ customer.verification_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.identity_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Insurance</b><br>{{ customer.policy_number }}
                                <button class="btn inacn-btn btn-success" v-if="customer.insurance_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ customer.status }}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="packageModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Package Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>Package Name</b><br>{{ cpackage.name }}</p></li>
                            <li><p class="alert m-0"><b>Duration</b><br>{{ cpackage.timeline+' days' }}</p></li>
                            <li><p class="alert m-0"><b>Cost</b><br>{{ cpackage.cost | formatOMR }}</p></li>
                            <li>
                                <p class="alert m-0"><b>Treatments</b>
                                    <label class="status-label bg-success"> {{ cpackage.treatments }} </label> <br>
                                    <table class="table table-striped bg-white my-2">
                                        <tbody>
                                            <tr v-for="(trtment, count) in cpackage.treatments_Summary" :key="count">
                                                <td><b>{{ trtment.count }}</b> {{ trtment.name }} </td>
                                                <td>{{ trtment.type }} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0"><b>Consultations</b>
                                    <label class="status-label bg-success"> {{ cpackage.treatments }} </label> <br>
                                    <table class="table table-striped bg-white my-2">
                                        <tbody>
                                            <tr v-for="(trtment, count) in cpackage.consultation_Summary" :key="count">
                                                <td><b>{{ trtment.count }}</b> {{ trtment.name }} </td>
                                                <td>{{ trtment.type }} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>
                                   <span class="status-label-full" :class="cpackage.css">{{ cpackage.status }}</span>
                                </p>
                            </li>
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
                activeYear: this.$route.params.id,
                courses:{},
                customer:'',
                cpackage:{},
                customers: [],
                packages:{},
                loader:false,
                loaderurl:'/svg/loop.gif',
                sform: new Form({
                        user_id:'',
                        course_code:'',
                        package_id:'',
                        search:'',
                        year:'',
                        ins_type:'',
                        status_id:''
                }),
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            searchAssets() {
                axios.get('/api/getCustomerSelectList').then((data) => {  this.customers = data.data }).catch();
                axios.get('/api/getPackagesList').then((data) => {  this.packages = data.data }).catch();
            },
            clearFilter() {
                this.sform.reset();
                this.showCourses();
            },
            getResults(page = 1) {
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                if(this.sform.search == 1) {
                    this.sform.post('/api/findSchedulesCourses?page=' + page).then(({data}) => {this.courses = data;});
                } else {
                    axios.get('/api/courses/get-packs-yearwise/'+checkYear+'?page=' + page).then(response => {this.courses = response.data; });
                }
                axios.get('/api/courses/get-packs-yearwise/'+checkYear+'?page='+ page).then(response => { this.courses = response.data; });
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
                axios.get('/api/customers/'+id).then((data) => {this.customer = data.data[0] }).catch();
            },
            viewPackage(pid) {
                axios.get('/api/packages/'+pid).then((data) => {this.cpackage = data.data }).catch();
                $('#packageModal').modal('show');
            },
            showCourses() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/courses/get-packs-yearwise/'+checkYear).then(({ data }) => {this.courses = data; this.$Progress.finish(); });
            },
            deleteCourse(id) {
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
                        this.form.delete('/api/courses/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            },
            yearsList() {
                axios.get('/api/getAllYearsList').then(({ data }) => (this.yearList = data));
            },
            makeSearch() {
                this.loader = true;
                this.sform.search = 1;
                this.sform.year = this.activeYear;
                this.courses = {};
                this.sform.post('/api/findSchedulesCourses')
                    .then(({data}) => { this.courses = data; this.loader = false; })
                    .catch(() => { this.loader = false; });
            },
            changeYear(year) {
                this.activeYear = year;
                this.$router.push('/appointments/course-packages/'+year);
                Fire.$emit('changeyear');
            }
        },
        beforeMount() {
            this.searchAssets();
            this.yearsList();
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
