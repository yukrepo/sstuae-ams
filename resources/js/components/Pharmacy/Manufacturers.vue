<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Manufacturers List
                                    <div class="card-tools">
                                        <button class="btn btn-secondary btn-sm mr-1" type="button" @click="addManufacturer"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">ID</th>
                                            <th>Name</th>
                                            <th>Remark</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="manufacturer in manufacturers.data" :key="manufacturer.id">
                                            <td>{{ manufacturer.id }}</td>
                                            <td>{{ manufacturer.name }}</td>
                                            <td>{{ manufacturer.description }}</td>
                                            <td align="center" v-if="manufacturer.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"> <i class="fas fa-times"></i> </label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editManufacturer(manufacturer)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteManufacturer(manufacturer.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :data="manufacturers" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addmanufacturerModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addmanufacturerModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateManufacturer() : createManufacturer()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addmanufacturerModalTitle">Add Manufacturer</h5>
                            <h5 class="modal-title" v-show="editmode" id="addmanufacturerModalTitle">Update Manufacturer's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="control-label">Manufacturer Name</label>
                                <input v-model="form.name" type="text" name="name" placeholder="enter manufacturer name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="description" class="control-label">remark</label>
                                <textarea v-model="form.description" name="description" placeholder="enter remark" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                <has-error :form="form" field="description"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="status_id" class="control-label">Status</label>
                                <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                    <option disabled value="">Select Status</option>
                                    <option value="2">Active</option>
                                    <option value="3">Deactive</option>
                                </select>
                                <has-error :form="form" field="status_id"></has-error>
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
                manufacturers: {},
                form: new Form({
                    id:'',
                    name:'',
                    description:'',
                    status_id:'',
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/manufacturers?page=' + page)
                    .then(response => {
                        this.manufacturers = response.data;
                    });
            },
            showManufacturers() {
                this.$Progress.start();
                axios.get('/api/manufacturers').then(({ data }) => {
                    this.manufacturers = data;
                    this.$Progress.finish();
                });
            },
            addManufacturer() {
                this.editmode = false;
                this.form.reset();
                $('#addmanufacturerModal').modal('show');
            },
            createManufacturer() {
                this.form.post('/api/manufacturers')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addmanufacturerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'manufacturer created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateManufacturer() {
                this.form.put('/api/manufacturers/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addmanufacturerModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'manufacturer details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editManufacturer(manufacturer) {
                this.editmode = true;
                this.form.fill(manufacturer);
                $('#addmanufacturerModal').modal('show');
            },
            deleteManufacturer(id) {
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
                        this.form.delete('/api/manufacturers/'+id)
                            .then(() => {
                                swal.fire('Deleted!', 'Record has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                swal.fire('Not Deleted!', 'Record can not be deleted.', 'error');
                            });
                    }
                });
            },
        },
        created() {
            Fire.$on('searching', () => {
                axios.get('/api/findManufacturer?q='+this.$parent.search)
                    .then((data) => {
                        this.manufacturers = data.data;
                    })
                    .catch();
            });
            this.showManufacturers();
            Fire.$on('AfterCreate', () => {
                this.showManufacturers();
            });

            //setInterval(() -> this.showManufacturers(), 3000);
        }
    }
</script>
