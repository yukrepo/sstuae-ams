<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Comments List
                                    <div class="card-tools">
                                        <button class="btn btn-green-theme btn-sm mr-1" type="button" @click="addComment"><i class="fas fa-plus fa-fw"></i></button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body p-0 content-box-180">
                                <table id="stock-alert" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">SNo</th>
                                            <th>Comment</th>
                                            <th style="width: 100px;">Added On</th>
                                            <th style="width: 50px;">Status</th>
                                            <th style="width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(comment, count) in comments.data" :key="comment.id">
                                            <td>{{ (comments.current_page - 1)*(comments.per_page) + count + 1 }}</td>
                                            <td>{{ comment.comment }}</td>
                                            <td>{{ comment.created_at | setdate }}</td>
                                            <td align="center" v-if="comment.status_id == 2">
                                                <label class="status-label bg-teal"><i class="fas fa-check"></i></label>
                                            </td>
                                            <td align="center" v-else>
                                                <label class="status-label bg-pink"><i class="fas fa-times"></i></label>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" @click="editComment(comment)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" @click="deleteComment(comment.id)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" v-if="comments.total > 1">
                                <pagination class="m-0 float-right" :limit="10" :data="comments" @pagination-change-page="getResults" :show-disabled="true">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addcommentModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="addcommentModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateComment() : createComment()">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addcommentModalTitle">Add Comment</h5>
                            <h5 class="modal-title" v-show="editmode" id="addcommentModalTitle">Update Comment's Info</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="comment" class="control-label">Comment</label>
                                    <input v-model="form.comment" type="text" name="comment" id="comment" placeholder="enter comment"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('comment') }">
                                    <has-error :form="form" field="comment"></has-error>
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
                comments: {},
                form: new Form({
                    id:'',
                    comment:'',
                    status_id:''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('/api/comments?page=' + page)
                    .then(response => {
                        this.comments = response.data;
                    });
            },
            showComments() {
                this.$Progress.start();
                axios.get('/api/comments').then(({ data }) => {
                    this.comments = data;
                    this.$Progress.finish();
                });
            },
            addComment() {
                this.editmode = false;
                this.form.reset();
                $('#addcommentModal').modal('show');
            },
            createComment() {
                this.form.post('/api/comments')
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcommentModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Comment created successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            updateComment() {
                this.form.put('/api/comments/'+this.form.id)
                .then(() => {
                    this.$Progress.start();
                    Fire.$emit('AfterCreate');
                    $('#addcommentModal').modal('hide');
                    toast.fire({
                        type:'success',
                        title:'Comment details updated successfully'
                    });
                    this.$Progress.finish();
                })
                .catch(() => {

                });
            },
            editComment(comment) {
                this.editmode = true;
                this.form.fill(comment);
                $('#addcommentModal').modal('show');
            },
            deleteComment(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38c172',
                    cancelButtonColor: '#e3342f',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.comment) {
                        this.form.delete('/api/comments/'+id)
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
            this.showComments();
            Fire.$on('AfterCreate', () => {
                this.showComments();
            });
        }
    }
</script>
