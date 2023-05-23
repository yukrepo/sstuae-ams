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
                                <p><i class="fa fa-calendar mr-1"></i> Consultation Report</p>
                                <form @submit.prevent="fetchM1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapists Report</p>
                                <form @submit.prevent="fetchMP1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MP1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
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
                                <p><i class="fa fa-calendar mr-1"></i> Consultation Report</p>
                                <form @submit.prevent="fetchM2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapists Report</p>
                                <form @submit.prevent="fetchMP2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MP2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
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
                                <p><i class="fa fa-calendar mr-1"></i> Consultation Report</p>
                                <form @submit.prevent="fetchM3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Therapists Report</p>
                                <form @submit.prevent="fetchMP3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MP3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
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
                MP1form: new Form({
                    date:'',
                }),
                M2form: new Form({
                    date:'',
                }),
                MP2form: new Form({
                    date:'',
                }),
                M3form: new Form({
                    date:'',
                }),
                MP3form: new Form({
                    date:'',
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
                    window.open('/reports/consultation-ids-day-report/'+adate, '_blank');
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
                    window.open('/reports/consultation-ids-month-report/'+adate, '_blank');
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
                    window.open('/reports/consultation-ids-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMP1Report(){
                if(this.MP1form.date){
                    let activeFullDate = new Date(this.MP1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-drbased-day-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMP2Report(){
                if(this.MP2form.date){
                    let activeFullDate = new Date(this.MP2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/treatment-drbased-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMP3Report(){
                if(this.MP3form.date){
                    let res = this.MP3form.date;
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
        },
        mounted() {
            this.getAssets();
        }
    }
</script>
