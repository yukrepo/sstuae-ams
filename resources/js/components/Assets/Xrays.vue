<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Xrays List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addXray"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Value</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(xray, count) in xrays.data" :key="xray.id">
                                            <td>{{ (xrays.current_page - 1)*(xrays.per_page) + count + 1 }}</td>
                                            <td>{{ xray.value }}</td>
                                            <td>{{ xray.created_at | setdate }}</td>
                                            <td align="center" v-if="xray.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editXray(xray)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteXray(xray.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="xrays.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="xrays" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addxrayModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addxrayModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateXray() : createXray()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addxrayModalTitle">Add Xray</h5>
                            <h5 class="modal-title" v-show="editmode" id="addxrayModalTitle">Update Xray's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="value" class="control-label">Value</label>
                                    <input v-model="form.value" type="text" name="value" id="value" placeholder="enter value"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('value') }">
                                    <has-error :form="form" field="value"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="status_id" class="control-label">Status</label>
                                    <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                        <option value="" disabled>Select Status</option>
                                        <option value="2">Active</option>
                                        <option value="3">Deactive</option>
                                    </select>
                                    <has-error :form="form" field="status_id"></has-error>
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
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editmode: false,
                xrays: {},
                form: new Form({
                    id:'',
                    value:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/xrays?page=' + page)
                    .then(response => {
                        this.xrays = response.data;
                    });
            },
            showXrays() {
                this.$Progress.start();
                axios.get('/api/xrays').then(({ data }) => {
                    this.xrays = data;
                    this.$Progress.finish();
                });
            },
            addXray() {
                this.editmode = false;
                this.form.reset();
                $('#addxrayModal').modal('show');
            },
            createXray() {
                this.form.post('/api/xrays')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addxrayModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Xray created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateXray() {
                this.form.put('/api/xrays/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addxrayModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Xray details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editXray(xray) {
                this.editmode = true;
                this.form.fill(xray);
                $('#addxrayModal').modal('show');
            },
            deleteXray(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('/api/xrays/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            }
        },
        created() {
            this.showXrays();
            Fire.$on('AfterCreate', () => {
                this.showXrays();
            });
        }
    }
</script>
