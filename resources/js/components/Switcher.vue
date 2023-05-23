<template>
    <div>
        <div class="modal fade" id="switchModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form @submit.prevent="switchLocation()" class="m-0">
                        <div class="modal-header">
                            <h5 class="modal-title" id="switchModalModalTitle">Switch Location</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="location" class="control-label">Location</label>
                                        <select v-model="lform.location" name="location" id="location" class="form-control"  :class="{ 'is-invalid': lform.errors.has('location') }">
                                            <option value="">Select Location</option>
                                            <option :value="loc.id" v-for="(loc, lkey) in locations" :key="lkey">{{loc.clinic_name}}</option>
                                        </select>
                                        <has-error :form="lform" field="location"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Switch </button>
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
            lform: new Form ({
                location:'',
            }),
            locations:this.$store.getters.olocations
        }
    },
    methods: {
        switchLocation() {
            this.lform.post('/api/switch-admin').then(() => {
                    this.$Progress.start();
                    location.reload();
                    this.$Progress.finish();
                }).catch((err) => { console.log(err); });
        },
    },
    async mounted() {
        if(this.locations == '') {
           await this.$store.dispatch('setLocation');
           await this.$store.dispatch('setOlocation');
        }
    }
}
</script>
