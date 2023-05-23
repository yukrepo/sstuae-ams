<template>
    <div>
        <div class="content">
            <div class="container-fluid report-data">
               <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Daily Reports</h3>
                            </div>
                            <div class="card-body">
                                <p><i class="fa fa-calendar mr-1"></i> Overall Report</p>
                                <form @submit.prevent="fetchM1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapist Based Report</p>
                                <form @submit.prevent="fetchMT1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MT1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Based Report</p>
                                <form @submit.prevent="fetchMI1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MI1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Detailed Report</p>
                                <form @submit.prevent="fetchMID1Report()" class="form-inline">
                                    <div class="form-group">
                                        <select v-model="MID1form.insurance_id" class="form-control">
                                            <option disabled value="">Select Insurance</option>
                                            <option :value="insurance.id" v-for="insurance in insurances" :key="insurance.id" v-show="insurance.id != 1">{{ insurance.name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <date-picker v-model="MID1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Monthly Reports</h3>
                            </div>
                            <div class="card-body">
                                <p><i class="fa fa-calendar mr-1"></i> Overall Report</p>
                                <form @submit.prevent="fetchM2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapist Based Report</p>
                                <form @submit.prevent="fetchMT2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MT2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Based Report</p>
                                <form @submit.prevent="fetchMI2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MI2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Detailed Report</p>
                                <form @submit.prevent="fetchMID2Report()" class="form-inline">
                                    <div class="form-group">
                                        <select v-model="MID2form.insurance_id" class="form-control">
                                            <option disabled value="">Select Insurance</option>
                                             <option :value="insurance.id" v-for="insurance in insurances" :key="insurance.id" v-show="insurance.id != 1">{{ insurance.name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <date-picker v-model="MID2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Day Range Reports</h3>
                            </div>
                            <div class="card-body">
                                 <p><i class="fa fa-calendar mr-1"></i> Overall Report</p>
                                <form @submit.prevent="fetchM3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapist Based Report</p>
                                <form @submit.prevent="fetchMT3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MT3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Based Report</p>
                                <form @submit.prevent="fetchMI3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MI3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Insurance Detailed Report</p>
                                <form @submit.prevent="fetchMID3Report()" class="form-inline">
                                    <div class="form-group">
                                        <select v-model="MID3form.insurance_id" class="form-control">
                                            <option disabled value="">Select Insurance</option>
                                            <option :value="insurance.id" v-for="insurance in insurances" :key="insurance.id" v-show="insurance.id != 1">{{ insurance.name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <date-picker v-model="MID3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
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
                isLoading: false,
                insurances:{},
                M1form: new Form({
                    date:'',
                }),
                MT1form: new Form({
                    date:'',
                }),
                MI1form: new Form({
                    date:'',
                }),
                MID1form: new Form({
                    date:'',
                    insurance_id:''
                }),
                M2form: new Form({
                    date:'',
                }),
                MT2form: new Form({
                    date:'',
                }),
                MI2form: new Form({
                    date:'',
                }),
                MID2form: new Form({
                    date:'',
                    insurance_id:''
                }),
                M3form: new Form({
                    date:'',
                }),
                MT3form: new Form({
                    date:'',
                }),
                MI3form: new Form({
                    date:'',
                }),
                MID3form: new Form({
                    date:'',
                    insurance_id:''
                }),
            }
        },
        methods: {
            getAssets() {
                axios.get('/api/getInsurancersList').then(({ data }) => (this.insurances = data));
            },
            fetchM1Report(){
                if(this.M1form.date){
                    let activeFullDate = new Date(this.M1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-overall-day-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchM2Report(){
                if(this.M2form.date){
                    let activeFullDate = new Date(this.M2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-overall-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchM3Report(){
                if(this.M3form.date){
                    let res = this.M3form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/treatment-overall-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMT1Report(){
                if(this.MT1form.date){
                    let activeFullDate = new Date(this.MT1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-drbased-day-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMT2Report(){
                if(this.MT2form.date){
                    let activeFullDate = new Date(this.MT2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-drbased-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMT3Report(){
                if(this.MT3form.date){
                    let res = this.MT3form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/treatment-drbased-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMI1Report(){
                if(this.MI1form.date){
                    let activeFullDate = new Date(this.MI1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-ins-day-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMI2Report(){
                if(this.MI2form.date){
                    let activeFullDate = new Date(this.MI2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-ins-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMI3Report(){
                if(this.MI3form.date){
                    let res = this.MI3form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/treatment-ins-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMID1Report(){
                if(this.MID1form.date){
                    let activeFullDate = new Date(this.MID1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-insd-day-report/'+this.MID1form.insurance_id+'/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMID2Report(){
                if(this.MID2form.date){
                    let activeFullDate = new Date(this.MID2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-insd-month-report/'+this.MID2form.insurance_id+'/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMID3Report(){
                if(this.MID3form.date){
                    let res = this.MID3form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/treatment-insd-custom-report/'+this.MID3form.insurance_id+'/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
        },
        mounted() {
            this.getAssets();
        }
    }
</script>
