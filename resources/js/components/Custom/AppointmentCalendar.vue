<template>
    <div>
        <div class="custom-calendar">
            <div class="cal-head row">
                <div class="col-md-3">
                    <div class="btn-group" v-if="viewType == 'day'">
                        <button type="button" @click="movePreviousDay" class="btn btn-sm btn-outline-purple">Back</button>
                        <button type="button" @click="moveThisDay" class="btn btn-sm" v-bind:class="[(actDate == 'today') ? 'btn-purple' : 'btn-outline-purple']">Today</button>
                        <button type="button" @click="moveNextDay" class="btn btn-sm btn-outline-purple">Next</button>
                    </div>
                    <div class="btn-group" v-else>
                        <button type="button" @click="movePreviousMonth" class="btn btn-sm btn-outline-purple">Back</button>
                        <button type="button" @click="moveThisMonth" class="btn btn-sm" v-bind:class="[(actDate == 'today') ? 'btn-purple' : 'btn-outline-purple']">Today</button>
                        <button type="button" @click="moveNextMonth" class="btn btn-sm btn-outline-purple">Next</button>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div v-if="viewType == 'day'">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ activeFullDate | titledate }}</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group">
                                    <button type="button" @click="changeDoctorView()" class="btn btn-sm" v-bind:class="[(drView == 'doctor') ? 'btn-purple' : 'btn-outline-purple']">Doctor slots</button>
                                    <button type="button" @click="changeDoctorView()" class="btn btn-sm" v-bind:class="[(drView == 'therapist') ? 'btn-purple' : 'btn-outline-purple']">Therapist slots</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <h5>{{ header.label }}</h5>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <div class="btn-group">
                        <button type="button" @click="changeView('month')" class="btn btn-sm" v-bind:class="[(viewType == 'month') ? 'btn-purple' : 'btn-outline-purple']">Month</button>
                        <button type="button" @click="changeView('day')" class="btn btn-sm" v-bind:class="[(viewType == 'day') ? 'btn-purple' : 'btn-outline-purple']">Day</button>
                    </div>
                </div>
            </div>
            <div class="cal-body">
                <div class="month-box" v-show="viewType == 'month'">
                    <div class="month-header">
                        <div class="week-box" v-for="(wname, key) in weekdayLabels" :key="key">{{ wname }}</div>
                    </div>
                    <div class="month-body">
                        <div class="month-liner" v-for="(week, wkey) in weeks" :key="wkey+activeMonth">

                            <div class="monthday-box" :class="[(day.label == 0) ? 'blocked-box' : ((day.isToday == true) ? 'isToday' : '' )]" v-for="day in week" :key="day.number+day.weekdayNumber+activeDate+activeMonth">

                                <span class="dayno" :class="[(day.label == 0) ? 'visibility-hidden' : (day.dateString < activeDateString) ? 'text-danger' : ((day.dateString > activeDateString) ? 'text-success' : '') ]" v-show="day.label" v-bind:value="day.dateString">


                                    <button disabled class="no-appoint"> {{ day.label }} </button>

                                    <button class="btn btn-green-theme-outline btn-sm" @click="gotoDayView(day.dateString+28800000)"> <i class="fas fa-eye"></i> </button>
                                </span>
                                <div class="appoint-box" v-show="day.label != 0">
                                    <!-- <span v-b-popover.hover.bottom="mapp.appointment_code+' - '+mapp.first_name" class="active-appoint" :class="[(mapp.appointment_type_id == 2)?'bg-teal':'bg-indigo']" v-for="(mapp, mpkey) in monthlyAppointments[day.fullODate]" :key="mpkey" @click="viewAppointment(mapp.appointment_code)">{{ (mapp.appointment_type_id == 2)?'T':'C' }}</span> -->
                                   <p class="m-1">consultation  <span class="counter_num"> {{  monthlyAppointments[day.label+'c'] }} </span> </p>
                                    <p class="m-1">Treatments  <span class="counter_num"> {{  monthlyAppointments[day.label+'t'] }} </span> </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="day-box" v-show="viewType == 'day'">
                    <div class="day-header">
                        <div class="hour-box">TimeSlots</div>
                        <div class="desc-box" v-if="drView == 'therapist'">
                            <div class="drheadline" :style="'width:'+100/therapists.length+'%'" v-for="(therapist, tkey) in therapists" :key="tkey">
                                {{ therapist.name }}
                            </div>
                        </div>
                        <div class="desc-box" v-else>
                            <div class="drheadline" :style="'width:'+100/doctors.length+'%'" v-for="(doctor, dkey) in doctors" :key="dkey">
                                {{ doctor.name }}
                            </div>
                        </div>
                    </div>
                    <div class="day-body day-content-box">
                        <div v-if="drView == 'therapist'">
                            <div class="day-liner" v-for="slot in timeslots" :key="slot.id">
                                <div class="hour-box">{{ slot.time }}</div>
                                <div class="desc-box">
                                    <div class="drheadline" :style="'width:'+100/therapists.length+'%'" :class="[dailyAppointments[therapist.id+'-'+slot.id+'-css']]" v-for="(therapist, tkey) in therapists" :key="tkey">

                                        <span class="btn-apt border-first-apt" v-if="(dailyAppointments[therapist.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[therapist.id+'-'+slot.id+'-code'])">
                                            <a class="text-danger" href="javascript:;" @click="viewAppointment(dailyAppointments[therapist.id+'-'+slot.id+'-code'])">{{ dailyAppointments[therapist.id+'-'+slot.id+'-code'] }}</a> ({{ dailyAppointments[therapist.id+'-'+slot.id+'-status']}})
                                            <b class="float-right bg-dark py-0 px-1 text-white text-uppercase code-status">
                                                {{ dailyAppointments[therapist.id + "-" + slot.id + "-icode"] }}
                                            </b>
                                            <span class="float-right cursor-pointer call-btn" :class="'callbtn-'+dailyAppointments[therapist.id + '-' + slot.id + '-call']" @click="Youcall(dailyAppointments[therapist.id + '-' + slot.id + '-code'])">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                        </span>
                                        <span class="btn-apt" v-else-if="(dailyAppointments[therapist.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[therapist.id+'-'+slot.id+'-code1'])">
                                            <b class="text-dark">{{ dailyAppointments[therapist.id+'-'+slot.id+'-code1'] }} - {{ dailyAppointments[therapist.id+'-'+slot.id+'-coden1'] }}</b>
                                        </span>
                                        <span class="btn-apt" v-else-if="(dailyAppointments[therapist.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[therapist.id+'-'+slot.id+'-code2'])">
                                            <b class="text-green-theme">{{ (dailyAppointments[therapist.id+'-'+slot.id+'-code2'].length >= 21) ?dailyAppointments[therapist.id+'-'+slot.id+'-code2'].substr(0, 20)+'....':dailyAppointments[therapist.id+'-'+slot.id+'-code2'] }}</b>
                                        </span>

                                        <span class="btn-apt" v-else-if="dailyAppointments[therapist.id+'-'+slot.id+'-type'] == 'block'">

                                            <span v-if="dailyAppointments[therapist.id+'-'+slot.id+'-camp'] == dailyAppointments[therapist.id+'-'+(slot.id-1)+'-camp']">
                                                &nbsp;
                                            </span>
                                            <span v-else>
                                                Reason - {{ dailyAppointments[therapist.id+'-'+slot.id+'-code'] }}
                                            </span>
                                        </span>
                                        <span class="btn-apt" v-else> &nbsp;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="day-liner" v-for="slot in timeslots" :key="slot.id">
                                <div class="hour-box">{{ slot.time }}</div>
                                <div class="desc-box">
                                    <div class="drheadline" :style="'width:'+100/doctors.length+'%'" :class="[dailyAppointments[doctor.id+'-'+slot.id+'-css']]" v-for="(doctor, dkey) in doctors" :key="dkey">

                                        <span class="btn-apt border-first-apt" v-if="(dailyAppointments[doctor.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[doctor.id+'-'+slot.id+'-code'])">
                                            <a class="text-danger" href="javascript:;" @click="viewAppointment(dailyAppointments[doctor.id+'-'+slot.id+'-code'])">{{ dailyAppointments[doctor.id+'-'+slot.id+'-code'] }}</a> ({{ dailyAppointments[doctor.id+'-'+slot.id+'-status']}})
                                            <b class="float-right bg-dark py-0 px-1 text-white text-uppercase code-status">
                                                {{ dailyAppointments[doctor.id + "-" + slot.id + "-icode"] }}
                                            </b>
                                            <span class="float-right cursor-pointer call-btn" :class="'callbtn-'+dailyAppointments[doctor.id + '-' + slot.id + '-call']" @click="Youcall(dailyAppointments[doctor.id + '-' + slot.id + '-code'])">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                        </span>
                                        <span class="btn-apt" v-else-if="(dailyAppointments[doctor.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[doctor.id+'-'+slot.id+'-code1'])">
                                            <b class="text-dark">{{ dailyAppointments[doctor.id+'-'+slot.id+'-code1'] }} - {{ dailyAppointments[doctor.id+'-'+slot.id+'-coden1'] }}</b>
                                        </span>
                                        <span class="btn-apt" v-else-if="(dailyAppointments[doctor.id+'-'+slot.id+'-type'] == 'book') && (dailyAppointments[doctor.id+'-'+slot.id+'-code2'])">
                                            <b class="text-green-theme">{{ (dailyAppointments[doctor.id+'-'+slot.id+'-code2'].length >= 31) ?dailyAppointments[doctor.id+'-'+slot.id+'-code2'].substr(0, 30)+'....':dailyAppointments[doctor.id+'-'+slot.id+'-code2'] }}</b>
                                        </span>

                                        <span class="btn-apt" v-else-if="dailyAppointments[doctor.id+'-'+slot.id+'-type'] == 'block'">

                                            <span v-if="dailyAppointments[doctor.id+'-'+slot.id+'-camp'] == dailyAppointments[doctor.id+'-'+(slot.id-1)+'-camp']">
                                                &nbsp;
                                            </span>
                                            <span v-else>
                                                Reason - {{ dailyAppointments[doctor.id+'-'+slot.id+'-code'] }}
                                            </span>
                                        </span>
                                        <span class="btn-apt" v-else> &nbsp;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="prouserModal"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Customer Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>User ID</b><br>{{ procustomer.username }}</p></li>
                            <li><p class="alert m-0"><b>Name</b><br>{{ procustomer.first_name }} {{ procustomer.last_name }}</p></li>
                            <li><p class="alert m-0"><b>Email</b><br>{{ procustomer.email }}</p></li>
                            <li><p class="alert m-0"><b>Contact No</b><br>{{ procustomer.contact_no }}</p></li>
                            <li><p class="alert m-0"><b>City</b><br>{{ procustomer.city }}</p></li>
                            <li><p class="alert m-0"><b>Address</b><br>{{ procustomer.address }}</p></li>
                            <li><p class="alert m-0"><b>Joined On</b><br>{{ procustomer.created_at | setdate }}</p></li>
                            <li><p class="alert m-0"><b>Identity Card</b><br>{{ procustomer.verification_number }}
                                <button class="btn inacn-btn btn-success" v-if="procustomer.identity_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Insurance</b><br>{{ procustomer.policy_number }}
                                <button class="btn inacn-btn btn-success" v-if="procustomer.insurance_verified == 1">Verified</button>
                                <button class="btn inacn-btn btn-danger" v-else>Not Verified</button></p>
                            </li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ procustomer.status }}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="appointmentModal"  data-backdrop="static" data-keyboard="false" data-easein="slideRightBigIn" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Appointment Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components">
                        <ul class="link-list">
                            <li><p class="alert m-0"><b>Appointment ID</b><br>{{ appointment.appointment_code }}</p></li>
                            <li><p class="alert m-0"><b>Appointment Type</b><br>{{ appointment.appointment_type }}</p></li>
                            <li v-show="appointment.course_id"><p class="alert m-0">
                                <b>Course Details</b><br>{{ appointment.course_type }} - {{ appointment.course_id }}<br>
                                {{ appointment.course_reason }} </p></li>
                            <li><p class="alert m-0"><b>Date - Timing</b><br>
                                {{ appointment.date | setdate }}
                                ( {{ listSlots[appointment.start_timeslot] }} - {{ clistSlots[appointment.end_timeslot] }} )
                                </p></li>
                            <li><p class="alert m-0"><b>Treatment</b><br>{{ appointment.reason }}</p></li>
                            <li><p class="alert m-0"><b>Doctor/Therapist</b><br>
                                {{ appointment.doctor }}
                                <span v-show="appointment.second_doctor_id">
                                    , {{ extratherapists[appointment.second_doctor_id] }}
                                </span>
                            </p></li>
                            <li><p class="alert m-0"><b>Visit Type</b><br>{{ appointment.visit_type }}</p></li>
                            <li><p class="alert m-0"><b>Patient Detail</b><br>NAME: {{ appointment.first_name }} {{ appointment.last_name }}<br>
                                                                            Mobile: {{ appointment.mobile }}<br>
                                                                            Address: {{ appointment.address }}<br>
                                                                            City: {{ appointment.city }}</p></li>
                            <li><p class="alert m-0"><b>Created By</b><br>{{ appointment.admin | capitalize }} On  {{ appointment.created_at | setfulldate }}</p></li>
                            <li><p class="alert m-0"><b>Remark</b><br>{{ (appointment.user_remark)?appointment.user_remark:'No remark added' }}</p></li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ appointment.status }}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="callModal"  data-backdrop="static" data-keyboard="false" data-easein="slideRightBigIn">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Calling Status</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body mt-2" v-if="loader == 2">
                        <img class="wf-50" :src="loaderurl" alt="loading...">
                    </div>
                    <div class="modal-body" v-else>
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-condensed bg-white">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase">Details</th>
                                    </tr>
                                </thead>
                                <tbody v-if="callForm.call_status.length >= 1">
                                    <tr v-for="(cs, csi) in callForm.call_status" :key="csi">
                                        <td>
                                            {{ cs.status | uppercase }}  <br>
                                            <small class="d-block text-right">
                                                By: <b>{{ cs.admin_name }}</b> on <b>{{ cs.time }}</b>
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td>
                                            No calls have been made yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group" v-if="isNotPast">
                            <label for="call_point" class="control-label text-white">Calling Status</label>
                            <select v-model="callForm.call_point" name="call_point" id="call_point" class="form-control" :class="{ 'is-invalid': callForm.errors.has('call_point') }">
                                <option disabled value="0">Select Status</option>
                                <option value="1">Not Answered</option>
                                <option value="2">Not Coming</option>
                                <option value="3">Confirmed</option>
                            </select>
                            <has-error :form="callForm" field="call_point"></has-error>
                        </div>
                        <div class="form-group"  v-if="isNotPast">
                            <button class="btn btn-block btn-success btn-sm" @click="updateCallStatus" type="button">Update Calling Status</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="inner-overlay" v-show="loader == 1">
            <img :src="loaderurl" alt="loading...">
        </div>
    </div>
</template>
<style scoped>
.counter_num{background: #eeeeee;
    width: 40px;
    padding: 2px 10px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-block;
    text-align: center;}
</style>
<script>
import moment from 'moment';
const _daysInMonths = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
const _weekdayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const _weekdayLength = 3;
const _weekdayCasing = 'title';
const _monthLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const _monthLength = 0;
const _monthCasing = 'title';

// Helper function for label transformation
const _transformLabel = (label, length, casing) => {
  label = length <= 0 ? label : label.substring(0, length);
  if (casing.toLowerCase() === 'lower') return label.toLowerCase();
  if (casing.toLowerCase() === 'upper') return label.toUpperCase();
  return label;
};

// Today's data
const _today = new Date();
const _todayComps = {
  year: _today.getFullYear(),
  month: _today.getMonth() + 1,
  day: _today.getDate()
};
export default {
    data() {
        return {
            today: _today,
            weekdayLabels: _weekdayLabels,
            viewType: 'month',
            drView: 'doctor',
            actDate: 'today',
            activeDate: '',
            activeMonth: '',
            activeYear: '',
            activeDateString: '',
            activeFullDate: '',
            title: '',
            timeslots: '',
            timetaken:'',
            dayLabel:'',
            //weeks:'',
            month: _todayComps.month,
            year: _todayComps.year,
            doctors: [],
            therapists: [],
            procustomer:'',
            appointment:'',
            listSlots:[],
            clistSlots:[],
            monthlyAppointments:'',
            dailyAppointments:[],
            extratherapists:[],
            loader: false,
            loaderurl: '/images/spinner.gif',
            callForm: new Form({
                appointment_code:'',
                call_point:'',
                call_status:''
            })
        }
    },
    computed: {
        isNotPast() {
            if(this.actDate == 'today') {
                return true
            }
            let active_day = Date.parse(this.activeDate);
            let tdate = Date.parse(new Date());
            if(active_day >= tdate) {
                return true
            }
            return false
        },
        // Our component exposes month as 1-based, but sometimes we need 0-based
        monthIndex() {
            return this.month - 1;
        },
        // State referenced by header (no dependencies yet...)
        months() {
            return _monthLabels.map((ml, i) => ({
            label: _transformLabel(ml, _monthLength, _monthCasing),
            number: i + 1,
            }));
        },
        // State for weekday header (no dependencies yet...)
        weekdays() {
            return _weekdayLabels.map((wl, i) => {
            return {
                label: _transformLabel(wl, _weekdayLength, _weekdayCasing),
                number: i + 1,
            };
            });
        },
        // State for calendar header
        header() {
            const month = this.months[this.monthIndex];
            return {
            month,
            year: this.year.toString(),
            shortYear: this.year.toString().substring(2, 4),
            label: month.label + ' ' + this.year,
            };
        },
        // Returns number for first weekday (1-7), starting from Sunday
        firstWeekdayInMonth() {
            return new Date(this.year, this.monthIndex, 1).getDay() + 1;
        },
        // Returns number of days in the current month
        daysInMonth() {
            // Check for February in a leap year
            const isFebruary = this.month === 2;
            const isLeapYear = (this.year % 4 == 0 && this.year % 100 != 0) || this.year % 400 == 0;
            if (isFebruary && isLeapYear) return 29;
            // ...Just a normal month
            return _daysInMonths[this.monthIndex];
        },
        weeks() {
            const weeks = [];
            let monthStarted = false, monthEnded = false;
            let monthDay = 0; let fullDate = 0; let dateString = '';
            // Cycle through each week of the month, up to 6 total
            for (let w = 1; w <= 6 && !monthEnded; w++) {
            // Cycle through each weekday
            const week = [];
            for (let d = 1; d <= 7; d++) {
                // We need to know when to start counting actual month days
                if (!monthStarted && d >= this.firstWeekdayInMonth) {
                // Initialize day counter
                monthDay = 1;
                // ...and flag we're tracking actual month days
                monthStarted = true;
                // Still in the middle of the month (hasn't ended yet)
                } else if (monthStarted && !monthEnded) {
                // Increment the day counter
                monthDay += 1;
                }
                // Append day info for the current week
                // Note: this might or might not be an actual month day
                //  We don't know how the UI wants to display various days,
                //  so we'll supply all the data we can

                week.push({
                    label: monthDay ? monthDay.toString() : '0',
                    fullDate:  monthDay ?  monthDay+'-'+this.month+'-'+this.year : '',
                    fullODate: monthDay ?  this.year+'-'+("0" + this.month).slice(-2)+'-'+("0" + monthDay).slice(-2) : '',
                    number: monthDay,
                    weekdayNumber: d,
                    weekNumber: w,
                    beforeMonth: !monthStarted,
                    afterMonth: monthEnded,
                    inMonth: monthStarted && !monthEnded,
                    isToday: monthDay === _todayComps.day && this.month === _todayComps.month && this.year === _todayComps.year,
                    isFirstDay: monthDay === 1,
                    isLastDay: monthDay === this.daysInMonth,
                    dateString: monthDay ? Date.parse(this.year+'-'+this.month+'-'+monthDay) : '',
                });

                // Trigger end of month on the last day
                if (monthStarted && !monthEnded && monthDay >= this.daysInMonth) {
                    monthDay = 0;
                    monthEnded = true;
                }
            }
            // Append week info for the month
            weeks.push(week);
            }
            //console.log(weeks);
            return weeks;
        },
    },
        methods: {
            setTitle(){
                this.title = this.activeFullDate;
            },
            isExist(arr, cid) {
                for (let i = 0; i < arr.length; i++) {
                    if(cid == parseInt(arr[i])){
                        return true;
                    }
                };
                return false;
            },
            setDate(){
                this.activeMonth = this.today.getMonth() + 1;
                this.activeYear = this.today.getFullYear();
                this.activeFullDate = this.today;
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.today.getDate();
                this.activeDateString = Date.parse(this.activeDate);
            },
            resetFollowupCheck() {
                this.form.followup_appointment = '';
                this.form.followup_verified = '';
                this.followuptext = '';
            },
            changeView(vtype) {
                this.viewType = vtype;
                this.setDate();
                this.setTitle();
                if(vtype == 'day'){
                    this.setDailyAppointments();
                }
            },
            changeDoctorView() {
                if(this.drView == 'doctor') {
                    this.drView = 'therapist';
                } else {
                    this.drView = 'doctor';
                }
            },
            getTimeSlots(){
                axios.get('/api/get-all-time-slots').then(({ data }) => { this.timeslots = data; });
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                axios.get('/api/getTherapistsArrayList').then((data) => {this.extratherapists = data.data }).catch();
            },
            moveThisDay() {
                this.activeFullDate = new Date(this.today.getTime());
                this.actDate = 'today';
                this.activeMonth = parseInt(this.activeFullDate.getMonth())+1;
                this.activeYear = this.activeFullDate.getFullYear();
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.activeFullDate.getDate();
                this.setDailyAppointments();
            },
            moveNextDay() {
                let day = 60 * 60 * 24 * 1000;
                this.activeFullDate = new Date(this.activeFullDate.getTime() + day);

                this.activeMonth = parseInt(this.activeFullDate.getMonth())+1;
                this.activeYear = this.activeFullDate.getFullYear();
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.activeFullDate.getDate();

                let todaysDate = this.today.getFullYear() +'-'+ parseInt(this.today.getMonth()+1) +'-'+ this.today.getDate();
                let str1 = Date.parse(todaysDate);
                let str2 = Date.parse(this.activeDate);
                if(str1 == str2) { this.actDate = 'today';  }
                else { this.actDate = ''; }
                this.setDailyAppointments();
            },
            movePreviousDay() {
                let day = 60 * 60 * 24 * 1000;
                this.activeFullDate = new Date(this.activeFullDate.getTime() - day);

                this.activeMonth = parseInt(this.activeFullDate.getMonth())+1;
                this.activeYear = this.activeFullDate.getFullYear();
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.activeFullDate.getDate();

                let todaysDate = this.today.getFullYear() +'-'+ parseInt(this.today.getMonth()+1) +'-'+ this.today.getDate();
                let str1 = Date.parse(todaysDate);
                let str2 = Date.parse(this.activeDate);
                if(str1 == str2) { this.actDate = 'today';  }
                else { this.actDate = ''; }
                this.setDailyAppointments();
            },
            moveThisMonth() {
                this.month = _todayComps.month;
                this.year = _todayComps.year;
                this.setMonthlyAppointments();
            },
            moveNextMonth() {
                if (this.month < 12) {
                this.month++;
                } else {
                this.month = 1;
                this.year++;
                }
                this.setMonthlyAppointments();
            },
            movePreviousMonth() {
                if (this.month > 1) {
                this.month--;
                } else {
                this.month = 12;
                this.year--;
                }
                this.setMonthlyAppointments();
            },
            setMonthlyAppointments(){
                let date = this.year+'-'+("0" + this.month).slice(-2)+'-01';
                axios.get('/api/get-monthly-appointments/'+date).then(({ data }) => {
                    this.monthlyAppointments = data;
                });
            },
            setDailyAppointments(){
                this.loader = 1
                let date = this.activeFullDate.getFullYear()+'-'+("0" + parseInt(this.activeFullDate.getMonth()+1)).slice(-2)+'-'+("0" + this.activeFullDate.getDate()).slice(-2);
                axios.get('/api/get-daily-appointments/'+date).then(({ data }) => {
                   // console.log(this.objToArray(data));
                    this.dailyAppointments = data;
                    this.loader = false
                });
            },
            moveNextYear() {
                this.year++;
            },
            movePreviousYear() {
                this.year--;
            },
            gotoDayView(daystring) {
                this.activeDateString = daystring;
                this.activeFullDate = new Date(daystring);
                this.activeMonth = parseInt(this.activeFullDate.getMonth())+1;
                this.activeYear = this.activeFullDate.getFullYear();
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.activeFullDate.getDate();
                let todaysDate = this.today.getFullYear() +'-'+ parseInt(this.today.getMonth()+1) +'-'+ this.today.getDate();
                this.viewType = 'day';
                let str1 = Date.parse(todaysDate);
                let str2 = Date.parse(this.activeDate);
                if(str1 == str2) { this.actDate = 'today';  }
                else { this.actDate = ''; }
                this.setDailyAppointments();
            },
            viewAppointment(id) {
                axios.get('/api/appointments/view/'+id)
                    .then((data) => {this.appointment = data.data[0] })
                    .catch();
                $('#appointmentModal').modal('show');
            },
            ////// create appointment

            viewCustomer(id) {
                if(id == ''){id = this.form.user_id}
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
                $('#prouserModal').modal('show');
            },
            hideCustomer() {
                $('#prouserModal').modal('hide');
            },
            Youcall(apid) {
                this.loader = true;
                axios.get('/api/appointments/get-call-status/'+apid).then((data) => { 
                    this.callForm.fill(data.data); this.loader = false; });
                $('#callModal').modal('show');
            },
            updateCallStatus() {
                swal.fire({
                    title: "Are you sure?",
                    text: "You want to update the call status for this patient?",
                    type: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#C70039",
                    cancelButtonColor: "#0DC957",
                    confirmButtonText: "Yes, Please update!",
                    cancelButtonText: "Not Now",
                }).then((result) => {
                    if (result.value) {
                        this.callForm.post("/api/called-appointment")
                        .then((response) => {
                            toast.fire({
                                type: "success",
                                title: "Appointment updated successfully.",
                            });
                            this.setDailyAppointments();
                            this.callForm.reset();
                            $('#callModal').modal('hide');
                        });
                    }
                });
            },
        },
        beforeMount() {
            this.setDate();
        },
        mounted() {
            this.getTimeSlots();
            this.setMonthlyAppointments();
            Fire.$on('AfterCreate', () => {
                this.setMonthlyAppointments();
            });
        }
    }
</script>
