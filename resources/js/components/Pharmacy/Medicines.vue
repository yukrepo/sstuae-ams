<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9 py-1">
                        <vue-search placer="search by medicine name, barcode"></vue-search>
                    </div>
                    <div class="col-md-3 text-right py-1">
                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addMedicine"> <i
                                class="fas fa-plus "></i> New medicine </button>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Medicines List</h3>
                            </div>
                            <div class="card-body p-0 table-responsive search-content-box">
                                <table id="medicine-list" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-75">ID</th>
                                            <th>Category</th>
                                            <th>Medicine Name</th>
                                            <th>Unitsize / Packsize</th>
                                            <th>Medicine Barcode</th>
                                            <th>Cash Price</th>
                                            <th>Status</th>
                                            <th class="wf-150"
                                                v-show="(profile.admin_type_id == 1) || (profile.admin_type_id == 4)">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(medicine, pID) in medicines.data" :key="pID">
                                            <td>{{ (medicines.current_page - 1) * (medicines.per_page) + pID + 1 }}</td>
                                            <td>{{ medicine.category_name }}</td>
                                            <td>{{ medicine.name }}</td>
                                            <td>{{ medicine.unitsize }} {{ medicine.unit }}</td>
                                            <td>{{ medicine.barcode }}</td>
                                            <td>{{ medicine.cash_price | formatOMR }}</td>
                                            <td>
                                                <label class="status-label bg-teal" v-if="medicine.status_id == 2"><i
                                                        class="fas fa-check"></i></label>
                                                <label class="status-label bg-pink" v-else><i
                                                        class="fas fa-times"></i></label>
                                            </td>
                                            <td v-show="(profile.admin_type_id == 1) || (profile.admin_type_id == 4)">
                                                <button class="btn btn-warning btn-sm" @click="editMedicine(medicine)"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm"
                                                    @click="deleteMedicine(medicine.id)"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :limit="10" :data="medicines"
                                    @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addMedicineModal" data-backdrop="static" data-keyboard="false"
            aria-labelledby="addMedicineModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateMedicine() : createMedicine()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addMedicineModalTitle">Add Medicine</h5>
                            <h5 class="modal-title" v-show="editmode" id="addMedicineModalTitle">Update Medicine's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="control-label">category</label>
                                        <select v-model="form.category_id" name="category_id" class="form-control"
                                            :class="{ 'is-invalid': form.errors.has('category_id') }" @change="getUnitSize">
                                            <option disabled value="">Select Category</option>
                                            <option v-for="category in categories" :key="category.id"
                                                v-bind:value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <has-error :form="form" field="category_id"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label">name</label>
                                        <input v-model="form.name" type="text" name="name" placeholder="enter Medicine name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="control-label">unitsize</label>
                                        <div class="input-group ">
                                            <input v-model="form.unitsize" type="text" name="unitsize"
                                                placeholder="packsize / unitsize" class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('unitsize') }">
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ form.unit }}</span>
                                            </div>
                                        </div>
                                        <has-error :form="form" field="unitsize"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="" class="control-label">cash price</label>
                                                <input v-model="form.cash_price" type="text" name="cash_price"
                                                    placeholder="enter cash price" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('cash_price') }">
                                                <has-error :form="form" field="cash_price"></has-error>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Status</label>
                                                <select name="status_id" id="status_id" v-model="form.status_id"
                                                    class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="2">Active</option>
                                                    <option value="1">Deactive</option>
                                                </select>
                                                <has-error :form="form" field="status_id"></has-error>
                                            </div>
                                        </div>
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
    </div>
</template>
<script>
export default {
    data() {
        return {
            editmode: false,
            categories: {},
            medicines: {},
            asearch: '',
            profile: '',
            form: new Form({
                id: '',
                name: '',
                category_id: '',
                unit: '',
                unitsize: '',
                insurance_price: 0,
                cash_price: '',
                status_id: 2
            })
        }
    },
    methods: {
        getIndex(list, id) {
            return id
        },
        getResults(page = 1) {
            axios.get('/api/medicines?page=' + page)
                .then(response => {
                    this.medicines = response.data;
                });
        },
        showMedicines() {
            this.$Progress.start();
            axios.get('/api/medicines').then(({ data }) => {
                this.medicines = data;
                this.$Progress.finish();
            });

        },
        getUnitSize() {
            axios.get('/api/categories/' + this.form.category_id).then(({ data }) => (this.form.unit = data[0]['unit']));
        },
        getCategories() {
            axios.get('/api/getCategoryList').then(({ data }) => (this.categories = data));
        },
        addMedicine() {
            this.editmode = false;
            this.form.reset();
            $('#addMedicineModal').modal('show');
        },
        createMedicine() {
            this.form.post('/api/medicines')
                .then((data) => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addMedicineModal').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'Medicine created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
        },
        updateMedicine() {
            this.form.put('/api/medicines/' + this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addMedicineModal').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'Medicine details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
        },
        editMedicine(Medicine) {
            this.editmode = true;
            this.form.fill(Medicine);
            $('#addMedicineModal').modal('show');
        },
        deleteMedicine(id) {
            swal.fire({
                title: 'Are you sure?',
                text: "It will delete all stocks related to this Medicine. You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#38c172',
                cancelButtonColor: '#e3342f',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    this.form.delete('api/medicines/' + id)
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
    beforeMount() {
        axios.get('/api/get-active-user').then(response => { this.profile = response.data; });
    },
    mounted() {
        this.getCategories();
        Fire.$on('searching', () => {
            axios.get('/api/findMedicine?q=' + this.asearch)
                .then((data) => {
                    this.medicines = data.data;
                })
                .catch();
        });
        this.showMedicines();
        Fire.$on('AfterCreate', () => {
            this.showMedicines();
        });
        //setInterval(() -> this.showMedicines(), 3000);
    }
}
</script>
