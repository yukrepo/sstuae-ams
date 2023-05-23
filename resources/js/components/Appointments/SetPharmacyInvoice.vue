<template>
    <div>
        <div class="content-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                <li class="breadcrumb-item">Appointments</li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </div>
         <div class="content">
            <div class="container-fluid">
                <div class="fullbarmodal" id="setPharmacyInvocieModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="setPharmacyInvocieModalTitle" aria-hidden="true">
                    <div role="document">
                        <div class="modal-content">
                            <form @submit.prevent="editmode ? updatePharmacyInvoice() : createPharmacyInvoice()">
                                <div class="modal-header">
                                    <h5 class="modal-title" v-show="!editmode" id="createAppointModalTitle">Create Pharmacy Invoice</h5>
                                    <h5 class="modal-title" v-show="editmode" id="createAppointModalTitle">Update Pharmacy Invoice</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i class="fas fa-times text-white"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row border-bottom m-b-15 p-b-10 m-l-0 m-r-0 detailbox">
                                        <div class="col-12">
                                            <h5>MEDICINES</h5>
                                            <div class="alert alert-danger" v-show="catchmessage">
                                                {{ catchmessage }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-b-20">
                                        <div class="col-12 m-b-15">
                                            <span class="span-title"> Prescribed Medicines : </span>
                                            <span class="med-span" v-for="(medicin, mpkey) in JSON.parse(appointment.medicines)" :key="'mp-'+mpkey">{{ medicin.name }} ({{ medicin.qty }})</span>
                                        </div>
                                        <div class="col-8">
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                    <tr class="text-uppercase">
                                                        <th style="width: 50px;">SNo</th>
                                                        <th>Medicine</th>
                                                        <th>Batch No (Exp Date)</th>
                                                        <th style="width:110px">Stock</th>
                                                        <th style="width:110px">Cost</th>
                                                        <th style="width:110px">Quantity</th>
                                                        <th style="width:110px">Price</th>
                                                        <th style="width: 50px;"> #</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(counter, cID) in countlist" :key="cID" :id="'row'+cID">
                                                        <td>{{counter}}</td>
                                                        <td>
                                                            <input v-model="form.medicine__name[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['name']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__batch_no[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['batch_no']" readonly="readonly">
                                                        </td>
                                                         <td>
                                                            <input v-model="form.medicine__stock[getPhIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['stock']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__cost[getPhIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['cost']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__qty[getPhIndex(counter, cID)]" class="form-control" type="number" @change="rePhTotal" name="medicine[]['qty']">
                                                        </td>
                                                        <td>
                                                            <input v-model="form.medicine__total[getPhIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['total']" readonly="readonly">
                                                        </td>
                                                        <td>
                                                            <button type="button" @click="removeBar(cID)" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <td colspan="6" class="text-right">
                                                    <b> SUBTOTAL</b>
                                                    </td>
                                                    <td>
                                                        <input value="0" type="text" v-model="form.sub_total" name="sub_total" id="sub_total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('sub_total') }">
                                                    </td>
                                                    <td>
                                                        <button type="button" @click="rePhTotal" class="btn btn-secondary btn-sm"><i class="fa fa-sync"></i></button>
                                                    </td>
                                                </tfoot>
                                            </table>
                                            <hr>
                                            <div class="row">
                                                <div class="col-8">
                                                    <label class="control-label">Insurance Details</label><hr class="m-b-5 m-t-5">
                                                    <p>
                                                        <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="form.insured" @change="rePhTotal">
                                                        <b>{{ insurance.name }}</b> (Discount: {{ insurance.pharmacy_discount_percent }}%) - (Deduction: {{ insurance.pharmacy_deduction_percent }}%)</p-check>
                                                    </p>
                                                </div>
                                                <div class="col-4" v-show="options.bliss_hours == 1">
                                                    <label class="control-label">Offers by SST</label><hr class="m-b-5 m-t-5">
                                                    <p>
                                                        <p-check class="p-default p-curve p-fill check-label-resize" :true-value="1" v-model="form.offered" @change="rePhTotal">
                                                        <b>Bliss Hours Discount</b> -- (Discount:
                                                        <b v-if="options.bliss_hours_discount_type == 2">
                                                            {{ options.bliss_hours_discount }}%
                                                        </b>
                                                        <b v-else-if="options.bliss_hours_discount_type == 1">
                                                            {{ options.bliss_hours_discount }}
                                                        </b>
                                                        <b v-else>
                                                            0
                                                        </b>
                                                        )</p-check>
                                                    </p>
                                                </div>

                                            </div>
                                            <table class="table table-bordered m-b-0" v-show="form.offered == 1">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-right text-uppercase">Bliss Hours Discount</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.offered_value" name="offered_value" id="offered_value" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('offered_value') }"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered m-b-0" v-show="form.insured == 1">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-right text-uppercase">Insurance Discount</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.insured_discount" name="insured_discount" id="insured_discount" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('insured_discount') }"></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right text-uppercase">Amount Paid by Insurance Company</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.insured_deduction" name="insured_deduction" id="insured_deduction" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('insured_deduction') }"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-right text-uppercase">Total Payable</th>
                                                        <td style="width: 100px;"><input value="0" type="text" v-model="form.total" name="total" id="total" readonly="readonly" class="form-control" :class="{ 'is-invalid': form.errors.has('total') }"></td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="col-4">
                                            <div class="add-box">
                                                <form @submit.prevent="addMedicine()">
                                                    <div class="add-box-header">
                                                        <h5>Add Medicine</h5>
                                                    </div>
                                                    <div class="add-box-body">
                                                        <div class="form-group">
                                                            <label for="medicine_id" class="control-label">medicine</label>
                                                            <model-select :options="medicines"
                                                                        name="medicine_id"
                                                                        id="medicine_id"
                                                                        aria-required="true"
                                                                        v-model="nmform.medicine_id"
                                                                        placeholder="select medicine"
                                                                        @input="onSelect" >
                                                            </model-select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="stock_id" class="control-label">Batch no</label>
                                                            <model-select :options="stocks"
                                                                        name="stock_id"
                                                                        id="stock_id"
                                                                        aria-required="true"
                                                                        v-model="nmform.stock_id"
                                                                        placeholder="select batch no"  >
                                                            </model-select>
                                                        </div>
                                                    </div>
                                                    <div class="add-box-footer text-right">
                                                        <button type="submit" class="btn btn-sm btn-dark"> Add Now </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                                    <button v-show="!editmode" type="submit" class="btn btn-wide btn-success"> Submit </button>
                                    <button v-show="editmode" type="submit" class="btn btn-wide btn-success"> Update </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirmModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="confirmModalTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Add Medicine
                                </div>
                                <div class="modal-body text-center">
                                    <h4 class="m-b-20 m-t-20">Medicine has been added successfully.</h4>
                                    <button type="button" class="btn btn-lg btn-success m-b-20" @click="rePhTotal" data-dismiss="modal"> OK ! </button>
                                </div>
                                <div class="modal-footer text-center d-block">
                                    <i class="fas fa-check text-dark"></i>
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
import moment from 'moment';
    export default {
        data() {
            return {
                svgurl: '/svg/',
                docurl: '/files/docs/',
                editmode: false,
                catchmessage: '',
                currentYear: new Date().getFullYear(),
                activeID: this.$route.params.id,
                active_location: '',
                appointment:'',
                listSlots: [],
                customer: {},
                treatment:'',
                insurance:'',
                options:'',
                countlist:[],
                medicines:[],
                stocks:[],
                form: new Form({
                        medicine__qty:[],
                        medicine__batch_no:[],
                        medicine__stock_id:[],
                        medicine__cost:[],
                        medicine__stock:[],
                        medicine__name:[],
                        medicine__id:[],
                        medicine__total:[],
                        insured:'',
                        sub_total:0,
                        insured_deduction:0,
                        insured_discount:0,
                        offered:'',
                        offered_type:'',
                        offered_discount:0,
                        offered_value:0,
                        total:0
                }),
                aform: new Form({
                        insured:'',
                        sub_total:'',
                        insured_deduction:0,
                        insured_discount:0,
                        offered:'',
                        offered_type:'',
                        offered_discount:'',
                        offered_value:0,
                        total:''
                }),
                nmform: new Form({
                    medicine_id:'',
                    stock_id:''
                })
            }
        },
        methods: {
            getIndex(list, id) {
                return id;
            },
            getPhIndex(list, id) {
                return list;
            },
            getAge(date) {
                let today = new Date();
                let ndate = new Date(date);
                var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
                var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
                var diffDuration = moment.duration(a.diff(b));
                return '('+diffDuration.years()+' yrs)';
            },
            viewCustomer(id) {
                $('#userModal').modal('show');
            },
            addBar(){
                let bcount = this.countlist.length;
                this.countlist.push(bcount+1);
            },
            removeBar(barkey){
               this.$delete(this.countlist, barkey);
               this.rePhTotal();
            },
            getAllAssets() {
                axios.get('/api/getOeCategoriesSelectList').then((data) => {this.oecategories = data.data }).catch();
                axios.get('/api/getDiseasesSelectList').then((data) => {this.diseases = data.data }).catch();
                axios.get('/api/getTreatmentsSelectList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getSymptomsSelectList').then((data) => {this.symptoms = data.data }).catch();
                axios.get('/api/getMedicinesSelectList').then((data) => {this.medicines = data.data }).catch();
                axios.get('/api/getXTestsSelectList').then((data) => { this.xtests = data.data }).catch();
                axios.get('/api/getDTestsSelectList').then((data) => { this.dtests = data.data }).catch();
            },
            showAppointment() {
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
                this.$Progress.start();
                axios.get('/api/appointments/view/'+this.$route.params.id).then(({ data }) => {
                    this.appointment = data[0];
                    let appdata =  data[0];
                    axios.get('/api/customers/'+this.appointment.user_id).then((data) => {
                        this.customer = data.data[0];
                        axios.get('/api/insurances/'+data.data[0].insurance_id).then((data) => {this.insurance = data.data}).catch();
                    }).catch();
                    axios.get('/api/treatments/'+this.appointment.treatment_id).then((data) => {this.treatment = data.data}).catch();
                    this.$Progress.finish();

                    let presmed = JSON.parse(this.appointment.medicines);
                    presmed.forEach((element, key) => {
                        let pkey = key + 1;
                        this.countlist.push(pkey);
                        axios.get('/api/stocks/get-expiring-by-medicine/'+element.id)
                            .then((edata) => {
                                this.form.medicine__qty[pkey] = (element.qty < edata.data.stock)?element.qty:edata.data.stock;
                                this.form.medicine__batch_no[pkey] = edata.data.batch_no+' ('+ moment(edata.data.exp_date).format('DD.MM.YYYY')+')';
                                this.form.medicine__stock_id[pkey] = edata.data.id;
                                this.form.medicine__cost[pkey] = edata.data.selling_cost;
                                this.form.medicine__stock[pkey] = edata.data.stock;
                                this.form.medicine__name[pkey] = element.name;
                                this.form.medicine__id[pkey] = element.id;
                            });
                    });
                });
                axios.get('/api/getOptionsList').then(({ data }) => {
                    this.options = data;
                    this.$Progress.finish();
                });
                this.rePhTotal();
            },
            getAppointmentHistory() {
                this.$Progress.start();
                axios.get('/api/appointments/user-history/'+this.$route.params.id).then(({ data }) => {
                    //console.log(data);
                    this.appointment_histories = data;
                    this.$Progress.finish();
                });
            },
            reTotal(){
                this.addInsurance(); this.addOffers();
                this.aform.sub_total = this.treatment.cost;
                let total = this.makeNumber(this.aform.sub_total) - this.makeNumber(this.aform.insured_discount) - this.makeNumber(this.aform.insured_deduction) - this.makeNumber(this.aform.offered_value);
                this.aform.total = this.makeNumber(total);
            },
            addInsurance() {
                if(this.aform.insured == 1){
                    if(this.appointment.appointment_type_id == 1) {
                        this.aform.insured_discount = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.consult_discount_percent)/100);
                        this.aform.insured_deduction = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.consult_deduction_percent)/100);
                    }
                    else if(this.appointment.appointment_type_id == 2) {
                        this.aform.insured_discount = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.treatment_discount_percent)/100);
                        this.aform.insured_deduction = this.makeNumber(this.makeNumber(this.aform.sub_total)*this.makeNumber(this.insurance.treatment_deduction_percent)/100);
                    }
                    else {
                        this.aform.insured_discount = 0;
                        this.aform.insured_deduction = 0;
                    }
                } else {
                    this.aform.insured_discount = 0;
                    this.aform.insured_deduction = 0;
                }
            },
            addMedicine() {
                if((this.nmform.medicine_id == '') || (this.nmform.stock_id == '')){
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Please select the medicine and batch no.',
                        timer: 2500
                    });
                    return false;
                }
                let bcount = this.countlist.length;
                let pkey = bcount + 1;

                this.form.medicine__stock_id[pkey] = this.nmform.stock_id;
                this.form.medicine__id[pkey] = this.nmform.medicine_id;
                axios.get('/api/stocks/stock-by-id/'+this.nmform.stock_id)
                .then((edata) => {
                    this.form.medicine__qty[pkey] = 1;
                    this.form.medicine__batch_no[pkey] = edata.data.batch_no+' ('+ moment(edata.data.exp_date).format('DD.MM.YYYY')+')';
                    this.form.medicine__cost[pkey] = edata.data.selling_cost;
                    this.form.medicine__stock[pkey] = edata.data.stock;
                    this.form.medicine__name[pkey] = edata.data.name;
                });
                this.nmform.stock_id = '';
                this.nmform.medicine__id = '';
                this.countlist.push(pkey);
                $('#confirmModal').modal('show');
                this.rePhTotal();
            },
            addOffers() {
                let offer = '';
                if(this.aform.offered == 1){
                    if(this.options.bliss_hours == 1) {
                        if(this.options.bliss_hours_discount_type == 1) {
                            offer = this.makeNumber(this.options.bliss_hours_discount);
                        } else if(this.options.bliss_hours_discount_type == 2) {
                            offer =  this.makeNumber(this.makeNumber(this.treatment.cost)*this.makeNumber(this.options.bliss_hours_discount)/100);
                        } else {
                            offer = 0;
                        }
                        this.aform.offered_value = offer;
                    } else {
                        this.aform.offered_value = 0;
                    }
                } else {
                    this.aform.offered_value = 0;
                }
            },
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            onSelect (product) {
               axios.get('/api/stocks/get-list-by-medicine/'+product)
                    .then((data) => {
                        if(data.data){
                            this.stocks = data.data;
                            //this.rePhTotal();
                        }
                    });
            },
            rePhTotal(){
                this.updateSubTotal(); this.addPhInsurance(); this.addPhOffers();
                let total = this.makeNumber(this.form.sub_total) - this.makeNumber(this.form.insured_discount) - this.makeNumber(this.form.insured_deduction) - this.makeNumber(this.form.offered_value);
                this.form.total = this.makeNumber(total);
            },
            addPhInsurance() {
                if(this.form.insured == 1){
                    this.form.insured_discount = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.insurance.pharmacy_discount_percent)/100);
                    this.form.insured_deduction = this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.insurance.pharmacy_deduction_percent)/100);
                } else {
                    this.form.insured_discount = 0;
                    this.form.insured_deduction = 0;
                }
            },
            addPhOffers() {
                let offer = '';
                if(this.form.offered == 1){
                    if(this.options.bliss_hours == 1) {
                        if(this.options.bliss_hours_discount_type == 1) {
                            offer = this.makeNumber(this.options.bliss_hours_discount);
                        } else if(this.options.bliss_hours_discount_type == 2) {
                            offer =  this.makeNumber(this.makeNumber(this.form.sub_total)*this.makeNumber(this.options.bliss_hours_discount)/100);
                        } else {
                            offer = 0;
                        }
                        this.form.offered_value = offer;
                    } else {
                        this.form.offered_value = 0;
                    }
                } else {
                    this.form.offered_value = 0;
                }
            },
            updateSubTotal() {
                let counter = this.countlist;
                let stotal = 0; let total = 0;
                counter.forEach(element => {
                    this.form.medicine__total[element] = this.makeNumber(this.form.medicine__cost[element]) * this.makeNumber(this.form.medicine__qty[element]);
                    stotal = stotal +  this.form.medicine__total[element];
                });
                this.form.sub_total = stotal;
            },
            setPharmacyInvoice() {
                 $('#setPharmacyInvocieModal').modal('show');
            },
            setInvoice() {
                this.reTotal();
                $('#setInvocieModal').modal('show');
            },
            getPrimaryAssets() {
               /* let presmed = JSON.parse(this.appointment.medicines);
               presmed.forEach((element, key) => {
                   console.log(element+'-'+key);
               }); */
               //console.log(this.appointment);
                //axios.get('/api/getStocksSelectList').then(({ data }) => { this.medicines = data; });
            },
            createCourse() {
                swal.fire({
                    title: 'Create Course',
                    text: 'Are you sure, you want to create a course respective to thie apppointment?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#151E5F',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Create It Now',
                    cancelButtonText: 'No',
                    showLoaderOnConfirm: true,
                }).then((result) => {
                    if (result.value) {
                        axios.get('/api/courses/create-course/'+this.activeID)
                        .then((data) => {
                            axios.get('/api/appointments/add-course?aid='+this.activeID+'&cid='+data.data.course_code)
                            .then((vresponse) => {
                                this.showAppointment();
                            });
                            swal.fire({
                                title: 'Course Created',
                                text: "Do you want to view course page ?",
                                type: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#151E5F',
                                cancelButtonColor: '#06A23F',
                                confirmButtonText: 'Go To Course Page',
                                cancelButtonText: 'Stay On This Page'
                            }).then((vresult) => {
                                this.$route.push('/appointments/courses/'+data.data.course_code);
                            });
                        }).catch(() => {
                            swal.file({
                                type: 'error',
                                title: 'Unable to process request'
                            })
                        })
                    }
                });
            }

        },
        created() {
            this.getAllAssets();
            this.showAppointment();
            this.getPrimaryAssets();
            //this.getAppointmentHistory();

            //this.defineSlots();
        }

    }
</script>
