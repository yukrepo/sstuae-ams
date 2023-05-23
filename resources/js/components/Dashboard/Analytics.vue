<template>
    <div class="p-t-1">
        <div class="search-box">
            <div class="row m-0 justify-content-md-center">
                <div class="col-md-4 col-12 pl-0">
                    <date-range-picker ref="picker" :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }" :timePicker24Hour="false" :showWeekNumbers="true" :showDropdowns="true"  :dateFormat="dateFormat" :autoApply="false" v-model="form.dateRange" @update="getAllScores">
                        <template v-slot:input="picker">
                            <span v-if="form.dateRange.startDate">
                                {{ picker.startDate | setdate }} - {{ picker.endDate | setdate }}
                            </span>
                            <span v-else>
                                Select Date Range
                            </span>
                        </template>
                    </date-range-picker>
                </div>
                <div class="col-md-2 col-12 p-0">
                    <button class="btn btn-sm btn-danger" type="button">RESET</button>
                </div>
            </div>
        </div>
        <div class="analytics-div">
            <div class="row m-0">
                <div class="col-md-8 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Analysis</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="ra_labels.length >= 1">
                            <v-frappe-chart type="axis-mixed" :labels="ra_labels" :height="340" :colors="['#38c172', '#5B2C6F', '#D35400', '#007bff', '#17202A', '#FFC106', '#e3342f']" :data="ra_data" :truncateLegends="true" :axisOptions="{xAxisMode : span, xIsSeries : true}" />
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Consultation Revenue Analysis</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="cra_labels.length >= 1">
                            <v-frappe-chart type="line"  :labels="cra_labels" :height="340" :data="cra_data" :lineOptions="{ regionFill: 1 }"  :truncateLegends="true" :axisOptions="{xAxisMode : span, xIsSeries : true}"  />
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Busy Business Hours</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="highTime.length">
                            <div class="single-line-show" v-for="(tdata, tkey) in highTime" :key="'t-'+tkey">
                                <label class="d-block"> {{ tdata.value }} 
                                    <span class="float-right">{{ tdata.percent }}</span>
                                </label>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"  :style="'width:'+tdata.percent" :aria-valuenow="tdata.percent" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Medicine Revenue Analysis</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="pra_labels.length >= 1">
                            <v-frappe-chart type="line"  :labels="pra_labels" :height="340" :data="pra_data" :lineOptions="{ regionFill: 1 }"  :truncateLegends="true" :axisOptions="{xIsSeries : true}"  />
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Sold Medicines</h3>
                        </div>
                        <div class="card-body table-responsive p-0" v-if="topMedicines.length">
                            <div class="single-line-show" v-for="(tmdata, tmkey) in topMedicines" :key="'tm-'+tmkey">
                                <label class="d-block"> {{ tmdata.value }} 
                                    <span class="float-right">{{ tmdata.percent }}</span>
                                </label>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"  :style="'width:'+tmdata.percent" :aria-valuenow="tmdata.percent" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lower Business Hours</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="lowTime.length">
                            <div class="single-line-show" v-for="(tdata, tkey) in lowTime" :key="'t-'+tkey">
                                <label class="d-block"> {{ tdata.value }} 
                                    <span class="float-right">{{ tdata.percent }}</span>
                                </label>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"  :style="'width:'+tdata.percent" :aria-valuenow="tdata.percent" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Treatment Revenue Analysis</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="tra_labels.length >= 1">
                            <v-frappe-chart type="line"  :labels="tra_labels" :height="340" :data="tra_data" :lineOptions="{ regionFill: 1 }"  :truncateLegends="true" :axisOptions="{xIsSeries : true}"  />
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 px-1 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Treatments</h3>
                        </div> 
                        <div class="card-body table-responsive p-0" v-if="topTreatments.length">
                            <div class="single-line-show" v-for="(tdata, tkey) in topTreatments" :key="'t-'+tkey">
                                <label class="d-block"> {{ tdata.value }} 
                                    <span class="float-right">{{ tdata.percent }}</span>
                                </label>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"  :style="'width:'+tdata.percent" :aria-valuenow="tdata.percent" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" v-else>
                            <div class="d-table w-100 h-100">
                                <div class="d-table-cell align-middle text-center">
                                    <div class="spinner-grow text-secondary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
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
    import DateRangePicker from 'vue2-daterange-picker';
    import { VFrappeChart } from 'vue-frappe-chart';
    export default {
        components: { DateRangePicker, VFrappeChart },
        data() {
            return {
                loader: false,
                loaderurl: '/images/spinner.gif',
                showpercent:false,
                topMedicines:[],
                topTreatments:[],
                ra_labels:[],
                ra_data:[],
                cra_labels:[],
                cra_data:[],
                pra_labels:[],
                pra_data:[],
                tra_labels:[],
                tra_data:[],
                lowTime:[],
                highTime:[],
                form: new Form({
                    dateRange : {},
                }),
            }
        },
        methods: {
            dateFormat(classes, date) {
                if(!classes.disabled) {
                }
                return classes
            },
            getAllScores() {
                if(this.form.dateRange.startDate) {
                    var endDate = JSON.stringify(this.form.dateRange.endDate).slice(1, -1)
                    var startDate = JSON.stringify(this.form.dateRange.startDate).slice(1, -1)
                    var ed = endDate.split("T")
                    var sd = startDate.split("T")
                    var sdate = sd[0]
                    var edate = ed[0]
                }else {
                    var today = new Date();
                    var dd = today.getDate();

                    var mm = today.getMonth()+1; 
                    var yyyy = today.getFullYear();

                    dd = dd < 10 ? '0'+ dd : dd;
                    mm = mm < 10 ?  '0'+ mm : mm;
                    
                    var edate = yyyy+'/'+mm+'/'+dd

                    var date = new Date();
                    var last = new Date(date.getTime() - (7 * 24 * 60 * 60 * 1000));
                    var ldd = last.getDate()
                    ldd = ldd < 10 ? '0' + ldd : ldd
                    var lmm = last.getMonth()+1
                    lmm = lmm < 10 ? '0' + lmm : lmm
                    var sdate = yyyy+'/'+lmm+'/'+ldd
                }
                let syear = sdate.substr(0,4);
                let eyear = edate.substr(0,4);
                if(syear != eyear) {
                     this.$toaster.error('Year of selected dates must be the same for analytics. Please change.');
                     return false;
                } else {
                    this.getTopMedicines(sdate, edate);
                    this.getTopTreatments(sdate, edate);
                    this.getRevenueAnalysis(sdate, edate);
                    this.getConsultRevenueAnalysis(sdate, edate);
                    this.getPharmacyRevenueAnalysis(sdate, edate);
                    this.getTreatmentRevenueAnalysis(sdate, edate);
                    this.getHighTime(sdate, edate);
                    this.getLowTime(sdate, edate);
                }
            },
            getTopMedicines(sdate, edate) {
                this.topMedicines = []
                axios.get('/api/analytics-top-medicines?s='+sdate+'&e='+edate).then((response) => { 
                    this.topMedicines = response.data
                }).catch((error) => { console.log(error) })
            },
            getTopTreatments(sdate, edate) {
                this.topTreatments = []
                axios.get('/api/analytics-top-treatments?s='+sdate+'&e='+edate).then((response) => { 
                    this.topTreatments = response.data
                }).catch((error) => { console.log(error) })
            },
            getHighTime(sdate, edate) {
                this.highTime = []
                axios.get('/api/high-business-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.highTime = response.data
                }).catch((error) => { console.log(error) })
            },
            getLowTime(sdate, edate) {
                this.lowTime = []
                axios.get('/api/low-business-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.lowTime = response.data
                }).catch((error) => { console.log(error) })
            },
            getRevenueAnalysis(sdate, edate) {
                this.ra_labels = []
                this.ra_data = []
                axios.get('/api/analytics-revenue-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.ra_labels = response.data.labels
                    this.ra_data = response.data.data
                }).catch((error) => { console.log(error) })
            },
            getConsultRevenueAnalysis(sdate, edate) {
                this.cra_labels = []
                this.cra_data = []
                axios.get('/api/consult-revenue-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.cra_labels = response.data.labels
                    this.cra_data = response.data.data
                }).catch((error) => { console.log(error) })
            },
            getPharmacyRevenueAnalysis(sdate, edate) {
                this.pra_labels = []
                this.pra_data = []
                axios.get('/api/pharmacy-revenue-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.pra_labels = response.data.labels
                    this.pra_data = response.data.data
                }).catch((error) => { console.log(error) })
            },
            getTreatmentRevenueAnalysis(sdate, edate) {
                this.tra_labels = []
                this.tra_data = []
                axios.get('/api/treatment-revenue-data?s='+sdate+'&e='+edate).then((response) => { 
                    this.tra_labels = response.data.labels
                    this.tra_data = response.data.data
                }).catch((error) => { console.log(error) })
            }
        },
        created() {
            this.getAllScores();
        }
    }
</script>
<style scoped>
.search-box{
    height: 40px;
}
</style>