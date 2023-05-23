<template>
    <div>
        <div class="content bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full-width-form">
                            <form @submit.prevent="updateOptions()">
                                <div class="full-form-header">
                                    <h3 class="full-form-title">Configurations
                                        <div class="card-tools">
                                           <!--  <button type="submit" class="btn btn-block btn-sm btn-primary upper-btn">Submit</button> -->
                                        </div>
                                    </h3>
                                </div>
                                <div class="full-form-body">
                                    <div class="row customer-div">
                                        <div :class="option.inputsize" v-for="(option, index) in options" :key="index">
                                            <div class="form-group">
                                                <label class="control-label">{{ option.name.replace('_', ' ').replace('_', ' ').replace('_', ' ') }}</label>
                                                <input :type="option.input_type" v-model="option.value" class="form-control" :value="option.value" readonly :class="[(option.input_type == 'checkbox')?'wf-30':'' ]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                svgurl: '/svg/',
                docurl: 'files/docs/',
                options: {},
                form: new Form({

                })
            }
        },
        methods: {
            getOptions() {
                this.$Progress.start();
                axios.get('/api/options').then(({ data }) => {
                    this.options = data;
                    this.$Progress.finish();
                });
            },
            updateOptions() {
                this.form.post('/api/options')
                .then(() => {
                    this.$Progress.start();
                    this.$Progress.finish();
                }).catch(() => {

                });
            },
        },
        created() {
            this.getOptions();
        }
    }
</script>
