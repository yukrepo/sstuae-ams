<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Financial Year
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addYear"><i class="fas fa-plus fa-fw"></i> Increase Financial Year</button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Year</th>
                                            <th>Remark</th>
                                            <th>Appointments</th>
                                            <th>Finances</th>
                                            <th style="width: 100px;">Added On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="financial_year in financial_years.data" :key="financial_year.id">
                                            <td>{{ financial_year.id }}</td>
                                            <td>{{ financial_year.year }}</td>
                                            <td>{{ (financial_year.remark)?financial_year.remark:'--' }}</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>{{ financial_year.created_at | setdate }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="financial_years.total > 1">
                                <pagination class="m-0 float-right" :data="financial_years" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
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
                financial_years: {},
                isLoading: false,
                fullPage: true,
                form: new Form({
                    id:'',
                    year:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/financial-years?page=' + page)
                    .then(response => {
                        this.financial_years = response.data;
                    });
            },
            showFinancialYears() {
                this.$Progress.start();
                axios.get('/api/financial-years').then(({ data }) => {
                    this.financial_years = data;
                    this.$Progress.finish();
                });
            },
            updateFormYear() {
                axios.get('/api/get-last-financial-year').then(({ data }) => ( this.form.year = data.year + 1 ));
            },
            addYear() {
                let loader = this.$loading.show();
                this.form.post('/api/financial-years')
                .then(({data}) => {
                    Fire.$emit('AfterCreate');
                    toast.fire({
                        type:'success',
                        title:'Financial year has been created successfully.'
                    });
                    loader.hide();
                })
                .catch((data) => {
                    swal.fire({
                        type:'error',
                        title:'Error in creating Financial year',
                        text: 'Please try again later.'
                    });
                    loader.hide();
                });

            }
        },
        created() {
            this.showFinancialYears();
            this.updateFormYear();
            Fire.$on('AfterCreate', () => {
                this.showFinancialYears();
                this.updateFormYear();
            });
        }
    }
</script>
