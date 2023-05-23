<template>
    <div>
        <div class="custom-calendar">
            <div class="cal-head row">
                <div class="col-md-3">
                    <div class="btn-group">
                        <button type="button" @click="movePreviousMonth" class="btn btn-sm btn-outline-purple">Back</button>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <h5><button type="button" @click="moveThisMonth" class="btn btn-sm" v-bind:class="[(actDate == 'today') ? 'btn-purple' : 'btn-outline-purple']">{{ header.label }} </button></h5>
                </div>
                <div class="col-md-3 text-right">
                    <div class="btn-group">
                        <button type="button" @click="moveNextMonth" class="btn btn-sm btn-outline-purple">Next</button>
                    </div>
                </div>
            </div>
            <div class="cal-body">
                <div class="month-box">
                    <div class="month-header">
                        <div class="week-box" v-for="(wname, key) in weekdayLabels" :key="key">{{ wname }}</div>
                    </div>
                    <div class="month-body">
                        <div class="month-liner" v-for="(week, wkey) in weeks" :key="wkey+activeMonth">
                            <div class="monthday-box-at" :class="[(day.label == 0) ? 'blocked-box' : ((day.isToday == true) ? 'btn-purple isToday' : '' ), (monthlyAvailabilities[day.fullODate]) ? 'bg-lightteal' : 'bg-lightgray']" v-for="day in week" :key="day.number+day.weekdayNumber+activeDate+activeMonth">

                                    <span class="dayno" :class="[(day.label == 0) ? 'visibility-hidden' : '' ]" v-show="day.label" v-bind:value="day.dateString">
                                        <button :class="[(day.dateString == activeDateString) ? 'add-today-appoint text-white' : 'no-appoint' ]" disabled type="button"> {{ day.label }} </button>

                                        <button class="btn btn-sm btn-green-theme float-left" type="button" v-if="(monthlyAvailabilities[day.fullODate])" @click="viewModel(day.dateString)"> <i class="fas fa-desktop"></i> </button>

                                        <button class="btn btn-sm btn-success float-left" @click="holidayModel(day.dateString)" v-else-if="(day.dateString > activeDateString)"> <i class="fas fa-plus"></i> </button>

                                        <div class="avail-box" v-show="(day.dateString > activeDateString)">

                                            <div v-if="(monthlyAvailabilities[day.fullODate])">
                                                <span v-if="(monthlyAvailabilities[day.fullODate].remark)">
                                                    {{ monthlyAvailabilities[day.fullODate].remark }}
                                                </span>
                                                <span v-else>
                                                    No Remark
                                                </span>
                                            </div>
                                        </div>

                                    </span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewModal" data-backdrop="static" data-keyboard="false" aria-labelledby="viewModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateShift() : createShift()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAppointModalTitle">Day Shift Timeslots</h5>
                            <span class="text-right float-right">
                                <b class="text-uppercase m-r-5">DATE : </b> {{ eform.date | setdate }}
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="modal-scroll-with-foot">
                                <div class="row m-0">
                                    <div class="col-md-4 p-r-5 p-l-5">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Active Timing Slots
                                                     <button type="button" @click="chooseNoneActive" class="float-right btn btn-danger btn-sm" v-if="allActive"><i class="fas fa-times"></i></button>
                                                     <button type="button" @click="chooseAllActive" class="float-right btn btn-success btn-sm m-r-5" v-else> <i class="fas fa-check"></i> </button>
                                                </h3>
                                            </div>
                                            <div class="card-body p-0 content-box">
                                                <table id="stock-alert" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="wf-50">SNo</th>
                                                            <th>TimeSlot</th>
                                                            <th class="wf-100">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(time, count) in ontimings" :key="time.id">
                                                            <td class="pf-3">{{ count + 1 }}</td>
                                                            <td class="pf-3">{{ time.time }}</td>
                                                            <td class="pf-0 text-center">
                                                                <i @click="removeSlot(time.id)" class="fas fa-check-square text-success big-icon-center" v-if="eform.slots.indexOf(time.id) >= 0"></i>
                                                                <i @click="addSlot(time.id)" class="fa fa-square text-muted big-icon-center" v-else></i>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 p-r-5 p-l-5">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Off Timing Slots
                                                     <button type="button" @click="chooseNoneInactive" class="float-right btn btn-danger btn-sm" v-if="allOffTime"><i class="fas fa-times"></i></button>
                                                     <button type="button" @click="chooseAllInactive" class="float-right btn btn-success btn-sm m-r-5" v-else> <i class="fas fa-check"></i> </button>
                                                </h3>
                                            </div>
                                            <div class="card-body p-0  content-box">
                                                <table id="stock-alert" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="wf-50">SNo</th>
                                                            <th>TimeSlot</th>
                                                            <th class="wf-50">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(time, count) in offtimings" :key="time.id">
                                                            <td class="pf-3">{{ count + 1 }}</td>
                                                            <td class="pf-3">{{ time.time }}</td>
                                                            <td class="pf-0 text-center">
                                                                 <i @click="removeSlot(time.id)" class="fas fa-check-square text-success big-icon-center" v-if="eform.slots.indexOf(time.id) >= 0"></i>
                                                                <i @click="addSlot(time.id)" class="fa fa-square text-muted big-icon-center" v-else></i>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 p-r-5 p-l-5">
                                        <div class="form-group">
                                            <label for="type" class="control-label">Select Type </label>
                                            <select name="type" v-model="eform.type" class="form-control" :class="{ 'is-invalid': eform.errors.has('type') }">
                                                <option value="" disabled>Select Type</option>
                                                <option value="1">Shift Timings</option>
                                                <option value="2">Weekly Off</option>
                                                <option value="3">Approved Leave/ Day Off</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="remark" class="control-label">Remark</label>
                                            <textarea v-model="eform.remark" name="remark" rows="3" class="form-control" :class="{ 'is-invalid': eform.errors.has('remark') }"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="hours" class="control-label">OT Status</label>
                                            <select name="hours" v-model="eform.hours" class="form-control" :class="{ 'is-invalid': eform.errors.has('hours') }">
                                                <option value="0">Normal Shifts</option>
                                                <option value="adjustment">Shift Adjustment</option>
                                                <option value="ot">Over Timings</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Close </button>
                            <button  type="submit" class="btn btn-wide btn-success"> {{ editmode ? 'Update' : 'Create' }} </button>
                        </div>
                    </form>
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
    props: ['doctor_id', 'bus'],
    data() {
        return {
            today: _today,
            weekdayLabels: _weekdayLabels,
            viewType: 'month',
            actDate: 'today',
            activeDate: '',
            activeMonth: '',
            activeYear: '',
            activeDateString: '',
            title: '',
            timeslots: '',
            month: _todayComps.month,
            year: _todayComps.year,
            active_location: '',
            catchmessage: '',
            appointmentDate:'',
            monthlyAvailabilities:'',
            shift:'',
            offtimings: [],
            ontimings:[],
            alltimings:[],
            editmode: false,
            eform: new Form({
                id:'',
                type:'',
                date:'',
                admin_id:'',
                doctor_id: '',
                remark:'',
                hours:'',
                slots:[],
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
        allActive() {
            if(this.ontimings.length >= 1) {
                let array1 = this.eform.slots;
                let array2 = this.ontimings.map((element) => { return element.id });
                let filteredArray = array1.filter(value => array2.includes(value));
                if(array2.length == filteredArray.length) {
                    return true
                } else {
                    return false
                }
            }   
            return false
        },
        allOffTime() {
            if(this.offtimings.length >= 1) {
                let array1 = this.eform.slots;
                let array2 = this.offtimings.map((element) => { return element.id });
                let filteredArray = array1.filter(value => array2.includes(value));
                if(array2.length == filteredArray.length) {
                    return true
                } else {
                    return false
                }
            }   
            return false
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
            holidayModel(datestring){
                this.editmode = false;
                this.eform.reset();
                let currenttime = new Date(datestring);
                let dd = (currenttime.getDate() < 10 ? '0' : '') + currenttime.getDate();
                let MM = ((currenttime.getMonth() + 1) < 10 ? '0' : '') + (currenttime.getMonth() + 1);
                let date = currenttime.getFullYear()+'-'+MM+'-'+dd;
                this.eform.date = date;
                this.eform.doctor_id = this.doctor_id;
                $('#viewModal').modal('show');
            },
            setDate(){
                this.activeMonth = this.today.getMonth() + 1;
                this.activeYear = this.today.getFullYear();
                this.activeDate = this.activeYear +'-'+ this.activeMonth +'-'+ this.today.getDate();
                this.activeDateString = Date.parse(this.activeDate);
            },
            changeView(vtype) {
                this.viewType = vtype;
                this.setDate();
                this.setTitle();
            },
            backDateMonth() {
                this.actDate = '';
                if(this.viewType == 'month') {
                    if (this.activeMonth > 1) { this.activeMonth-- ;}
                    else { this.activeMonth = 12; this.activeYear-- ; }
                    this.activeDate = this.activeYear +'-'+ this.activeMonth +'-1';
                } else if(this.viewType == 'day') {
                    if (this.activeMonth > 1) { this.activeMonth-- ;}
                    else { this.activeMonth = 12; this.activeYear-- ; }
                    this.activeDate = this.activeYear +'-'+ this.activeMonth +'-1';
                }
                this.setTitle();
            },
            nextDateMonth() {
                this.actDate = '';
            },
            setToday() {
                this.actDate = 'today';
                this.setDate();
                this.setTitle();
            },
            moveThisMonth() {
                this.month = _todayComps.month;
                this.year = _todayComps.year;
                this.setMonthlyAvailabilities();
            },
            moveNextMonth() {
                if (this.month < 12) {
                this.month++;
                } else {
                this.month = 1;
                this.year++;
                }
                this.setMonthlyAvailabilities();
            },
            movePreviousMonth() {
                if (this.month > 1) {
                this.month--;
                } else {
                this.month = 12;
                this.year--;
                }
                this.setMonthlyAvailabilities();
            },
            moveNextYear() {
                this.year++;
            },
            movePreviousYear() {
                this.year--;
            },
            setMonthlyAvailabilities(){
                let date = this.year+'-'+("0" + this.month).slice(-2)+'-01';
                //console.log(this.doctor_id);
                axios.get('/api/get-monthly-availabilities/'+date+'/'+this.doctor_id).then(({ data }) => {
                    this.monthlyAvailabilities = data;
                });
            },
            addSlot(tid) {
                if(this.eform.slots.includes(tid) == false) {
                    this.eform.slots.push(tid);
                }
            },
            removeSlot(tid) {
                if(this.eform.slots.includes(tid) == true) {
                    this.eform.slots.splice(this.eform.slots.indexOf(tid), 1);
                }
            },
            viewModel(datestring) {
                this.editmode = true;
                let currenttime = new Date(datestring);
                let dd = (currenttime.getDate() < 10 ? '0' : '') + currenttime.getDate();
                let MM = ((currenttime.getMonth() + 1) < 10 ? '0' : '') + (currenttime.getMonth() + 1);
                let date = currenttime.getFullYear()+'-'+MM+'-'+dd;
                axios.post('/api/get-day-shifts', {
                    date:date,
                    doctor_id:this.doctor_id
                }).then(({ data }) => {
                    this.eform.fill(data);
                });
                $('#viewModal').modal('show');
            },
            createShift() {
                //this.form.reset();
                this.eform.post('/api/availability/create-slot')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#viewModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Details has been added successfully'
                    });
                    this.setMonthlyAvailabilities();
                    this.$Progress.finish();
                    this.eform.reset();
                })
                .catch(() => {

                });

            },
            updateShift() {
                this.eform.post('/api/availability/modify-slot')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#viewModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Details has been added successfully'
                    });
                    this.setMonthlyAvailabilities();
                    this.$Progress.finish();
                    this.eform.reset();
                })
                .catch(() => {

                });
            },
            chooseAllInactive() {
                this.offtimings.forEach(element => {
                    if(this.eform.slots.indexOf(element.id) == -1) {
                        this.eform.slots.push(element.id);
                    }
                });
            },
            chooseNoneInactive() {
                this.offtimings.forEach(element => {
                    if(this.eform.slots.indexOf(element.id) >= 0) {
                        this.eform.slots.splice(this.eform.slots.indexOf(element.id), 1);
                    }
                });
             },
            chooseAllActive() {
                this.ontimings.forEach(element => {
                    if(this.eform.slots.indexOf(element.id) == -1) {
                        this.eform.slots.push(element.id);
                    }
                });
            },
            chooseNoneActive() {
                this.ontimings.forEach(element => {
                    if(this.eform.slots.indexOf(element.id) >= 0) {
                        this.eform.slots.splice(this.eform.slots.indexOf(element.id), 1);
                    }
                });
             },
            showTimeSlots() {
                axios.get('/api/get-off-time-slots').then(({ data }) => {  this.offtimings = data; });
                axios.get('/api/get-on-time-slots').then(({ data }) => {  this.ontimings = data; });
                axios.get('/api/get-all-time-slots').then(({ data }) => (this.alltimings = data));
            },
        },
        mounted() {
            this.bus.$on('submit', this.setMonthlyAvailabilities);
        },
        created() {
            this.setDate();
            this.setMonthlyAvailabilities();
            this.showTimeSlots();
        }
    }
</script>
