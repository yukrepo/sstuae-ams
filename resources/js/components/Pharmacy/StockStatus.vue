<template>
    <div>
        <div class="content my-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Near Expiry Stocks
                                    <div class="card-tools">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <button class="btn btn-sm" v-on:click="changeDays(30)" :class="(activeDays == 30)?'btn-green-theme':'btn-green-theme-outline'">30 Days</button>
                                            <button class="btn btn-sm" v-on:click="changeDays(60)" :class="(activeDays == 60)?'btn-green-theme':'btn-green-theme-outline'">60 Days</button>
                                            <button class="btn btn-sm" v-on:click="changeDays(90)" :class="(activeDays == 90)?'btn-green-theme':'btn-green-theme-outline'">90 Days</button>
                                        </div>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-75">SNo</th>
                                            <th>Medicine</th>
                                            <th>Batch No</th>
                                            <th>Exp Date</th>
                                            <th>Total Stock</th>
                                            <th>Available</th>
                                            <th>Added On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(stock, count) in stocks" :key="count">
                                            <td>{{ count + 1 }}</td>
                                            <td>{{ stock.name }}</td>
                                            <td>{{ stock.batch_no }}</td>
                                            <td>{{ stock.exp_date | setdate }}</td>
                                            <td>{{ stock.received_stock | freeNumber }}</td>
                                            <td>{{ stock.stock | freeNumber }}</td>
                                            <td>{{ stock.created_at | setdate }}</td>
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
                stocks: {},
                activeDays:30,

                loader:false,
                loaderurl:'/svg/loop.gif',
            }
        },
        methods: {
            changeDays(val) {
                this.activeDays = val;
                this.showStock();
            },
            showStock() {
                this.$Progress.start();
                axios.get('/api/stockstatus/'+this.activeDays).then(({ data }) => { this.stocks = data;  this.$Progress.finish();  });
            },
        },
        mounted() {
            this.showStock();
        }
    }
</script>
