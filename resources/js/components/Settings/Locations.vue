<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Locations </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="location in locations.data" :key="location.id">
                                            <td>{{ location.id }}</td>
                                            <td>{{ location.clinic_name | capitalize }}</td>
                                            <td>{{ location.address }}</td>
                                            <td>{{ location.city | capitalize }}</td>
                                            <td>{{ location.created_at | setdate }}</td>
                                            <td align="center" v-if="location.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editLocation(location)"><i class="fas fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="locations.total > 1">
                                <pagination class="m-0 float-right" :data="locations" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addlocationModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addlocationModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateLocation() : createLocation()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addlocationModalTitle">Add Location</h5>
                            <h5 class="modal-title" v-show="editmode" id="addlocationModalTitle">Update Location's Info</h5>
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
                locations: {},
                form: new Form({
                    id:'',
                    value:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/locations?page=' + page)
                    .then(response => {
                        this.locations = response.data;
                    });
            },
            showLocations() {
                this.$Progress.start();
                axios.get('/api/locations').then(({ data }) => {
                    this.locations = data;
                    this.$Progress.finish();
                });
            },
            addLocation() {
                this.editmode = false;
                this.form.reset();
                $('#addlocationModal').modal('show');
            },
            createLocation() {
                this.form.post('/api/locations')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlocationModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Location created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateLocation() {
                this.form.put('/api/locations/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addlocationModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Location details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editLocation(location) {
                this.editmode = true;
                this.form.fill(location);
                $('#addlocationModal').modal('show');
            },
            deleteLocation(id) {
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
                        this.form.delete('/api/locations/'+id)
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
            this.showLocations();
            Fire.$on('AfterCreate', () => {
                this.showLocations();
            });
        }
    }
</script>
