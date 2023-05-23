<template>
    <div>
        <div class="content">
            <div class="container-fluid report-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Reports</h3>
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
                M1form: new Form({
                    date:'',
                }),
            }
        },
        methods: {
            getAssets() {
               // axios.get('/api/getInsurancersList').then(({ data }) => (this.insurances = data));
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
        },
        created() {
           this.getAssets();
        }
    }
</script>
