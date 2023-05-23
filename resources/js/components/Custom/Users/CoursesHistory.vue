<template>
    <div>
        <div class="p-2 border-bottom text-center">
            <div class="btn-group" role="group" aria-label="First group">
                <button class="btn btn-sm" v-for="actyear in yearList"  v-on:click="changeCyear(actyear)" :key="actyear" :class="(activeYear == actyear)?'btn-green-theme':'btn-green-theme-outline'">{{ actyear }}</button>
            </div>
        </div>
        <div class="table-responsive p-0">
            <table id="coursetable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="wf-50">SNo</th>
                        <th>Course Type</th>
                        <th>Course ID</th>
                        <th>Doctor / Package</th>
                        <th>Remark</th>
                        <th class="wf-100">Started On</th>
                        <th>Insurance</th>
                        <th class="wf-85">Status</th>
                        <th class="wf-100">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(course, sn) in courses.courses" :key="course.id">
                        <td>{{ sn + 1 }}</td>
                        <td> <label class="status-label-full-outline text-dark">Prescribed</label> </td>
                        <td>{{ course.course_code }}</td>
                        <td>{{ course.doctor }}</td>
                        <td>{{ (course.remark)?course.remark.substr(0,10)+'...':'--' }}</td>
                        <td>{{ course.created_at | setdate }}</td>
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
                        <td>
                            <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/print-acknowledgement/'+course.course_code" >Print  </a>
                        </td>
                    </tr>
                    <tr v-for="(course, sn2) in courses.packages" :key="'p'+course.id">
                        <td>{{ sn2 +  courses.courses.length + 1}}</td>
                        <td> <label class="status-label-full-outline text-primary">Package</label> </td>
                        <td>{{ course.course_code }}</td>
                        <td>
                            <label class="status-label-full bg-teal">{{ course.package | uppercase }}</label>
                        </td>
                        <td>{{ course.remark }}</td>
                        <td>{{ course.start_date | setdate }}</td>
                        <td>
                            <label class="status-label-full bg-primary">Cash</label>
                        </td>
                        <td>
                            <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/print-package-acknowledgement/'+course.course_code" >Print  </a>
                        </td>
                    </tr>
                    <tr v-for="(course, sn3) in courses.directs" :key="course.id">
                        <td>{{ sn3 + courses.courses.length + courses.packages.length + 1 }}</td>
                        <td> <label class="status-label-full-outline text-green">Direct</label> </td>
                        <td>{{ course.course_code }}</td>
                        <td>--</td>
                        <td>{{ (course.remark)?course.remark:'--' }}</td>
                        <td>{{ course.created_at | setdate }}</td>
                        <td>--</td>
                        <td>
                            <label class="status-label-full" :class="course.status_css">{{  course.status }}</label>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" target="_blank" :href="'/appointments/print-dacknowledgement/'+course.course_code" >Print  </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['user_id', 'bus'],
        data() {
            return {
                startYear: 2018,
                yearList: [],
                editmode: false,
                currentYear: new Date().getFullYear(),
                activeYear: '',
                courses:{},
            }
        },
        methods: {
            isActiveYear(checkYear) {
                if(this.activeYear == checkYear){
                    return true;
                }
            },
            showAppointments() {
                this.$Progress.start();
                let checkYear = '';
                if(this.activeYear){ checkYear = this.activeYear; }
                else{ checkYear = this.currentYear;}
                axios.get('/api/courses/get-patient-history-yearwise/'+this.user_id+'/'+checkYear).then(({ data }) => {
                    this.courses = data;
                    this.$Progress.finish();
                });
            },
            yearsList() {
                axios.get('/api/getAllYearsList').then(({ data }) => (this.yearList = data));
            },
            changeCyear(year) {
                this.activeYear = year;
                Fire.$emit('changeCyear');
            }
        },
        mounted() {
            this.activeYear = this.currentYear;
        },
        created() {
            this.yearsList();
            this.showAppointments();
            Fire.$on('changeCyear', () => {
                this.showAppointments();
            });
        }

    }
</script>
