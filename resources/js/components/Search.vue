<template>
    <div>
        <div class="content view-page">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Make A Search</h3>
                    </div>
                    <div class="card-body content-box">
                        <form @submit.prevent="makeSearch" class=" border-bottom">
                            <div class="row m-0">
                                <div class="col-md-2 p-3">
                                    <label for="type" class="control-label">Type</label>
                                    <select v-model="form.type" name="type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                            <option disabled value="">Select Type</option>
                                            <option value="medicines">Medicine</option>
                                            <option value="treatments">Treatment</option>
                                        </select>
                                        <has-error :form="form" field="type"></has-error>
                                </div>
                                <div class="col-md-4 p-3" v-if="form.type == 'medicines'">
                                    <label for="search" class="control-label">Search</label>
                                    <input v-model="form.search" type="text" name="search" id="search" placeholder="enter medicine name, barcode" class="form-control" :class="{ 'is-invalid': form.errors.has('search') }">
                                    <has-error :form="form" field="search"></has-error>
                                </div>
                                <div class="col-md-4 p-3" v-else>
                                    <label for="search" class="control-label">Search</label>
                                    <input v-model="form.search" type="text" name="search" id="search" placeholder="enter treatment name" class="form-control" :class="{ 'is-invalid': form.errors.has('search') }">
                                    <has-error :form="form" field="search"></has-error>
                                </div>
                                <div class="col-md-4 p-3">
                                    <label for="submit" class="control-label">-</label><br>
                                    <input type="submit" class="btn btn-sm btn-primary" value="Search">
                                </div>
                            </div>
                        </form>
                        <div class="p-3" v-if="loading">
                            <img :src="svgurl+'loop.gif'">
                        </div>
                        <div v-else-if="checker == 'treatments'">
                            <table id="stock-alert" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th>Treatment Type</th>
                                        <th>Treatment</th>
                                        <th>Cost <small>( <b>{{ $root.$data.currency }}</b> )</small> </th>
                                        <th>Timing</th>
                                        <th>Dual</th>
                                        <th>Remark</th>
                                        <th style="width: 85px;">Added On</th>
                                        <th style="width: 50px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(treatment, ikey) in records" :key="treatment.id">
                                        <td>{{ ikey + 1 }}</td>
                                        <td>{{ treatment.appointment_type }}</td>
                                        <td>{{ treatment.treatment }}</td>
                                        <td>{{ treatment.cost | formatOMR }}</td>
                                        <td>{{ treatment.timing | freeNumber }} Mins</td>
                                        <td class="text-left" v-if="treatment.is_it_dual == 1">
                                            <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                        </td>
                                        <td align="center" v-else>
                                            <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                        </td>
                                        <td>{{ (treatment.remark)?treatment.remark:'--' }}</td>
                                        <td>{{ treatment.created_at | setdate }}</td>
                                        <td align="center" v-if="treatment.status_id == 2">
                                            <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                        </td>
                                        <td align="center" v-else>
                                            <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else-if="checker == 'medicines'">
                            <table id="medicine-list" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="wf-75">ID</th>
                                        <th>Category</th>
                                        <th>Medicine Name</th>
                                        <th>Unitsize / Packsize</th>
                                        <th>Medicine Barcode</th>
                                        <th>Price</th>
                                        <th>Added On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(medicine, pID) in records" :key="pID">
                                        <td>{{ pID + 1 }}</td>
                                        <td>{{ medicine.category_name }}</td>
                                        <td>{{ medicine.name }}</td>
                                        <td>{{ medicine.unitsize }} {{ medicine.unit }}</td>
                                        <td>{{ medicine.barcode }}</td>
                                        <td>{{ medicine.cash_price | formatOMR }}</td>
                                        <td>{{ medicine.created_at | setdate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3" v-else>
                            <p class="text-danger">No results to show.</p>
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
                bus: new Vue(),
                records:'',
                checker:'',
                svgurl: '/svg/',
                loading:false,
                form: new Form({
                    type:'',
                    search:'',
                })
            }
        },
        methods: {
            makeSearch() {
                this.loading = true;
                this.checker = '';
                this.form.post('/api/search/menu-search')
                    .then((data) => {
                        this.checker = this.form.type;
                        this.records = data.data;
                        this.loading = false;
                    })
                    .catch(() => {

                    });
            }
        },
        created() {

        }
    }
</script>
