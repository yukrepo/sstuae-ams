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
                    <h5 v-if="viewType == 'day'">{{ activeFullDate | titledate }}</h5>
                    <h5 v-else>{{ header.label }}</h5>
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

                            <div class="monthday-box" :class="[(day.label == 0) ? 'blocked-box' : ((day.isToday == true) ? 'isToday' : '' ), (day.weekdayNumber == 6)?'day-off':'']" v-for="day in week" :key="day.number+day.weekdayNumber+activeDate+activeMonth">

                                <span class="dayno" :class="[(day.label == 0) ? 'visibility-hidden' : (day.dateString < activeDateString) ? 'text-danger' : ((day.dateString > activeDateString) ? 'text-success' : '') ]" v-show="day.label" v-bind:value="day.dateString">

                                    <button :class="[(day.dateString == activeDateString) ? 'add-today-appoint' : 'no-appoint' ]" disabled type="button"> {{ day.label }} </button>

                                </span>
                                <div class="appoint-box" v-show="((day.weekdayNumber != 6) && (day.label != 0))">
                                    <span class="active-appoint" :class="[(mapp.appointment_type_id == 2)?'bg-teal':'bg-indigo']" v-for="(mapp, mpkey) in monthlyAppointments[day.fullODate]" :key="mpkey" @click="viewAppointment(mapp.appointment_code)">{{ (mapp.appointment_type_id == 2)?'T':'C' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="day-box" v-show="viewType == 'day'">
                    <div class="day-header">
                        <div class="hour-box">TimeSlots</div>
                        <div class="desc-box">
                            <div class="drheadline w-100">
                                Name
                            </div>
                        </div>
                    </div>
                    <div class="day-body">
                        <div class="day-liner" v-for="slot in timeslots" :key="slot.id">
                            <div class="hour-box">{{ slot.time }}</div>
                        <div class="desc-box"></div>
                        </div>
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
                            <li><p class="alert m-0"><b>Created By</b><br>{{ appointment.admin | capitalize }}</p></li>
                            <li><p class="alert m-0"><b>Created On</b><br>{{ appointment.created_at | setfulldate }}</p></li>
                            <li><p class="alert m-0"><b>Status</b><br>{{ appointment.status }}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

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
            month: _todayComps.month,
            year: _todayComps.year,
            catchmessage: '',
            active_location: '',
            appointments:'',
            appointment:'',
            treatments:{},
            consultations: {},
            doctors: {},
            listSlots: [],
            clistSlots: [],
            therapists: {},
            extratherapists:[],
            start_timeslots: {},
            end_timeslots: {},
            rooms: {},
            customer: {},
            filteredOptions: [],
            options: [],
            sselected: '',
            ttype:'',
            listoptions: [],
            start_times: {},
            end_times: {},
            rooms:{},
            appointmentDate:'',
            monthlyAppointments:'',
            inputProps: {
                id: "autosuggest__input",
                name: 'customer',
                onInputChange: this.onInputChange,
                placeholder: "search by mobile number / user ID / ID Card"
            },
            form: new Form({
                id:'',
                location_id:'',
                user_id:'',
                room_id:'',
                doctor_id: '',
                appointment_type_id:'',
                treatment_id:'',
                second_doctor_id:'',
                date:'',
                start_time:'',
                end_time:'',
                visit_type_id:'',
                status_id:4,
                followup_appointment:'',
                course_id:''
            })
        }
    },
    computed: {
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
                this.title = this.activeDate;
            },
            setDate(){
                this.activeMonth = this.today.getMonth() + 1;
                this.activeYear = this.today.getFullYear();
                this.activeFullDate = this.today;
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.today.getDate();
                this.activeDateString = Date.parse(this.activeDate);
            },
            changeView(vtype) {
                this.viewType = vtype;
                this.setDate();
                this.setTitle();
            },
            getTimeSlots(){
                axios.get('/api/get-time-slots').then(({ data }) => {
                    //console.log(data);
                    this.timeslots = data;
                });
            },
            moveThisDay() {
                this.activeFullDate = new Date(this.today.getTime());
                this.actDate = 'today';
            },
            moveNextDay() {
                let day = 60 * 60 * 24 * 1000;
                this.activeFullDate = new Date(this.activeFullDate.getTime() + day);
                this.actDate = '';
            },
            movePreviousDay() {
                let day = 60 * 60 * 24 * 1000;
                this.activeFullDate = new Date(this.activeFullDate.getTime() - day);
                this.actDate = '';
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
                axios.get('/api/get-monthly-appointments/'+date+'/2').then(({ data }) => {
                    this.monthlyAppointments = data;
                });
            },
            moveNextYear() {
                this.year++;
            },
            movePreviousYear() {
                this.year--;
            },
            makeAppointment(datestring) {
                this.form.reset();
                let currenttime = new Date(datestring+86400000);
                let dd = (currenttime.getDate() < 10 ? '0' : '') + currenttime.getDate();
                let MM = ((currenttime.getMonth() + 1) < 10 ? '0' : '') + (currenttime.getMonth() + 1);
                //console.log(currenttime.getHours());
                //console.log(currenttime.getMinutes());
                this.appointmentDate = currenttime;
                this.form.date = currenttime.getFullYear()+'-'+MM+'-'+dd;
                this.form.location_id = this.active_location;
                //console.log(this.form.date);
                if((currenttime.getHours() > this.listoptions['appointment_end_hour']) || ((currenttime.getHours() == this.listoptions['appointment_end_hour']) && (currenttime.getMinutes() >= 1))) {
                    swal.fire('Ohh Time is Over', 'Appointment Booking time is over. Please make bookings in upcoming appointments or Contact Administrator for support.', 'error');
                }
                else if(currenttime.getHours() < this.listoptions['appointment_start_hour']){
                    swal.fire('Wait for It', 'Appointment Booking time has not started yet. Please Contact Administrator for any support.', 'error');
                }
                else {
                    $('#addApponitModal').modal('show');
                }
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
                $('#userModal').modal('show');
            },
            hideCustomer() {
                $('#userModal').modal('hide');
            },
            showType(){
                let tType = this.form.treatment_id;
                axios.get('/api/treatments/'+tType).then((data) => {
                    if(data.data.is_it_dual == 1){ this.ttype = 1; }
                    else { this.ttype = 0; }
                }).catch();
            },
            getTimings(atype, did){
                axios.get('/api/appointments/get-start-timings?q='+this.form.date+'&at='+atype+'&lid='+this.form.location_id+'&did='+did).then(({ data }) => {
                    this.start_times = data;
                });
            },
            getClosings(st){
                axios.get('/api/appointments/get-end-timings?q='+this.form.date+'&at='+this.form.appointment_type_id+'&lid='+this.form.location_id+'&did='+this.form.doctor_id+'&st='+st).then(({ data }) => {
                    this.end_times = data;
                });
            },
            getRooms(et){
                axios.get('/api/appointments/get-rooms?q='+this.form.date+'&lid='+this.form.location_id+'&st='+this.form.start_time+'&et='+et).then(({ data }) => {
                    this.rooms = data;
                });
            },
            getAllAssets() {
                axios.get('/api/getTreatmentsList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getConsultationsList').then((data) => {this.consultations = data.data }).catch();
                axios.get('/api/getOnlyDoctorsList').then((data) => {this.doctors = data.data }).catch();
                axios.get('/api/getOnlyTherapistsList').then((data) => {this.therapists = data.data }).catch();
                 axios.get('/api/getTherapistsArrayList').then((data) => {this.extratherapists = data.data }).catch();
                axios.get('/api/getOptionsList').then((data) => {this.listoptions = data.data }).catch();
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            getPrimaryAssets() {
                axios.get('/api/get-active-user').then((response) => {this.active_location = response.data[0].location_id;}).catch();
            },
            onSelected(option) {
                axios.get('/api/searchCustomerByPhone?q='+option.item)
                    .then((data) => {
                        this.sselected = data.data[0].id;
                        this.form.user_id = data.data[0].id;
                        this.form.mobile = data.data[0].mobile;
                    })
                    .catch();
            },
            onInputChange(text) {
                if (text === '' || text === undefined) {
                    return;
                }
                axios.get('/api/searchCustomer?q='+text)
                    .then((data) => {
                        this.filteredOptions = [{
                            data: data.data  }] })
                    .catch();
            },
        },
        created() {
            this.getTimeSlots();
            this.setDate();
            //this.setTitle();
            //this.setWeeks();
            this.getPrimaryAssets();
            this.getAllAssets();
            this.setMonthlyAppointments();
        }
    }
</script>
