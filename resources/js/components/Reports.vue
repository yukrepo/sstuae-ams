<template>
    <div>
        <div class="content">
            <div class="search-box">
                <form @submit.prevent="makeSearch">
                    <div class="row">
                        <div class="col-md-2">
                            <date-picker v-model="form.date" range lang="en" format="YYYY-MM-DD" confirm></date-picker>
                        </div>
                        <div class="col-md-3">
                            <select v-model="form.report_type" class="form-control">
                                <option disabled value="">Select Report Type</option>
                                <option :value="reportType.id" v-for="reportType in assignedReports" :key="reportType.id">
                                    {{ reportType.type +' - '+reportType.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <span v-if="active_report.other_enabled == 1">
                                <select v-model="form.other_data" class="form-control">
                                    <option disabled value="">Select Insurance</option>
                                    <option :value="insurance.id" v-for="insurance in insurances" :key="insurance.id" v-show="insurance.id != 1">{{ insurance.name }}</option>
                                </select>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <img class="img-icon mr-3" style="height:20px" :src="loadingurl" v-if="loader">
                            <input type="submit" class="btn btn-sm btn-primary" value="Get Report" v-else>
                            <button class="btn btn-sm btn-dark" @click="form.reset()" type="button">Reset</button>
                        </div>
                        <div class="col-md-3 text-right">
                            <b v-if="filename" class="mr-2 text-uppercase mt-1"> {{ records.length }} rows found</b>
                            <img class="img-icon" :src="loadingurl" v-if="loader == 2">
                            <json-excel class="btn btn-sm btn-success" v-else-if="records.length >= 1"  :data="records" :fields="json_fields" worksheet="Report" :name="filename" :before-generate="startDownload" :before-finish="finishDownload"> 
                                <span v-if="downloadprogres">
                                    <i class="spinner-border-sm spinner-border"></i>
                                    Downloading...
                                </span>
                                <span v-else>
                                    <i class="fa fa-arrow-alt-circle-down m-r-5"></i> EXCEL 
                                </span>
                            </json-excel>
                            <button class="btn btn-sm btn-success" type="button" v-else @click="exportEXCEL"> <i class="fa fa-arrow-alt-circle-down m-r-5"></i> EXCEL</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container-fluid report-full-data" v-if="filename">
                <table class="table-bordered table-striped table-condensed table">
                    <thead>
                        <tr>
                            <th v-for="(jfields, jkey) in json_fields" :key="'th'+jfields">
                                {{ jkey }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(tdvalue, tdkey) in records" :key="'td'+tdkey">
                            <td v-for="j2fields in json_fields" :key="'th2'+j2fields">
                                {{ tdvalue[j2fields] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>
               <div class="row m-0">
                    <div class="col-md-12">
                        <p style="border-radius:0; font-size:13px" class="m-2 alert alert-warning"> Please choose date range and report type.</p>
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
                downloadprogres: false,
                loader: false,
                loadingurl:'/images/spinner.gif',
                insurances:[],
                report_types:[],
                json_fields:[],
                records:[],
                filename:'',
                profile:[],
                form: new Form({
                    date:'',
                    report_type:'',
                    other_data:'',
                    other_type:''
                }),
            }
        },
        computed: {
            assignedReports() {
                if(this.profile.admin_type_id == 1) {
                    return this.report_types;
                } else {
                    let report = (this.profile.reports)?this.profile.reports.split(',').map(function(item) {
                                    return parseInt(item, 10);
                                }):[];
                    return this.report_types.filter((item) => {
                        return (report.indexOf(item.id) >= 0)
                    })
                }
            },
            active_report() {
                if(this.form.report_type) {
                    let selected_report = this.report_types.filter((item) => {
                        return this.form.report_type == item.id
                    })
                    return selected_report[0];
                } else {
                    return [];
                }
                
            },
        },
        methods: {
            startDownload() {
                this.downloadprogres = true;
            },
            finishDownload() {
                this.downloadprogres = false;
            },
            getAssets() {
                axios.get('/api/getReports').then(({ data }) => (this.report_types = data));
                axios.get('/api/getInsurancersList').then(({ data }) => (this.insurances = data));
            },
            makeSearch() {
                this.loader = 1;
                let check = 0;
                if(this.form.date == '') {
                    this.$toaster.error('Please select date range.');
                    check++;
                }
                if(this.form.report_type == '') {
                    this.$toaster.error('Please select report type.');
                    check++;
                }
                if(this.active_report.other_enabled == 1 && this.form.other_data == '') {
                    this.$toaster.error('Please select insurance name.');
                    check++;
                }
                if(check >= 1) {
                    this.loader = false;
                    return false;
                }
                this.form.post('/api/get-reports-data').then(({ data }) => {
                    this.records = data.records;
                    this.json_fields = data.json_fields;
                    this.filename = data.filename;
                    this.loader = false;
                }).catch((err) => {
                    this.loader = false;
                    console.log(err);
                    this.$toaster.error('Some Error occured. You can see it in console');
                });
            },
            exportEXCEL(){
                if(this.records.length >= 1) {
                    this.loader = 2;
                } else {
                    swal.fire('404 !', 'No records found to export.', 'info');
                }
            },
            
        },
        beforeMount() {
            axios.get('/api/get-active-user').then(response => { this.profile = response.data; });
        },
        created() {
            this.getAssets();
        }
    }
</script>