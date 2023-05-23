<template>
    <div>
        <div class="content courseDiv">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-1">
                            <input
                                type="text"
                                class="form-control"
                                v-model="sform.course_code"
                                placeholder="Course ID"
                            />
                        </div>
                        <div class="col-md-3">
                            <model-select
                                :options="customers"
                                v-model="sform.user_id"
                                placeholder="search patient"
                            >
                            </model-select>
                        </div>
                        <div class="col-md-2">
                            <select
                                v-model="sform.status_id"
                                class="form-control"
                                @change="makeSearch"
                            >
                                <option disabled value="">Select Status</option>
                                <option value="2">Active</option>
                                <option value="4">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <img
                                class="img-icon"
                                :src="loaderurl"
                                v-if="loader"
                            />
                            <input
                                type="submit"
                                class="btn btn-sm btn-primary"
                                value="Search"
                                v-else
                            />
                            <button
                                class="btn btn-sm btn-dark"
                                @click="sform.reset()"
                                type="button"
                            >
                                Reset
                            </button>
                            <button
                                class="btn btn-sm btn-danger float-right"
                                @click="clearFilter"
                                type="button"
                            >
                                Clear
                            </button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button
                                class="btn btn-sm btn-secondary"
                                type="button"
                                v-on:click="createCourse"
                            >
                                New Course
                            </button>
                            <div class="btn-group" role="group">
                                <button
                                    type="button"
                                    class="btn btn-sm wf-50"
                                    v-for="actyear in yearList"
                                    v-on:click="changeYear(actyear)"
                                    :key="actyear"
                                    :class="
                                        activeYear == actyear
                                            ? 'btn-green-theme'
                                            : 'btn-green-theme-outline'
                                    "
                                >
                                    {{ actyear }}
                                </button>
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
                                <h3 class="card-title">Course's List</h3>
                            </div>
                            <div class="card-body p-0">
                                <table
                                    id="coursetable"
                                    class="table table-striped table-hover"
                                >
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>Location</th>
                                            <th>Course ID</th>
                                            <th>User</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Started On</th>
                                            <th class="wf-85">Status</th>
                                            <th class="wf-150">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(course, sn) in courses.data"
                                            :key="course.id"
                                        >
                                            <td>
                                                {{
                                                    (courses.current_page - 1) *
                                                        courses.per_page +
                                                    sn +
                                                    1
                                                }}
                                            </td>
                                            <td>{{ course.clinic_name }}</td>
                                            <td>{{ course.course_code }}</td>
                                            <td>
                                                <button
                                                    class="
                                                        text-theme
                                                        blank-btn
                                                        text-uppercase
                                                        font-weight-bold
                                                    "
                                                    @click="
                                                        viewCustomer(
                                                            course.user_id
                                                        )
                                                    "
                                                >
                                                    {{ course.first_name }}
                                                    {{ course.last_name }}
                                                </button>
                                            </td>
                                            <td>
                                                {{
                                                    course.remark
                                                        ? course.remark
                                                        : "--"
                                                }}
                                            </td>
                                            <td>
                                                {{
                                                    course.created_at | setdate
                                                }}
                                            </td>
                                            <td>
                                                <label
                                                    class="status-label-full"
                                                    :class="course.status_css"
                                                    >{{ course.status }}</label
                                                >
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="
                                                        btn btn-sm btn-purple
                                                    "
                                                    v-show="
                                                        profile.admin_type_id ==
                                                            1 ||
                                                        profile.admin_type_id ==
                                                            4
                                                    "
                                                    @click="shiftCourse(course)"
                                                >
                                                    <i
                                                        class="
                                                            fas
                                                            fa-exchange-alt
                                                        "
                                                    ></i>
                                                </button>
                                                <a
                                                    class="
                                                        btn btn-sm btn-success
                                                    "
                                                    :href="
                                                        '/appointments/print-dacknowledgement/' +
                                                        course.course_code
                                                    "
                                                >
                                                    <i class="fas fa-map"></i>
                                                </a>
                                                <a
                                                    class="
                                                        btn btn-primary btn-sm
                                                    "
                                                    :href="
                                                        '/appointments/courses/direct-view/' +
                                                        course.course_code
                                                    "
                                                    ><i
                                                        class="fas fa-desktop"
                                                    ></i
                                                ></a>
                                                <button
                                                    class="
                                                        btn btn-danger btn-sm
                                                    "
                                                    type="button"
                                                    @click="
                                                        deleteCourse(
                                                            course.course_code
                                                        )
                                                    "
                                                    v-show="
                                                        (profile.admin_type_id ==
                                                            1 ||
                                                            profile.admin_type_id ==
                                                                4) &&
                                                        course.status_id <= 3
                                                    "
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination
                                    class="m-0 float-right"
                                    :limit="12"
                                    :data="courses"
                                    @pagination-change-page="getResults"
                                    :show-disabled="true"
                                >
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="setCourseModel"
            data-backdrop="static"
            data-keyboard="false"
            aria-labelledby="setCourseModelTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="submitCourse()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="setCourseModelTitle">
                                Create Course
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="user_id" class="control-label"
                                    >Search patient</label
                                >
                                <model-select
                                    :options="customers"
                                    name="user_id"
                                    id="user_id"
                                    aria-required="true"
                                    v-model="form.user_id"
                                    placeholder="search patient"
                                >
                                </model-select>
                                <has-error
                                    :form="form"
                                    field="user_id"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="control-label"
                                    >Remark</label
                                >
                                <textarea
                                    v-model="form.remark"
                                    name="remark"
                                    rows="4"
                                    id="remark"
                                    placeholder="enter remark"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('remark'),
                                    }"
                                ></textarea>
                                <has-error
                                    :form="form"
                                    field="remark"
                                ></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-wide btn-danger"
                                data-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <button
                                v-show="!editmode"
                                type="submit"
                                class="btn btn-wide btn-success"
                            >
                                Create
                            </button>
                            <button
                                v-show="editmode"
                                type="submit"
                                class="btn btn-wide btn-success"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div
            class="fade sidebar modal"
            id="userModal"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Customer Details</h3>
                        <button
                            type="button"
                            class="btn btn-close float-left"
                            data-dismiss="modal"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li>
                                <p class="alert m-0">
                                    <b>User ID</b><br />{{ customer.username }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Name</b><br />{{
                                        customer.first_name | capitalize
                                    }}
                                    {{ customer.last_name | capitalize }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Email</b><br />{{ customer.email }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Contact No</b><br />{{ customer.mobile }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>City</b><br />{{ customer.city }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Address</b><br />{{ customer.address }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Joined On</b><br />{{
                                        customer.created_at | setdate
                                    }}
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Identity Card</b><br />{{
                                        customer.verification_number
                                    }}
                                    <button
                                        class="btn inacn-btn btn-success"
                                        v-if="customer.identity_verified == 1"
                                    >
                                        Verified
                                    </button>
                                    <button
                                        class="btn inacn-btn btn-danger"
                                        v-else
                                    >
                                        Not Verified
                                    </button>
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Insurance</b><br />{{
                                        customer.policy_number
                                    }}
                                    <button
                                        class="btn inacn-btn btn-success"
                                        v-if="customer.insurance_verified == 1"
                                    >
                                        Verified
                                    </button>
                                    <button
                                        class="btn inacn-btn btn-danger"
                                        v-else
                                    >
                                        Not Verified
                                    </button>
                                </p>
                            </li>
                            <li>
                                <p class="alert m-0">
                                    <b>Status</b><br />{{ customer.status }}
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
            editmode: false,
            yearList: [],
            editmode: false,
            currentYear: new Date().getFullYear(),
            activeYear: this.$route.params.id,
            courses: {},
            customers: [],
            customer: "",
            profile: this.$store.getters.user,
            form: new Form({
                user_id: "",
                remark: "",
            }),
            loader: false,
            loaderurl: "/svg/loop.gif",
            sform: new Form({
                doctor_id: "",
                user_id: "",
                course_code: "",
                search: "",
                year: "",
                ins_type: "",
                status_id: "",
            }),
            sdoctors: [],
        };
    },
    methods: {
        isActiveYear(checkYear) {
            if (this.activeYear == checkYear) {
                return true;
            }
        },
        getResults(page = 1) {
            this.$Progress.start();
            let checkYear = "";
            if (this.activeYear) {
                checkYear = this.activeYear;
            } else {
                checkYear = this.currentYear;
            }
            axios
                .get(
                    "/api/courses/get-direct-yearwise/" +
                        checkYear +
                        "?page=" +
                        page
                )
                .then((response) => {
                    this.courses = response.data;
                });
        },
        clearFilter() {
            this.sform.reset();
            this.showCourses();
        },
        makeSearch() {
            this.loader = true;
            this.sform.search = 1;
            this.sform.year = this.activeYear;
            this.courses = {};
            this.sform
                .post("/api/findDirectCourses")
                .then(({ data }) => {
                    this.courses = data;
                    this.loader = false;
                })
                .catch(() => {
                    this.loader = false;
                });
        },
        showCourses() {
            this.$Progress.start();
            let checkYear = "";
            if (this.activeYear) {
                checkYear = this.activeYear;
            } else {
                checkYear = this.currentYear;
            }
            axios
                .get("/api/courses/get-direct-yearwise/" + checkYear)
                .then(({ data }) => {
                    this.courses = data;
                    this.$Progress.finish();
                });
        },
        deleteCourse(id) {
            swal.fire({
                title: "Are you sure?",
                text: "Please enter the reason before deleting the course.",
                footer: "<span class='text-danger'>All the appointments and invoices under this course will be cancelled and You won't be able to revert this!</span>",
                type: "question",
                input: "text",
                inputAttributes: { autocapitalize: "off" },
                showCancelButton: true,
                confirmButtonColor: "#C70039",
                cancelButtonColor: "#0DC957",
                confirmButtonText: "Yes, Delete it!",
                cancelButtonText: "Not Now",
                preConfirm: (reason) => {
                    return axios
                        .post("/api/delete-course", {
                            course_code: id,
                            reason: reason,
                        })
                        .then(() => {
                            swal.fire(
                                "Deleted!",
                                "Record has been deleted.",
                                "success"
                            );
                            Fire.$emit("changeyear");
                        })
                        .catch((error) => {
                            swal.showValidationMessage(`${error}`);
                        });
                },
            })
                .then(() => {
                    swal.fire(
                        "Deleted!",
                        "Record has been deleted.",
                        "success"
                    );
                })
                .catch(() => {
                    swal.fire("Oops!", "Report to administrator .", "error");
                });
        },
        yearsList() {
            let ayear = this.$route.path.split("/");
            this.activeYear = ayear[3];
            axios
                .get("/api/getAllYearsList")
                .then(({ data }) => (this.yearList = data));
        },
        changeYear(year) {
            this.activeYear = year;
            this.$router.push("/appointments/direct-courses/" + year);
            Fire.$emit("changeyear");
        },
        createCourse() {
            $("#setCourseModel").modal("show");
        },
        submitCourse() {
            this.form
                .post("/api/courses/create-direct-course")
                .then((data) => {
                    swal.fire({
                        title: "Its Done",
                        text: "Course has been created successfully.",
                        type: "success",
                    }).then((result) => {
                        Fire.$emit("changeyear");
                        $("#setCourseModel").modal("hide");
                    });
                })
                .catch((response) => {});
        },
        viewCustomer(id) {
            $("#userModal").modal("show");
            axios
                .get("/api/customers/" + id)
                .then((data) => {
                    this.customer = data.data[0];
                })
                .catch();
        },
        shiftCourse(course) {
            swal.fire({
                title: "Are you sure?",
                text: "You want to shift this course under Prescribed Courses.",
                footer: "<span class='text-danger'>You won't be able to revert this!</span>",
                type: "question",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off",
                    placeholder: "Enter consultation appointment ID",
                },
                showCancelButton: true,
                confirmButtonColor: "#C70039",
                cancelButtonColor: "#0DC957",
                confirmButtonText: "Yes, Shift it!",
                cancelButtonText: "Not Now",
                showLoaderOnConfirm: true,
                preConfirm: (apid) => {
                    return axios
                        .post("/api/courses/shift-course", {
                            course_code: course.course_code,
                            user_id: course.user_id,
                            apid: apid,
                        })
                        .then((response) => {
                            //console.log(response.data.status);
                            if (response.data.status == "error") {
                                throw new Error(response.data.message);
                            } else {
                                Fire.$emit("changeyear");
                                swal.fire(
                                    "Shifted!",
                                    "Course has been moved in prescribed courses successfully. Please check there.",
                                    "success"
                                );
                            }
                        })
                        .catch((error) => {
                            swal.showValidationMessage(`${error}`);
                        });
                },
            });
        },
    },
    mounted() {
        this.showCourses();
        axios
            .get("/api/getCustomerSelectList")
            .then((data) => {
                this.customers = data.data;
            })
            .catch();
        Fire.$on("changeyear", () => {
            this.showCourses();
        });
    },
};
</script>
