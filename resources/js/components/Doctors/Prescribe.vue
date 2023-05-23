<template>
    <div>
         <div class="content full-white-page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form name="prescription-form">
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Consultation AND PRESCRIPTION</h3>
                                </div>
                                <div class="full-form-body">
                                    <div class="row detailbox">
                                        <div class="col-md-3">
                                            <h5>Appointment Details</h5>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th>ID</th>
                                                            <td>{{ appointment.appointment_code }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Visit Type</th>
                                                            <td>{{ appointment.visit_type }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Consultation</th>
                                                            <td>{{ appointment.reason }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date</th>
                                                            <td>{{ appointment.date | setdate }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Time</th>
                                                            <td>{{ listSlots[appointment.start_timeslot]+' - '+listSlots[appointment.end_timeslot] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h5>Patient Details</h5>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th>ID</th>
                                                            <td>{{ appointment.username }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Name</th>
                                                            <td>{{ appointment.first_name }} {{ appointment.last_name }} ({{ appointment.mobile }})</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Gender</th>
                                                            <td>
                                                                <span v-if="appointment.gender == 1">
                                                                    Male ({{ appointment.married | capitalize }})
                                                                </span>
                                                                <span v-else-if="appointment.gender == 2">
                                                                    Female ({{ appointment.married | capitalize }})
                                                                </span>
                                                                <span v-else>
                                                                    Unknown ({{ appointment.married | capitalize }})
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>D.O.B. (Age)</th>
                                                            <td>{{ appointment.date_of_birth | setdate }} {{ getAge(appointment.date_of_birth) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Insurance
                                                                <span class="label badge-success p-t-2 p-l-5 p-r-5 p-b-2" v-if="appointment.insurance_verified == 1"> <i class="fas fa-check"></i> </span>
                                                                <span class="label p-t-2 p-l-5 p-r-5 p-b-2 badge-danger" v-else> <i class="fas fa-times"></i> </span>
                                                            </th>
                                                            <td>{{ appointment.insurancer }} ({{ appointment.policy_number | uppercase }})
                                                                <img class="btn-img" v-img:docurl+appointment.insurance_copy :src="this.docurl+appointment.insurance_copy">

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Patient History</h5>
                                            <div class="h-fix">
                                                <div>
                                                    <ul class="nav nav-pills" id="pills-tab-desc" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="pills-dstab-1" data-toggle="pill" href="#pills-dstab1" role="tab" aria-controls="pills-dstab1" aria-selected="true">Symptoms</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-8" data-toggle="pill" href="#pills-dstab8" role="tab" aria-controls="pills-dstab8" aria-selected="false">History </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-2" data-toggle="pill" href="#pills-dstab2" role="tab" aria-controls="pills-dstab2" aria-selected="true">GE</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-3" data-toggle="pill" href="#pills-dstab3" role="tab" aria-controls="pills-dstab3" aria-selected="true">Diagnosis</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-4" data-toggle="pill" href="#pills-dstab4" role="tab" aria-controls="pills-dstab4" aria-selected="false">Tests</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-5" data-toggle="pill" href="#pills-dstab5" role="tab" aria-controls="pills-dstab5" aria-selected="false">Medicines</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-6" data-toggle="pill" href="#pills-dstab6" role="tab" aria-controls="pills-dstab6" aria-selected="false">Therapies</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="pills-dstab-7" data-toggle="pill" href="#pills-dstab7" role="tab" aria-controls="pills-dstab7" aria-selected="false">Remark </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="tab-content p-0" id="pills-tabContent-desc">
                                                    <div class="tab-pane fade show active" id="pills-dstab1" role="tabpanel" aria-labelledby="pills-dstab1-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(shistory, skey) in appointment_histories.symptoms" :key="skey">
                                                                    <th class="wf-100">{{ skey }}<br>{{ shistory.date}}</th>
                                                                    <td><span class="d-block" v-for="hdata in  JSON.parse(shistory.data)" :key="hdata.id">
                                                                        {{ hdata.name }} - {{ hdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab2" role="tabpanel" aria-labelledby="pills-dstab2-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(thistory, tkey) in appointment_histories.oe_categories" :key="tkey">
                                                                    <th class="wf-100">{{ tkey }}<br>{{ thistory.date}}</th>
                                                                    <td><span class="d-block" v-for="hdata in  JSON.parse(thistory.data)" :key="hdata.id">
                                                                        {{ hdata.name }} - {{ hdata.result }} - {{ hdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab3" role="tabpanel" aria-labelledby="pills-dstab3-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(mhistory, mkey) in appointment_histories.diagnosis" :key="mkey">
                                                                    <th class="wf-100">{{ mkey }}<br>{{ mhistory.date}}</th>
                                                                    <td><span class="d-block" v-for="hdata in  JSON.parse(mhistory.data)" :key="hdata.id">
                                                                        {{ hdata.name }} - {{ hdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab4" role="tabpanel" aria-labelledby="pills-dstab4-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(thhistory, thkey) in appointment_histories.tests" :key="thkey">
                                                                    <th class="wf-100">{{ thkey }}<br>{{ thhistory.date}}</th>
                                                                    <td><span class="d-block" v-for="hdata in  JSON.parse(thhistory.data)" :key="hdata.id">
                                                                        {{ hdata.name }} - {{ hdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab5" role="tabpanel" aria-labelledby="pills-dstab5-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(mhistory, mkey) in appointment_histories.medicines" :key="mkey">
                                                                    <th class="wf-100">{{ mkey }}<br>{{ mhistory.date }}</th>
                                                                    <td><span class="d-block" v-for="(mdata, ms) in  JSON.parse(mhistory.data)" :key="ms+'-'+mdata.id">
                                                                        {{ mdata.name }} ({{ mdata.qty }} In {{ mdata.days}}  days) - ({{ mdata.dtab }}) - {{ mdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab6" role="tabpanel" aria-labelledby="pills-dstab6-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(thhistory, thkey) in appointment_histories.therapies" :key="thkey">
                                                                    <th class="wf-100">{{ thkey }}<br>{{ thhistory.date }}</th>
                                                                    <td><span class="d-block" v-for="(thdata, ths) in  JSON.parse(thhistory.data)" :key="ths+'-'+thdata.id">
                                                                        {{ thdata.name }} ({{ thdata.qty }} In {{ thdata.days}}  days) - {{ thdata.remark }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab7" role="tabpanel" aria-labelledby="pills-dstab7-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(trhistory, trkey) in appointment_histories.remarks" :key="trkey">
                                                                    <th class="wf-100">{{ trkey }}<br>{{ trhistory.date }}</th>
                                                                    <td>{{ trhistory.remark }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-dstab8" role="tabpane8" aria-labelledby="pills-dstab8-tab">
                                                        <table class="table table-condensed table-striped table-bordered">
                                                            <tbody>
                                                                <tr v-for="(shistory, skey) in appointment_histories.drug_history" :key="skey">
                                                                    <th class="wf-100">{{ skey }}<br>{{ shistory.date}}</th>
                                                                    <td><span class="d-block" v-for="hdata in  JSON.parse(shistory.data)" :key="hdata.id">
                                                                        {{ hdata }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="medbox">
                                        <div>
                                            <ul class="nav nav-pills m-0" id="pills-tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="pills-tab-1" data-toggle="pill" href="#pills-tab1" role="tab" aria-controls="pills-tab1" aria-selected="true">Symptoms</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-8" data-toggle="pill" href="#pills-tab8" role="tab" aria-controls="pills-tab8" aria-selected="false">Past History</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-6" data-toggle="pill" href="#pills-tab6" role="tab" aria-controls="pills-tab6" aria-selected="true">General Examination</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-2" data-toggle="pill" href="#pills-tab2" role="tab" aria-controls="pills-tab2" aria-selected="false">Lab Examination</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-7" data-toggle="pill" href="#pills-tab7" role="tab" aria-controls="pills-tab7" aria-selected="true">Diagnosis</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-3" data-toggle="pill" href="#pills-tab3" role="tab" aria-controls="pills-tab3" aria-selected="false">Medicines</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-4" data-toggle="pill" href="#pills-tab4" role="tab" aria-controls="pills-tab4" aria-selected="false">Therapies</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-tab-5" data-toggle="pill" href="#pills-tab5" role="tab" aria-controls="pills-tab5" aria-selected="false">Remark &amp; Followup</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-tab1" role="tabpanel" aria-labelledby="pills-tab1-tab">
                                                <div class="pbody">
                                                    <button class="btn btn-primary btn-sm wf-125 mb-1" type="button" @click="addSymptom">
                                                        <i class="fas fa-plus"></i> Add Symptom
                                                    </button>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">SNo</th>
                                                                <th style="width: 200px;">Symptom</th>
                                                                <th>Remark</th>
                                                                <th style="width: 50px;"><button type="button" @click="addSBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in scountlist" :key="'sc'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="symptoms"
                                                                            name="symptom[]['id']"
                                                                            v-model="form.symptom__id[getIndex(counter, cID)]"
                                                                            placeholder="select symptom">
                                                                    </model-select>
                                                                </td>
                                                                <td><input v-model="form.symptom__remark[getIndex(counter, cID)]" class="form-control" type="text" name="symptom[]['remark']"  value="1"></td>
                                                                <td>
                                                                    <button type="button" @click="removeSBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab2" role="tabpanel" aria-labelledby="pills-tab2-tab">
                                                <div class="pbody">
                                                    <button class="btn btn-primary btn-sm mb-1 wf-125" type="button" @click="addXray">
                                                        <i class="fas fa-plus"></i> Add X-Ray
                                                    </button>
                                                    <button class="btn btn-primary btn-sm mb-1 wf-125" type="button" @click="addLabTest">
                                                        <i class="fas fa-plus"></i> Add Lab Test
                                                    </button>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">SNo</th>
                                                                <th style="width: 120px;">Test Type</th>
                                                                <th style="width: 120px;">Test</th>
                                                                <th>Result</th>
                                                                <th>Attachments</th>
                                                                <th>Remark</th>
                                                                <th style="width: 50px;"><button type="button" @click="addTBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in tcountlist" :key="'ttc'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="test_types"
                                                                            name="test[]['type_id']"
                                                                            v-model="form.test__type_id[getIndex(counter, cID)]"
                                                                            placeholder="select test type">
                                                                    </model-select>
                                                                </td>
                                                                <td class="opselector">
                                                                    <model-select :options="xtests"
                                                                            name="test[]['id']"
                                                                            v-model="form.test__id[getIndex(counter, cID)]"
                                                                            placeholder="select x-ray type" v-if="form.test__type_id[getIndex(counter, cID)] == 'xray'">
                                                                    </model-select>
                                                                    <model-select :options="dtests"
                                                                            name="test[]['id']"
                                                                            v-model="form.test__id[getIndex(counter, cID)]"
                                                                            placeholder="select lab test" v-else-if="form.test__type_id[getIndex(counter, cID)] == 'labtest'">
                                                                    </model-select>
                                                                    <span v-else> -- </span>
                                                                </td>
                                                               <td><input v-model="form.test__result[getIndex(counter, cID)]" class="form-control" type="text" name="test[]['result']"  value="1"></td>
                                                                <td><input @change="uploadFile(getIndex(counter, cID))" class="form-control" type="file" name="test[]['files']" style="height:auto"></td>
                                                                <td><input v-model="form.test__remark[getIndex(counter, cID)]" class="form-control" type="text" name="test[]['remark']"  value="1"></td>
                                                                <td>
                                                                    <button type="button" @click="removeTBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab3" role="tabpanel" aria-labelledby="pills-tab3-tab">
                                                <div class="pbody">
                                                    <button type="button" @click="addMBar" class="btn btn-secondary  m-b-5 btn-sm m-r-5"><i class="fa fa-plus"></i></button>
                                                    <button class="btn btn-success m-r-5  m-b-5 btn-sm" type="button" @click="viewMedList"> Show Medicine Stock List</button>
                                                     <button class="btn btn-danger btn-sm m-b-5" type="button" @click="hideMedList"> Hide Stock List</button>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="wf-50">SNo</th>
                                                                <th>Medicine</th>
                                                                <th class="wf-200">Dose</th>
                                                                <th>Instructions</th>
                                                                <th class="wf-100">Days</th>
                                                                <th class="wf-100">Qty</th>
                                                                <th class="wf-50"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in mcountlist" :key="'mc'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="medicines"
                                                                            name="medicine[]['id']"
                                                                            v-model="form.medicine__id[getIndex(counter, cID)]"
                                                                            placeholder="select medicine"
                                                                            @input="getbatch">
                                                                    </model-select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                            name="medicine[]['dtab']"
                                                                            v-model="form.medicine__dtab[getIndex(counter, cID)]"
                                                                            placeholder="add dose" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                            name="medicine[]['remark']"
                                                                            v-model="form.medicine__remark[getIndex(counter, cID)]"
                                                                            placeholder="add Instructions" />
                                                                </td>
                                                                <td>
                                                                    <input v-model="form.medicine__days[getIndex(counter, cID)]" class="form-control" type="text" name="medicine[]['days']">
                                                                </td>
                                                                <td>
                                                                    <input v-model="form.medicine__qty[getIndex(counter, cID)]" class="form-control" type="number" name="medicine[]['qty']">
                                                                </td>
                                                                <td>
                                                                    <button type="button" @click="removeMBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab4" role="tabpanel" aria-labelledby="pills-tab4-tab">
                                                <div class="pbody">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">SNo</th>
                                                                <th style="width: 250px;">Therapy</th>
                                                                <th style="width: 120px;">Days</th>
                                                                <th>Remark</th>
                                                                <th style="width: 50px;"><button type="button" @click="addThBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in thcountlist" :key="'te'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="treatments"
                                                                            name="therapy[]['id']"
                                                                            v-model="form.therapy__id[getIndex(counter, cID)]"
                                                                            placeholder="select therapy">
                                                                    </model-select>
                                                                </td>
                                                                <td>
                                                                    <input v-model="form.therapy__days[getIndex(counter, cID)]" class="form-control" type="text" name="therapy[]['days']">
                                                                </td>
                                                                <td><input v-model="form.therapy__remark[getIndex(counter, cID)]" class="form-control" type="text" name="therapy[]['remark']"  value="1"></td>
                                                                <td>
                                                                    <button type="button" @click="removeThBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab5" role="tabpanel" aria-labelledby="pills-tab5-tab">
                                                <div class="pbody p-l-5 p-r-5">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label for="dr_remark" class="control-label">Remark</label>
                                                            <textarea class="form-control" rows="5" v-model="form.dr_remark" placeholder="enter remark here"></textarea>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="control-label">Follow UP</label>
                                                            <input type="number" class="form-control" v-model="form.followup" placeholder="enter no of days for followup">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab6" role="tabpanel" aria-labelledby="pills-tab6-tab">
                                                <div class="pbody">
                                                    <button class="btn btn-primary btn-sm mb-1 wf-125" type="button" @click="addOE">
                                                        <i class="fas fa-plus"></i> Add GE
                                                    </button>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">SNo</th>
                                                                <th>General Examination</th>
                                                                <th>Result</th>
                                                                <th>Remark</th>
                                                                <th style="width: 50px;"><button type="button" @click="addOBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in ocountlist" :key="'oc'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="oecategories"
                                                                            name="oecategory[]['id']"
                                                                            v-model="form.oecategory__id[getIndex(counter, cID)]"
                                                                            placeholder="select oe category">
                                                                    </model-select>
                                                                </td>
                                                               <td><input v-model="form.oecategory__result[getIndex(counter, cID)]" class="form-control" type="text" name="oecategory[]['result']"></td>
                                                                <td><input v-model="form.oecategory__remark[getIndex(counter, cID)]" class="form-control" type="text" name="oecategory[]['remark']"></td>
                                                                <td>
                                                                    <button type="button" @click="removeOBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab7" role="tabpanel" aria-labelledby="pills-tab7-tab">
                                                <div class="pbody">
                                                    <button class="btn btn-primary btn-sm mb-1 wf-125" type="button" @click="addDiagnosis">
                                                        <i class="fas fa-plus"></i> Add Diagnosis
                                                    </button>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">SNo</th>
                                                                <th>Diagnosis</th>
                                                                <th>Remark</th>
                                                                <th style="width: 50px;"><button type="button" @click="addDBar" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i></button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(counter, cID) in dcountlist" :key="'dc'+cID" :id="'row'+cID">
                                                                <td>{{  counter }}</td>
                                                                <td class="opselector">
                                                                    <model-select :options="diagnosis"
                                                                            name="diagnosis[]['id']"
                                                                            v-model="form.diagnosis__id[getIndex(counter, cID)]"
                                                                            placeholder="select diagnosis">
                                                                    </model-select>
                                                                </td>
                                                                <td><input v-model="form.diagnosis__remark[getIndex(counter, cID)]" class="form-control" type="text" name="diagnosis[]['remark']"  value="1"></td>
                                                                <td>
                                                                    <button type="button" @click="removeDBar(cID)" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tab8" role="tabpanel" aria-labelledby="pills-tab8-tab">
                                                <div class="pbody p-l-5 p-r-5">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label class="control-label">Past History</label>
                                                            <textarea class="form-control" rows="5" v-model="form.general_history"></textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="control-label">Family History</label>
                                                            <textarea class="form-control" rows="5" v-model="form.family_history"></textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="control-label">Drug History</label>
                                                            <textarea class="form-control" rows="5" v-model="form.drug_history"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">

                                        </div>
                                        <div class="col-md-2 m-t-20">
                                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 1">
                                            <button type="button" @click="savePrescription" class="btn btn-sm btn-block btn-primary m-r-5" v-else>Save Prescription</button>
                                        </div>
                                        <div class="col-md-2 m-t-20">
                                            <img class="wf-50" :src="loaderurl" alt="updating" v-if="loader == 1">
                                            <button type="button" @click="submitPrescription" class="btn btn-sm btn-block btn-dark" v-else>Submit Prescription</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addsymptomModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addsymptomModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createSymptom()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addsymptomModalTitle">Add Symptom</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-12">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="sform.value" type="text" name="value" id="value" placeholder="enter value"
                                            class="form-control" :class="{ 'is-invalid': sform.errors.has('value') }">
                                        <has-error :form="sform" field="value"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Create </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addoeModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addoeModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createOE()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addoeModalTitle">Add O/E</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-12">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="oform.value" type="text" name="value" id="value" placeholder="enter value"
                                            class="form-control" :class="{ 'is-invalid': oform.errors.has('value') }">
                                        <has-error :form="oform" field="value"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Create </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="adddiagnosisModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="adddiagnosismModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createDiagnosis()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="adddiagnosismModalTitle">Add Diagnosis</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-12">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="dform.value" type="text" name="value" id="value" placeholder="enter value"
                                            class="form-control" :class="{ 'is-invalid': dform.errors.has('value') }">
                                        <has-error :form="dform" field="value"></has-error>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="code" class="control-label">Code</label>
                                        <input v-model="dform.code" type="text" name="code" id="code" placeholder="enter code"
                                            class="form-control" :class="{ 'is-invalid': dform.errors.has('code') }">
                                        <has-error :form="dform" field="code"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Create </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="stockModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="stockmModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="stockmModalTitle">Stock Details</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" v-if="showmLoader">
                                 <img style="height:28px" :src="svgurl+'loop.gif'" alt="updating">
                            </div>
                            <div class="col-md-12" v-else>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Batch No</th>
                                            <th>Exp Date</th>
                                            <th>Stock</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="stocks.length == 0">
                                            <td colspan="5"><p><b class="alert alert-danger text-uppercase font-weight-bold d-block"><i>Out Of Stock</i></b></p></td>
                                        </tr>
                                        <tr v-for="(stock, stk) in stocks" :key="stk" v-else>
                                            <td>{{ ++stk }}</td>
                                            <td>{{ stock.batch_no }}</td>
                                            <td>{{ stock.exp_date | setdate }}</td>
                                            <td>{{ stock.stock }}</td>
                                            <td>{{ stock.medicine.cash_price | formatOMR }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-danger text-uppercase font-weight-bold" v-show="notins">
                                    Alert! this medicine is not covered under insurance.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-wide btn-success" data-dismiss="modal"> Ok </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addxrayModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addxrayModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createXray()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addxrayModalTitle">Add Xray</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="value" class="control-label">Value</label>
                                    <input v-model="xform.value" type="text" name="value" id="value" placeholder="enter value"
                                        class="form-control" :class="{ 'is-invalid': xform.errors.has('value') }">
                                    <has-error :form="xform" field="value"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="status_id" class="control-label">Status</label>
                                    <select v-model="xform.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': xform.errors.has('status_id') }">
                                        <option value="" disabled>Select Status</option>
                                        <option value="2">Active</option>
                                        <option value="3">Deactive</option>
                                    </select>
                                    <has-error :form="xform" field="status_id"></has-error>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Create </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addlaboratoryModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addlaboratoryModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="createlabTest()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addlaboratoryModalTitle">Add Laboratory Test</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                         <label for="lab_category_id" class="control-label">Category</label>
                                        <select v-model="lform.lab_category_id" name="lab_category_id" class="form-control" :class="{ 'is-invalid': lform.errors.has('lab_category_id') }">
                                            <option disabled value="">Select Category</option>
                                            <option v-for="category in categories" :key="category.id" v-bind:value="category.id">
                                                {{ category.value }}
                                            </option>
                                        </select>
                                        <has-error :form="lform" field="lab_category_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="value" class="control-label">Name</label>
                                        <input v-model="lform.value" type="text" name="value" id="value" placeholder="enter name"
                                            class="form-control" :class="{ 'is-invalid': lform.errors.has('value') }">
                                        <has-error :form="lform" field="value"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="status_id" class="control-label">Status</label>
                                        <select v-model="lform.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': lform.errors.has('status_id') }">
                                            <option value="" disabled>Select Status</option>
                                            <option value="2">Active</option>
                                            <option value="3">Deactive</option>
                                        </select>
                                        <has-error :form="lform" field="status_id"></has-error>
                                    </div>
                                </div>
                                <div class="col-6">
                                   <div class="form-group">
                                        <label for="lab_range" class="control-label">lab range</label>
                                        <textarea v-model="lform.lab_range" name="lab_range" rows="4" id="lab_range" placeholder="enter lab range"
                                            class="form-control" :class="{ 'is-invalid': lform.errors.has('lab_range') }"></textarea>
                                        <has-error :form="lform" field="lab_range"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="remark" class="control-label">Remark</label>
                                        <textarea v-model="lform.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                            class="form-control" :class="{ 'is-invalid': lform.errors.has('remark') }"></textarea>
                                        <has-error :form="lform" field="remark"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button v-show="!editmode" type="submit" class="btn btn-wide btn-success"> Create </button>
                            <button v-show="editmode" type="submit" class="btn btn-wide btn-success"> Update </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="fade sidebar modal" id="medStockModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="sidebar-header">
                        <h3>Medicines Stocks Details</h3>
                        <button type="button" class="btn btn-close float-left" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="components vfixedh">
                        <table class="table table-striped table-condensed table-hover">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>S.No</th>
                                    <th>Medicine</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(mstock, sID) in medstocks" :key="mstock.id">
                                    <td>{{ sID +1 }}</td>
                                    <td>{{ mstock.name }}</td>
                                    <td> {{ mstock.stock_sum | freeNumber }} </td>
                                </tr>
                            </tbody>
                        </table>
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
                showmLoader:false,
                docurl: '/files/docs/',
                loader: false,
                loaderurl: '/svg/loop.gif',
                editmode: false,
                catchmessage: '',
                activeID: '',
                active_location: '',
                appointment:'',
                listSlots: [],
                customer: {},
                newEnabled:'',
                categories:{},
                ocountlist:[1,2,3],
                dcountlist:[1,2,3],
                tcountlist:[1,2,3],
                scountlist:[1,2,3],
                mcountlist:[1,2,3],
                thcountlist:[1,2,3],
                oecategories:[],
                diseases:[],
                diagnosis:[],
                treatments:[],
                symptoms:[],
                appointment_histories:'',
                medicines_doses:[],
                medicines_instructions:[],
                test_types:[{'value': 'xray', 'text':'X-Ray'},{'value': 'labtest', 'text':'Laboratories'}],
                xtests:[],
                dtests:[],
                medicines:[],
                stocks:[],
                notins:'',
                medstocks:[],
                form: new Form({
                        appointment_code: '',
                        user_id:'',
                        dr_remark:'',
                        followup:'',
                        medicine__id:[],
                        medicine__days:[],
                        medicine__qty:[],
                        medicine__dtab:[],
                        medicine__remark:[],
                        therapy__id:[],
                        therapy__days:[],
                        therapy__remark:[],
                        symptom__id:[],
                        symptom__remark:[],
                        oecategory__id:[],
                        oecategory__result:[],
                        oecategory__remark:[],
                        disease__id:[],
                        disease__remark:[],
                        diagnosis__id:[],
                        diagnosis__remark:[],
                        test__type_id:[],
                        test__id:[],
                        test__remark:[],
                        test__result:[],
                        test__files:[],
                        drug_history:'',
                        general_history:'',
                        family_history:'',
                        status_id:''
                }),
                sform: new Form({
                    value:'',
                    status_id:'2'
                }),
                oform: new Form({
                    value:'',
                    status_id:'2'
                }),
                dform: new Form({
                    value:'',
                    code:'',
                    status_id:'2'
                }),
                xform: new Form({
                    value:'',
                    status_id:'2'
                }),
                lform: new Form({
                    id:'',
                    value:'',
                    lab_category_id:'',
                    lab_range: '',
                    remark:'',
                    status_id:'2'
                })
            }
        },
        methods: {
            addSymptom() {this.sform.reset(); $('#addsymptomModal').modal('show'); },
            createSymptom() {
                this.sform.post('/api/symptoms').then(() => { $('#addsymptomModal').modal('hide');
                    toast.fire({type:'success', title:'Symptom created successfully' });
                    axios.get('/api/getSymptomsSelectList').then((data) => {this.symptoms = data.data }).catch(); });
            },
            addOE() {this.oform.reset(); $('#addoeModal').modal('show'); },
            createOE() {
                this.oform.post('/api/oe-categories').then(() => { $('#addoeModal').modal('hide');
                    toast.fire({type:'success', title:'O/E created successfully' });
                    axios.get('/api/getOeCategoriesSelectList').then((data) => {this.oecategories = data.data }).catch(); });
            },
            addDiagnosis() {this.dform.reset(); $('#adddiagnosisModal').modal('show'); },
            createDiagnosis() {
                this.dform.post('/api/diagnosis').then(() => {  $('#adddiagnosisModal').modal('hide');
                    toast.fire({type:'success', title:'Diagnosis created successfully' });
                    axios.get('/api/getDiseasesSelectList').then((data) => {this.diagnosis = data.data }).catch(); });
            },
            addXray() {this.xform.reset(); $('#addxrayModal').modal('show'); },
            createXray() {
                this.xform.post('/api/xrays').then(() => { $('#addxrayModal').modal('hide');
                    toast.fire({type:'success', title:'Xray created successfully' });
                    axios.get('/api/getXTestsSelectList').then((data) => { this.xtests = data.data }).catch();  });
            },
            addLabTest() {  axios.get('/api/getLabCategoryList').then(({ data }) => (this.categories = data));
                this.lform.reset(); $('#addlaboratoryModal').modal('show'); },
            createlabTest() {
                this.lform.post('/api/laboratories').then(() => { $('#addlaboratoryModal').modal('hide');
                    toast.fire({type:'success', title:'Lab test created successfully' });
                    axios.get('/api/getDTestsSelectList').then((data) => { this.dtests = data.data }).catch();  });
            },
            getIndex(list, id) {
                return id;
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
                if(id == ''){id = this.form.user_id}
                axios.get('/api/customers/'+id)
                    .then((data) => {this.customer = data.data[0] })
                    .catch();
                $('#userModal').modal('show');
            },
            hideCustomer() {
                $('#userModal').modal('hide');
            },
            getAllAssets() {
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
                axios.get('/api/getOeCategoriesSelectList').then((data) => {this.oecategories = data.data }).catch();
                axios.get('/api/getDiseasesSelectList').then((data) => {this.diagnosis = data.data }).catch();
               // axios.get('/api/getDiagnosisSelectList').then((data) => {this.diagnosis = data.data }).catch();
                axios.get('/api/getPresTreatmentsSelectList').then((data) => {this.treatments = data.data }).catch();
                axios.get('/api/getSymptomsSelectList').then((data) => {this.symptoms = data.data }).catch();
                axios.get('/api/getMedicinesSelectList').then((data) => {this.medicines = data.data }).catch();
                axios.get('/api/getXTestsSelectList').then((data) => { this.xtests = data.data }).catch();
                axios.get('/api/getDTestsSelectList').then((data) => { this.dtests = data.data }).catch();
                axios.get('/api/getDosesSelectList').then((data) => { this.medicines_doses = data.data }).catch();
                axios.get('/api/getInstructionsSelectList').then((data) => { this.medicines_instructions =  data.data }).catch();
                axios.get('/api/getMedStockList').then((data) => { this.medstocks =  data.data }).catch();
            },
            getPrimaryAssets() {
                axios.get('/api/getSlotsList').then((data) => {this.listSlots = data.data }).catch();
            },
            showAppointment() {
                this.$Progress.start();
                let $frm = this.form;
                let $ths = this;
                axios.get('/api/appointments/view/'+this.activeID).then(({ data }) => {
                    this.appointment = data[0];
                    $frm.user_id = this.appointment.user_id;
                    $frm.followup = this.appointment.reminder_days;
                    $frm.dr_remark = this.appointment.dr_remark;
                    if(this.appointment.symptoms) {
                        JSON.parse(this.appointment.symptoms).forEach(function(element, i) {
                            if(i >= 3) { $ths.addSBar(); }
                            $frm.symptom__id.push(element.id);
                            $frm.symptom__remark.push(element.remark);
                        });
                    }
                    if(this.appointment.oe_categories) {
                        JSON.parse(this.appointment.oe_categories).forEach(function(element, i) {
                            if(i >= 3) { $ths.addOBar(); }
                            $frm.oecategory__id.push(element.id);
                            $frm.oecategory__result.push(element.result);
                            $frm.oecategory__remark.push(element.remark);
                        });
                    } else {
                        let oep = [2,13,15,16];
                        oep.forEach(function(element, i) {
                            if(i >= 3) { $ths.addOBar(); }
                            $frm.oecategory__id.push(element);
                            $frm.oecategory__result.push('');
                            $frm.oecategory__remark.push('');
                        });
                    }
                    if(this.appointment.diagnosis) {
                        JSON.parse(this.appointment.diagnosis).forEach(function(element, i) {
                            if(i >= 3) { $ths.addDBar(); }
                            $frm.diagnosis__id.push(element.id);
                            $frm.diagnosis__remark.push(element.remark);
                        });
                    }
                    if(this.appointment.drug_history) {
                        let dh = JSON.parse(this.appointment.drug_history);
                        $frm.drug_history = dh.drug_history;
                        $frm.general_history = dh.general_history;
                        $frm.family_history = dh.family_history;
                    }
                    if(this.appointment.therapies) {
                        JSON.parse(this.appointment.therapies).forEach(function(element, i) {
                            if(i >= 3) { $ths.addThBar(); }
                            $frm.therapy__id.push(element.id);
                            $frm.therapy__days.push(element.days);
                            $frm.therapy__remark.push(element.remark);
                        });
                    }

                    if(this.appointment.medicines) {
                        JSON.parse(this.appointment.medicines).forEach(function(element, i) {
                            if(i >= 3) { $ths.addMBar(); }
                            $frm.medicine__id.push(element.id);
                            $frm.medicine__days.push(element.days);
                            $frm.medicine__qty.push(element.qty);
                            $frm.medicine__dtab.push(element.dtab);
                            $frm.medicine__remark.push(element.remark);
                        });
                    }
                    if(this.appointment.tests) {
                        JSON.parse(this.appointment.tests).forEach(function(element, i) {
                            if(i >= 3) { $ths.addTBar(); }
                            $frm.test__type_id.push(element.type);
                            $frm.test__id.push(element.id);
                            $frm.test__remark.push(element.remark);
                            $frm.test__result.push(element.result);
                            $frm.test__files.push(element.files);
                        });
                    }

                    /* if(this.appointment.status_id >= 5) {
                        this.$router.push('/doctors/view-appointment/'+this.appointment.appointment_code);
                    } */

                    this.$Progress.finish();
                });
            },
            getAppointmentHistory() {
                this.$Progress.start();
                axios.get('/api/appointments/user-history/'+this.activeID).then(({ data }) => {
                    //console.log(data);
                    this.appointment_histories = data;
                    this.$Progress.finish();
                });
            },
            /* defineSlots() {
                var slot = this.appointment.time_slots;
                let slots = slot.split(',');
                let timing = this.listSlots[slots[0]]+' - '+this.listSlots[slots[slots.length - 1]];
                return timing;
            }, */
            makeNumber(val) {
                if(isNaN(val)) { return 0; }
                else { return parseFloat(val); }
            },
            addSBar(){ let bcount = this.scountlist.length; this.scountlist.push(bcount+1); },
            addOBar(){ let ocount = this.ocountlist.length; this.ocountlist.push(ocount+1); },
            addDBar(){ let dcount = this.dcountlist.length; this.dcountlist.push(dcount+1); },
            addTBar(){ let bcount = this.tcountlist.length; this.tcountlist.push(bcount+1); },
            addMBar(){ let bcount = this.mcountlist.length; this.mcountlist.push(bcount+1); },
            addThBar(){ let bcount = this.thcountlist.length; this.thcountlist.push(bcount+1); },
            removeSBar(barkey){
                this.$delete(this.scountlist, barkey);
                this.form.symptom__id.splice(barkey, 1);
            },
            removeOBar(barkey){
                this.$delete(this.ocountlist, barkey);
                this.form.oecategory__id.splice(barkey, 1);
            },
            removeDBar(barkey){
                this.$delete(this.dcountlist, barkey);
                this.form.disease__id.splice(barkey, 1);
            },
            removeTBar(barkey){
                this.$delete(this.tcountlist, barkey);
                this.form.test__id.splice(barkey, 1);
            },
            removeMBar(barkey){
                this.$delete(this.mcountlist, barkey);
                this.form.medicine__id.splice(barkey, 1);
            },
            removeThBar(barkey){
                this.$delete(this.thcountlist, barkey);
                this.form.therapy__id.splice(barkey, 1);
            },

            uploadFile(e, okey) {
                let file = e.target.files[0];
                let reader = new FileReader();
                let pi = this.form;
                reader.onloadend = (file) => {
                    pi.test__files[okey] = reader.result;
                }
                reader.readAsDataURL(file);
            },

            onProductSelect(pid, key) {
                axios.get('/api/getMedicineDetail/'+pid)
                    .then((data) => {
                        //this.form.medicine__id[key] = data.data.id;
                        this.form.medicine__name[key] = data.data.name;
                    })
                    .catch();
                //console.log(product);
               //this.productSelected[key] = product;
            },
            getbatch(product) {
                this.notins = false;
                if(this.appointment.insurance_id >= 1) {
                    axios.post('/api/check-medicine-mapping', {insurance_id:this.appointment.insurance_id,medicine_id:product})
                    .then((data) => { this.notins = data.data.status; });
                }
                axios.get('/api/stocks/get-medicine-stocks/'+product)
                .then((data) => {
                    if(data.data){
                        if(data.data.status == 'success') {
                            this.stocks = data.data.data;
                        }
                        else {
                            this.stocks = '';
                        }

                        //this.rePhTotal();
                    }
                });
                $('#stockModal').modal('show');
            },
            submitPrescription(){
                this.loader = 1
                this.form.status_id = 5;
                this.form.post('/api/appointments/add-prescription')
                .then((data) => {
                    //console.log(data);
                    swal.fire({
                        title: 'Prescription has been submitted successfully',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Go To Print',
                        cancelButtonText: 'Stay On The Page',
                        footer: '<a href="/doctors/appointments-day-schedule"> Go To Appointments List</a>'
                    }).then((result) => {
                        if (result.value) {
                            this.loader = false;
                            let route = this.$router.resolve({path: '/appointments/print-perscription/'+this.activeID});
                            window.open(route.href, '_blank');
                            //this.$router.push('/customers/all');
                        }
                    }).catch(() => {

                    });

                    this.loader = false;
                })
                .catch(() => {
                    this.loader = false;
                });
            },
            savePrescription(){
                this.loader = 1;
                this.form.status_id = 10;
                this.form.post('/api/appointments/add-prescription')
                .then((data) => {
                    swal.fire({
                        title: 'Prescription saved successfully',
                        type: 'success',
                        }).then((result) => {
                            this.$toaster.success('Prescription has been saved.');
                        });
                    this.loader = false;
                })
                .catch(() => {
                    this.loader = false;
                });
            },
            viewMedList() {
                $('#medStockModal').modal('show');
                $('.modal-backdrop').hide();
            },
            hideMedList() {
                $('#medStockModal').modal('hide');
            },
            setID() {
                let activeId = this.$route.path.split("/");
                this.activeID = activeId[3];
                this.form.appointment_code =  activeId[3];
            }
        },
        beforeMount() {
            this.setID();
        },
        mounted() {
            this.showAppointment();
            this.getPrimaryAssets();
            this.getAppointmentHistory();
            this.getAllAssets();
            //this.defineSlots();
        }

    }
</script>
