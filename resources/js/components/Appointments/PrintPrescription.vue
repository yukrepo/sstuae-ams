<template>
    <div>
        <div class="content">
            <div class="container text-center p-b-25">
                <div class="text-right border-bottom m-b-10 p-b-10 pt-2">
                    <button class="btn btn-lg wf-250 btn-secondary" @click="viewlink"><i class="fas fa-link"></i> Get
                        Sharable Link</button>
                    <button class="btn btn-lg wf-250 btn-green-theme" @click="sharelink"><i class="fas fa-share"></i> Share
                        On whatsapp</button>
                    <button class="btn btn-lg wf-250 btn-dark-theme" @click="printpage"><i class="fas fa-print"></i> Take
                        Print Now</button>
                </div>
                <div id="printDiv">
                    <header class="clearfix">
                        <div class="container">
                            <figure>
                                <img class="logo" :src="imgurl + 'big-logo.png'" alt="">
                            </figure>
                            <div class="company-address">
                                <h2 class="title">{{ configs.company_name }}</h2>
                                <ul>
                                    <li class="text-left">
                                        <img class="social" :src="imgurl + 'domain.png'" alt=""> {{ configs.website }}
                                    </li>
                                    <li class="text-right">
                                        <img class="social" :src="imgurl + 'facebook.png'" alt=""> {{ configs.facebook_page }}
                                    </li>
                                    <li class="text-right">
                                        <img class="social" :src="imgurl + 'instagram.png'" alt=""> {{ configs.instagram_page
                                        }}
                                    </li>
                                    <li class="text-right">
                                        <img class="social" :src="imgurl + 'whatsapp.png'" alt=""> {{ configs.whatsapp_number
                                        }}
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </header>

                    <section class="bodybox">
                        <div class="container introbox">
                            <h2>Medical Prescription</h2>
                            <div class="row">
                                <div class="col-4">
                                    <p class="bottom-bordered">
                                        <b>PATIENT ID :</b> {{ customer.username }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <p class="bottom-bordered">
                                        <b>NAME :</b> {{ customer.first_name | capitalize }} {{ customer.last_name |
                                            capitalize }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <p class="bottom-bordered">
                                        <b>AGE :</b> {{ getAge(customer.date_of_birth) }}
                                        <span v-if="customer.gender == 1"> (Male) </span>
                                        <span v-else-if="customer.gender == 2"> (Female) </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <p class="bottom-bordered m-0">
                                        <b>REF BY :</b> {{ appointment.doctor | capitalize }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <p class="bottom-bordered m-0">
                                        <b>DATE :</b> {{ appointment.date | setdate }}
                                    </p>
                                </div>
                            </div>
                            <hr class="hline">
                            <div class="row">
                                <div class="col-4">
                                    <h4 class="subtitle">SYMPTOMS</h4>
                                    <p class="bottom-bordered" v-for="(symptom, skey) in JSON.parse(appointment.symptoms)"
                                        :key="'s-' + skey">
                                        <b>{{ symptom.name }} </b> {{ symptom.remark }}
                                    </p>
                                </div>
                                <!--     <div class="col-4">
                                    <h4 class="subtitle">GENERAL EXAMINATIONS</h4>
                                    <p class="bottom-bordered" v-for="(oe_category, okey) in JSON.parse(appointment.oe_categories)" :key="'o-'+okey">
                                        <b>{{ oe_category.name }} </b> {{ oe_category.result }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <h4 class="subtitle">DIAGNOSIS</h4>
                                    <p class="bottom-bordered" v-for="(diagnosiss, dkey) in JSON.parse(appointment.diagnosis)" :key="'d-'+dkey">
                                        <b>{{ diagnosiss.code }} - {{ diagnosiss.name }} : </b>  {{ diagnosiss.remark }}
                                    </p>
                                </div> -->
                            </div>
                            <hr class="hline">
                            <div class="row">
                                <!-- <div class="col-6">
                                    <h4 class="subtitle">LAB TESTS</h4>
                                    <p class="bottom-bordered" v-for="(test, tekey) in JSON.parse(appointment.tests)" :key="'te-'+tekey">
                                        <b>{{ test.type }} - {{ test.name }} : </b> {{ test.remark }}
                                    </p>
                                </div> -->
                                <div class="col-12">
                                    <h4 class="subtitle">THERAPIES</h4>
                                    <div v-if="JSON.parse(appointment.therapies)">
                                        <p class="bottom-bordered"
                                            v-for="(therapy, tkey) in JSON.parse(appointment.therapies)" :key="'t-' + tkey">
                                            <b>{{ therapy.days }} {{ therapy.name }} : </b> {{ therapy.remark }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <p>No therapies prescribed</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="hline">
                            <div>
                                <h4 class="subtitle">MEDICINES</h4>
                                <p class="bottom-bordered" v-for="(medicine, mkey) in JSON.parse(appointment.medicines)"
                                    :key="'m-' + mkey">
                                    <b>{{ medicine.name }}</b> | {{ medicine.dtab }} | {{ medicine.remark }} | For {{
                                        medicine.days | freeNumber }} days | {{ medicine.qty | freeNumber }} Packs

                                </p>
                            </div>
                            <hr class="hline">
                            <div>
                                <p class="bottom-bordered">
                                    <b>Followup : </b> {{ (appointment.reminder_days) ? appointment.reminder_days + ' days' : '--'
                                    }}
                                </p>
                                <p class="bottom-bordered">
                                    <b>Recommandations : </b> {{ (appointment.dr_remark) ? appointment.dr_remark : '--' }}
                                </p>
                                <div class="signbox">
                                    <p>Doctor Sign &amp; Stamp</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer>
                        <p class="thanks">
                            Location : {{ configs.location }}<br>
                            Address: {{ configs.address }}<br>
                            Contact: Tel : {{ configs.contact }}, Fax : {{ configs.fax }}, email : {{ configs.email }}
                        </p>
                    </footer>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false"
            aria-labelledby="confirmModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Sharable Link
                        </div>
                        <div class="modal-body text-center">
                            <h4 class="m-b-20 m-t-20">Copy the link below and share it with respective person.</h4>
                            <code class="border d-block mb-4">
                                    {{ 'https://srisritattva.me/panchakarma/print/view-prescription/' + sharable_link }}
                                </code>
                        </div>
                        <div class="modal-footer text-center d-block">
                            <button type="button" class="btn btn-lg btn-success m-0" data-dismiss="modal"> Done ! </button>
                        </div>
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
            docurl: '/files/docs/',
            imgurl: '/images/',
            editmode: false,
            catchmessage: '',
            currentYear: new Date().getFullYear(),
            activeID: '',
            active_location: '',
            appointment: '',
            listSlots: [],
            customer: {},
            medicines: [],
            profile: '',
            configs: '',
            sharable_link: '',
            wlink: ''
        }
    },
    methods: {
        getIndex(list, id) {
            return list;
        },
        printpage() {
            this.$htmlToPaper('printDiv');
        },
        getProfile() {
            axios.get('/api/get-configs').then(response => { this.configs = response.data; });
        },
        getAge(date) {
            let today = new Date();
            let ndate = new Date(date);
            var a = moment([today.getFullYear(), today.getMonth(), today.getDate()]);
            var b = moment([ndate.getFullYear(), ndate.getMonth(), ndate.getDate()]);
            var diffDuration = moment.duration(a.diff(b));
            return diffDuration.years() + ' yrs';
        },
        sharelink() {
            let slink = 'Please find your prescription on the link below -  https://srisritattva.me/panchakarma/print/view-prescription/' + this.sharable_link;
            let rpath = '//wa.me/' + this.customer.mobile + '?text=' + encodeURI(slink);
            let route = this.$router.resolve({ path: rpath });
            window.open(route.href, '_blank');
        },
        viewlink() {
            $('#confirmModal').modal('show');
        },
        showAppointment() {
            this.$Progress.start();
            axios.get('/api/appointments/view/' + this.activeID).then(({ data }) => {
                this.appointment = data[0];
                axios.get('/api/customers/' + this.appointment.user_id)
                    .then((data) => { this.customer = data.data[0] })
                    .catch();
                this.$Progress.finish();
                this.sharable_link = window.btoa(this.activeID + '-' + this.appointment.user_id);
            });
        },
    },
    beforeMount() {
        this.getProfile();
        let activeId = this.$route.path.split("/");
        this.activeID = activeId[3];
    },
    mounted() {
        this.showAppointment();
        //this.getAppointmentHistory();
        //this.getAllAssets();
        //this.defineSlots();
    }

}
</script>
