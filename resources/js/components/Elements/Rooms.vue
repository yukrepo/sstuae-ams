<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Rooms List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addRoom"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-70">ID</th>
                                            <th>Location</th>
                                            <th>Room Number</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Added On</th>
                                            <th class="wf-100">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(room, ikey) in rooms.data" :key="room.id">
                                            <td>{{ ++ikey }}</td>
                                            <td>{{ room.location }}</td>
                                            <td>{{ room.room_name }}</td>
                                            <td>{{ (room.remark)?room.remark:'--' }}</td>
                                            <td>{{ room.created_at | setdate }}</td>
                                            <td align="center" v-if="room.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editRoom(room)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteRoom(room.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="rooms.total > 1">
                                <pagination class="m-0 float-right" :data="rooms" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addroomModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addroomModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateRoom() : createRoom()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addroomModalTitle">Add Room</h5>
                            <h5 class="modal-title" v-show="editmode" id="addroomModalTitle">Update Room</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="location_id" class="control-label">Location</label>
                                    <select v-model="form.location_id" name="location_id" class="form-control" :class="{ 'is-invalid': form.errors.has('location_id') }">
                                        <option disabled value="">Select Location</option>
                                        <option v-for="location in locations" :key="location.id" v-bind:value="location.id">
                                            {{ location.clinic_name }}
                                        </option>
                                    </select>
                                    <has-error :form="form" field="location_id"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="room_name" class="control-label">Name</label>
                                    <input v-model="form.room_name" type="text" name="room_name" id="room_name" placeholder="enter room name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('room_name') }">
                                    <has-error :form="form" field="room_name"></has-error>
                                </div>
                                <div class="form-group col-6">
                                    <label for="remark" class="control-label">Remark</label>
                                    <textarea v-model="form.remark" name="remark" rows="4" id="remark" placeholder="enter remark"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                    <has-error :form="form" field="remark"></has-error>
                                </div>
                                <div class="form- col-6">
                                    <label for="status_id" class="control-label">Status</label>
                                    <select v-model="form.status_id" name="status_id" id="status_id" class="form-control" :class="{ 'is-invalid': form.errors.has('status_id') }">
                                        <option disabled value="">Select Status</option>
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
                rooms: {},
                locations: {},
                form: new Form({
                    id:'',
                    room_name:'',
                    location_id:'',
                    remark:'',
                    status_id:''
                })
            }
        },
        methods: {
            setActive(menuItem) {
                this.activeMenu = menuItem; // no need for Vue.set()
                console.log(menuItem);
            },
            getLocations() {
                axios.get('/api/getLocationsList').then(({ data }) => (this.locations = data));
            },
            getResults(page = 1) {
                axios.get('/api/rooms?page=' + page)
                    .then(response => {
                        this.rooms = response.data;
                    });
            },
            showRooms() {
                this.$Progress.start();
                axios.get('/api/rooms').then(({ data }) => {
                    this.rooms = data;
                    this.$Progress.finish();
                });
            },
            addRoom() {
                this.editmode = false;
                this.form.reset();
                $('#addroomModal').modal('show');
            },
            createRoom() {
                this.form.post('/api/rooms')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addroomModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Room created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateRoom() {
                this.form.put('/api/rooms/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addroomModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Room details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editRoom(room) {
                this.editmode = true;
                this.form.fill(room);
                $('#addroomModal').modal('show');
            },
            deleteRoom(id) {
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
                        this.form.delete('/api/rooms/'+id)
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
            this.getLocations();
            this.showRooms();
            Fire.$on('AfterCreate', () => {
                this.showRooms();
            });
        }
    }
</script>
