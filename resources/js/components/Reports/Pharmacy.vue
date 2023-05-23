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
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Stock Report</p>
                                <form @submit.prevent="fetchM1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Sales Report</p>
                                <form @submit.prevent="fetchMs1Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="Ms1form.date" lang="en" format="YYYY-MM-DD"></date-picker>
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
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Stock Report</p>
                                <form @submit.prevent="fetchM2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Sales Report</p>
                                <form @submit.prevent="fetchMs2Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="Ms2form.date" type="month" lang="en" format="YYYY-MM"></date-picker>
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
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Stock Report</p>
                                <form @submit.prevent="fetchM3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="M3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Medicine Sales Report</p>
                                <form @submit.prevent="fetchMs3Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="Ms3form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Doctor Based Medicine Report</p>
                                <form @submit.prevent="fetchMdReport()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="Mdform.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Get Report</button>
                                    </div>
                                </form>
                                <hr>
                                <p><i class="fa fa-calendar mr-1"></i> Direct Medicine Sales Report</p>
                                <form @submit.prevent="fetchMs4Report()" class="form-inline">
                                    <div class="form-group">
                                        <date-picker v-model="Ms4form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
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
                M1form: new Form({
                    date:'',
                }),
                M2form: new Form({
                    date:'',
                }),
                M3form: new Form({
                    date:'',
                }),
                Ms1form: new Form({
                    date:'',
                }),
                Ms2form: new Form({
                    date:'',
                }),
                Ms3form: new Form({
                    date:'',
                }),
                Ms4form: new Form({
                    date:'',
                }),
                Mdform: new Form({
                    date:'',
                }),
            }
        },
        methods: {
            fetchM1Report(){
                if(this.M1form.date){
                    let activeFullDate = new Date(this.M1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/medicine-stock-day-report/'+adate, '_blank');
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
                    window.open('/reports/medicine-stock-month-report/'+adate, '_blank');
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
                    window.open('/reports/medicine-stock-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMs1Report(){
                if(this.Ms1form.date){
                    let activeFullDate = new Date(this.Ms1form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/medicine-sales-day-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date',
                        });
                }
            },
            fetchMs2Report(){
                if(this.Ms2form.date){
                    let activeFullDate = new Date(this.Ms2form.date);
                    let adate = activeFullDate.getFullYear() +'-'+ ("0" + parseInt(activeFullDate.getMonth()+1)).slice(-2) +'-'+ activeFullDate.getDate();
                    window.open('/reports/medicine-sales-month-report/'+adate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the month',
                        });
                }
            },
            fetchMs3Report(){
                if(this.Ms3form.date){
                    let res = this.Ms3form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/medicine-sales-custom-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMs4Report(){
                if(this.Ms4form.date){
                    let res = this.Ms4form.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/direct-medicine-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
            fetchMdReport(){
                if(this.Mdform.date){
                    let res = this.Mdform.date;
                    let sddate = new Date(res[0]);
                    let eddate = new Date(res[1]);
                    let adate = sddate.getFullYear() +'-'+ ("0" + parseInt(sddate.getMonth()+1)).slice(-2) +'-'+ sddate.getDate();
                    let edate = eddate.getFullYear() +'-'+ ("0" + parseInt(eddate.getMonth()+1)).slice(-2) +'-'+ eddate.getDate();
                    window.open('/reports/drbased-medicine-report/'+adate+'/'+edate, '_blank');
                } else {
                    swal.fire({
                            type: 'error',
                            title: 'Please select the date range',
                        });
                }
            },
        },
        created() {

        }
    }
</script>
