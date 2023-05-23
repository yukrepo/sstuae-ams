<template>
    <div>
        <div class="content-header">
            <div class="mb-1">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Full Reports</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="wf-50">SNo</th>
                                            <th>Module</th>
                                            <th class="w-50">Reports</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <th>Stocks</th>
                                            <td>
                                                <a target="_blank" href="/full-reports/stocks" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <a target="_blank" href="/full-reports/stocks" class="btn btn-sm btn-outline-dark">
                                                    <i class="fas fa-file-excel"></i> EXCEL Report
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <th>Medicines</th>
                                            <td>
                                                <a target="_blank" href="/full-reports/medicines" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <a target="_blank" href="/full-reports/medicines" class="btn btn-sm btn-outline-dark">
                                                    <i class="fas fa-file-excel"></i> EXCEL Report
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <th>Customers</th>
                                            <td>
                                                <a target="_blank" href="/full-reports/customers" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <a target="_blank" href="/full-reports/customers" class="btn btn-sm btn-outline-dark">
                                                    <i class="fas fa-file-excel"></i> EXCEL Report
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <th>Treatments</th>
                                            <td>
                                                <a target="_blank" href="/full-reports/treatments" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <a target="_blank" href="/full-reports/treatments" class="btn btn-sm btn-outline-dark">
                                                    <i class="fas fa-file-excel"></i> EXCEL Report
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <th>Insurances</th>
                                            <td>
                                                <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-dark">
                                                    <i class="fas fa-file-excel"></i> EXCEL Report
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <th>
                                                <select v-model="form.insurance_id" class="form-control">
                                                    <option disabled value="">Select Insurance</option>
                                                    <option :value="insurance.id" v-for="insurance in insurances" :key="insurance.id" v-show="insurance.id != 1">{{ insurance.name }}</option>
                                                </select>
                                            </th>
                                            <td>
                                                <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                    <i class="fas fa-file-pdf"></i> PDF Report
                                                </a>
                                                <button class="btn btn-sm btn-outline-dark" @click="getCoveredList">
                                                    <i class="fas fa-file-excel"></i> Covered Excel Report
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                insurances:'',
                form: new Form({
                    insurance_id:''
                })
            }
        },
        methods: {
            getAssets() {
                axios.get('/api/getInsurancersList').then(({ data }) => (this.insurances = data));
            },
            getCoveredList() {
                if(this.form.insurance_id){
                    window.open('/full-reports/covered-report/'+this.form.insurance_id, '_blank');
                } else {
                    swal.fire('Oops', 'Please select the Insurance Company', 'error');
                }
            }
        },
        mounted() {
            this.getAssets();
        }
    }
</script>
