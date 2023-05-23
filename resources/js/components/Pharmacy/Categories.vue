<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Categories List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addCategory"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="wf-75">SNo</th>
                                            <th>Name</th>
                                            <th>Unit</th>
                                            <th>Remark</th>
                                            <th class="wf-100">Medicines</th>
                                            <th class="wf-75">Status</th>
                                            <th class="wf-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(category, count) in categories.data" :key="category.id">
                                            <td>{{ (categories.current_page - 1)*(categories.per_page) + count + 1 }}</td>
                                            <td>{{ category.name }}</td>
                                            <td>{{ category.unit }}</td>
                                            <td>{{ (category.remark) ? category.remark : '--' }}</td>
                                            <td>{{ category.medicines_count }}</td>
                                            <td align="center" v-if="category.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"> <i class="fas fa-times"></i> </label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editCategory(category)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteCategory(category.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination class="m-0 float-right" :data="categories" @pagination-change-page="getResults" :show-disabled="true">
                                    <span slot="prev-nav"><i class="fas fa-angle-double-left fa-fw"></i></span>
	                                <span slot="next-nav"><i class="fas fa-angle-double-right fa-fw"></i></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addcategoryModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addcategoryModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateCategory() : createCategory()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addcategoryModalTitle">Add Category</h5>
                            <h5 class="modal-title" v-show="editmode" id="addcategoryModalTitle">Update Category's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="control-label">Category Name</label>
                                <input v-model="form.name" type="text" name="name" placeholder="enter category name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="unit" class="control-label">Unit</label>
                                <input v-model="form.unit" type="text" name="unit" placeholder="enter unit"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('unit') }">
                                <has-error :form="form" field="unit"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="control-label">remark</label>
                                <textarea v-model="form.remark" name="remark" placeholder="enter remark" class="form-control" :class="{ 'is-invalid': form.errors.has('remark') }"></textarea>
                                <has-error :form="form" field="remark"></has-error>
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
                categories: {},
                form: new Form({
                    id:'',
                    name:'',
                    unit:'',
                    remark:'',
                    status_id:'',
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/categories?page=' + page)
                    .then(response => {
                        this.categories = response.data;
                    });
            },
            showCategories() {
                this.$Progress.start();
                axios.get('/api/categories').then(({ data }) => {
                    this.categories = data;
                    this.$Progress.finish();
                });
            },
            addCategory() {
                this.editmode = false;
                this.form.reset();
                $('#addcategoryModal').modal('show');
            },
            createCategory() {
                this.form.post('/api/categories')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcategoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'category created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateCategory() {
                this.form.put('/api/categories/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcategoryModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'category details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editCategory(category) {
                this.editmode = true;
                this.form.fill(category);
                $('#addcategoryModal').modal('show');
            },
            deleteCategory(id) {
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
                        this.form.delete('/api/categories/'+id)
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
                axios.get('/api/findCategory?q='+this.$parent.search)
                    .then((data) => {
                        this.categories = data.data;
                    })
                    .catch();
            });
            this.showCategories();
            Fire.$on('AfterCreate', () => {
                this.showCategories();
            });

            //setInterval(() -> this.showCategories(), 3000);
        }
    }
</script>
