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
                            <div class="monthday-box-at" :class="[(day.label == 0) ? 'blocked-box' : ((day.isToday == true) ? 'isToday' : '' ), (day.weekdayNumber == 6)?'day-off': (monthlyAvailabilities[day.fullODate]) ? 'bg-lightpink' : 'bg-lightteal']" v-for="day in week" :key="day.number+day.weekdayNumber+activeDate+activeMonth">

                                    <span class="dayno" :class="[(day.label == 0) ? 'visibility-hidden' : '' ]" v-show="day.label" v-bind:value="day.dateString">
                                        <button :class="[(day.dateString == activeDateString) ? 'add-today-appoint' : 'no-appoint' ]" disabled type="button"> {{ day.label }} </button>

                                        <button class="btn btn-sm btn-pink float-left" type="button" v-if="(monthlyAvailabilities[day.fullODate] && (day.weekdayNumber != 6))"> <i class="fas fa-desktop"></i> </button>

                                        <button class="btn btn-sm btn-success float-left" @click="holidayModel(day.dateString)" v-else-if="(day.dateString > activeDateString) && (day.weekdayNumber != 6)"> <i class="fas fa-plus"></i> </button>

                                        <div class="avail-box" v-show="(day.dateString > activeDateString)">

                                            <div v-if="(day.weekdayNumber == 6)"></div>
                                            <div v-else-if="(monthlyAvailabilities[day.fullODate])">
                                                <span v-if="(monthlyAvailabilities[day.fullODate].remark)">
                                                    <b>Reason: </b>{{ monthlyAvailabilities[day.fullODate].remark }}
                                                </span>
                                                <span v-else>
                                                    No Reason added
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
        <div class="modal fade" id="addApponitModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addApponitModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createHoliday()">
                        <div class="modal-header">
                                    <h5 class="modal-title" id="createAppointModalTitle">Submit Absence</h5>
                                <span class="text-right float-right">
                                    <b class="text-uppercase m-r-5">DATE : </b> {{ form.date | setdate }}
                                    </span>
                        </div>
                        <div class="modal-body">
                            <div class="row border-bottom m-b-10 p-b-15 m-l-0 m-r-0">
                                <div class="col-12">
                                    <div class="alert alert-danger" v-show="catchmessage">
                                        {{ catchmessage }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="days" class="control-label">No Of Days</label>
                                        <input type="number" v-model="form.days" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="type" class="control-label">Leave Type</label>
                                        <select name="type" v-model="form.type" class="form-control">
                                            <option disabled value="">Select Leave Type</option>
                                            <option value="1">Full Day Off</option>
                                            <option value="2">Select Time Slots</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-b-10 p-b-15 m-l-0 m-r-0">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea v-model="form.remark" name="remark" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button  type="submit" class="btn btn-wide btn-success"> Submit </button>
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
            editmode: false,
            form: new Form({
                id:'',
                location_id:'',
                admin_id:'',
                doctor_id: '',
                remark:'',
                days:1,
                block_time_slots:'',
                date:'',
                status_id:4,
                type:''
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
            holidayModel(datestring){
                this.editmode = false;
                this.form.reset();
                let currenttime = new Date(datestring);
                let dd = (currenttime.getDate() < 10 ? '0' : '') + currenttime.getDate();
                let MM = ((currenttime.getMonth() + 1) < 10 ? '0' : '') + (currenttime.getMonth() + 1);
                //console.log(currenttime.getHours());
                //console.log(currenttime.getMinutes());
                this.appointmentDate = currenttime;
                this.form.date = currenttime.getFullYear()+'-'+MM+'-'+dd;
                this.form.location_id = this.active_location;
                $('#addApponitModal').modal('show');
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
                axios.get('/api/get-monthly-availabilities/'+date).then(({ data }) => {
                    this.monthlyAvailabilities = data;
                });
            },
            createHoliday() {
                //this.form.reset();
                this.form.post('/api/availability/create-absent')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addApponitModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Absent details has been added successfully'
                    });
                    this.setMonthlyAvailabilities();
                    this.$Progress.finish();
                })
                .catch(() => {

                });

            }
        },
        created() {
            this.setDate();
            this.setMonthlyAvailabilities();
        }
    }
</script>
