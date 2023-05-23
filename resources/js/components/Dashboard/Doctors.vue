<template>
    <div>
        <div class="row synop mb-3">
            <div class="col-md-6 col-sm-6 col-12 m-b-20">
                <div class="card m-b-20">
                    <div class="card-header">
                        <h3 class="card-title">TODAYS REPORT</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row p-3">
                            <div class="col-6 text-uppercase border-right">
                                <h5>day Appointments <label class="float-right bg-danger"> {{ score.todaycount | freeNumber }} </label> </h5>
                            </div>
                            <div class="col-6 text-uppercase">
                                <h5>Current Month Count  <label class="float-right bg-danger"> {{ score.curmonthcount | freeNumber }} </label> </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-12 m-b-20">
                <div class="card m-b-20">
                    <div class="card-header">
                        <h3 class="card-title">SCOREBOARD</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row p-3">
                            <div class="col-6 text-uppercase border-right">
                                <h5>Yesterday APPOINTMENTS <label class="float-right bg-danger"> {{ score.dayscore | freeNumber }}</label></h5>
                                <div class="text-center p-4">
                                    <span v-if="score.dayscore == 0">
                                        <img class="wf-175" :src="imgurl+'poor.png'">
                                        <p class="text-danger p-t-20">Oops! It seems you were not present yesterday.</p>
                                    </span>
                                    <span v-else-if="(score.dayscore >= 1) && (score.dayscore <= 8)">
                                        <img class="wf-175" :src="imgurl+'star.png'" >
                                        <p class="text-info p-t-20">Ohh! Next day is ahead.<br> You will definatly achieve your goal!!</p>
                                    </span>
                                    <span v-else-if="(score.dayscore >= 9) && (score.dayscore <= 12)">
                                        <img class="wf-175" :src="imgurl+'good.png'">
                                        <p class="text-primary p-t-20">Well! You are doing good !!</p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 12) && (score.dayscore <= 14)">
                                        <img class="wf-175" :src="imgurl+'excellent.png'">
                                        <p class="text-primary p-t-20">Wow! Excellent Work.</p>
                                    </span>
                                    <span v-else-if="(score.dayscore > 14)">
                                        <img class="wf-175" :src="imgurl+'goal.png'">
                                        <p class="text-success p-t-20">Wow! You are</p>
                                        <h4 class="text-uppercase font-weight-bold text-theme">Achiever of the Day</h4>
                                    </span>
                                    <span v-else>
                                        <img class="wf-175" :src="imgurl+'missing.png'">
                                        <p class="text-danger p-t-20">What! your score is missing !!</p>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 text-uppercase">
                                <h5>Previous Month APPOINTMENTS <label class="float-right bg-success"> {{ score.monthscore | freeNumber }}</label> </h5>
                                <div class="text-center p-4">
                                    <span v-if="(score.monthscore >= 400)">
                                        <img class="wf-175" :src="imgurl+'month-star.png'">
                                        <p class="text-success p-t-20">Wow! You are</p>
                                        <h4 class="text-uppercase font-weight-bold text-theme">Achiever of the MONTH</h4>
                                    </span>
                                    <span v-else-if="(score.monthscore > 1) && (score.monthscore < 400)">
                                        <img class="wf-175" :src="imgurl+'star.png'">
                                        <p class="text-primary p-t-20">Well! You did good last month.<br>Keep working.<br> You can still get <span class="text-uppercase">Achiever of This Month.</span>.</p>
                                    </span>
                                    <span v-else>
                                        <img class="wf-175" :src="imgurl+'missing.png'">
                                        <p class="text-danger p-t-20">What! your score is missing !!</p>
                                    </span>
                                </div>
                            </div>
                        </div>
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
                score: [],
                imgurl: 'images/',
                appointments:'',
                profile:'',
                user:'',
                clistSlots:[],
                listSlots:[],
            }
        },
        methods: {
            getAppointments() {
                axios.get('/api/get-dashboard-appointments').then(response => { this.appointments = response.data;  });

            },
            completeAppointment(apcode) {
                swal.fire('updating', 'Please Keep Patience, This action is being updated', 'info');
            },
            getProfile() {
                axios.get('/api/get-active-user').then(response => { this.user = response.data[0]; });
                axios.get('/api/getBothSlotsList').then((data) => {this.listSlots = data.data.starts; this.clistSlots = data.data.ends; }).catch();
            },
            getScore() {
                axios.get('/api/get-dr-score').then(response => { this.score = response.data;  });
            }
        },
        beforeMount() {
             this.getProfile();
        },
        mounted() {
            this.getScore();
            this.getAppointments();
         //   this.getResults();
         //   this.showProducts();
            //setInterval(() => { this.getResults()}, 3000);
        }
    }
</script>
