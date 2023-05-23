<template>
    <div>
        <div class="content">
            <div class="container-fluid report-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Day Reports</h3>
                            </div>
                            <div class="card-body">
                                <p><i class="fa fa-calendar mr-1"></i> Invoice Day Report</p>
                                <form @submit.prevent="fetchM1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Invoice Complete Report</p>
                                <form @submit.prevent="fetchMC1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MC1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p v-show="profile.admin_type_id != 7"><i class="fa fa-calendar mr-1"></i> Insurance cumulative Report</p>
                                <form @submit.prevent="fetchMID3Report()" class="form-inline" v-show="profile.admin_type_id != 7">
                                    <div class="form-group">
                                        <select v-model="MID3form.insurance_id" class="form-control" multiple style="height:160px;">
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
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Month Reports</h3>
                            </div>
                            <div class="card-body">
                                <p><i class="fa fa-calendar mr-1"></i> Invoice Month Report</p>
                                <form @submit.prevent="fetchM2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M2form.date" type="month" lang="en"  format="YYYY-MM" placeholder="select month"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Invoice Complete Month Report</p>
                                <form @submit.prevent="fetchMC2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="MC2form.date" type="month" lang="en"  format="YYYY-MM" placeholder="select month"></date-picker>
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
                insurances: {},
                profile:'',
                M1form: new Form({
                    date:'',
                }),
                MC1form: new Form({
                    date:'',
                }),
                M2form: new Form({
                    date:'',
                }),
                MC2form: new Form({
                    date:'',
                }),
                MID3form: new Form({
                    date:'',
                    insurance_id:[]
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
                    window.open('/reports/invoice-report/'+adate, '_blank');
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
                    window.open('/reports/invoice-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMC1Report(){
                if(this.MC1form.date){
                    let activeFullDate = new Date(this.MC1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/invoice-complete-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMC2Report(){
                if(this.MC2form.date){
                    let activeFullDate = new Date(this.MC2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/invoice-complete-month-report/'+adate, '_blank');
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
                    if(sddate.getFullYear() != eddate.getFullYear()) {
                        swal.fire({type: 'error',  title: 'Both dates must be from same year.'});
                        return false;
                    }
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/invoice-cumulative-report/'+this.MID3form.insurance_id+'/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
        },
        created() {
            axios.get('/api/get-active-user').then(response => {this.profile = response.data;});
           this.getAssets();
        }
    }
</script>
