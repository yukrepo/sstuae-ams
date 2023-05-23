<template>
    <div>
         <div class="content">
            <div class="container">
                <div class="form-group text-right">
                    <button class="btn btn-sm btn-dark-theme" @click="printpage"><i class="fas fa-print"></i> Take Print Now</button>
                </div>
                <hr>
                <div class="container text-center p-b-25">
                    <div id="printDiv">
                        <header class="clearfix">
                            <div class="container">
                                <figure>
                                    <img class="logo" :src="imgurl+'big-logo.png'" alt="">
                                </figure>
                                <div class="company-address">
                                    <h2 class="title">{{  configs.company_name }}</h2>
                                        <ul>
                                        <li class="text-left">
                                            <img class="social" :src="imgurl+'domain.png'" alt=""> {{ configs.website }} </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'facebook.png'" alt=""> {{ configs.facebook_page }}
                                        </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'instagram.png'" alt=""> {{ configs.instagram_page }} </li>
                                        <li class="text-right">
                                            <img class="social" :src="imgurl+'whatsapp.png'" alt=""> {{ configs.whatsapp_number }} </li>
                                    </ul>
                                </div>
                            </div>
                        </header>

                        <section class="bodybox">
                            <div class="container introbox">
                                <h2 v-if="activeType == 'ar'" style="text-align:center; direction:rtl">الموافقة على الخضوع لعلاج الايورفيدا</h2>
                                <h2 v-else>Consent to Ayurvedic Medicine and treatment</h2>

                                <div v-if="activeType == 'ar'" style="text-align:right; padding-right:15px; direction:rtl">
                                    رقم المريض -  <b class="px-2 border-bottom"> {{ customer.username }} </b>
                                </div>
                                <div v-else style="text-align:right; padding-right:15px;">
                                    SSTP ID -  <b class="px-2 border-bottom"> {{ customer.username }} </b>
                                </div>
                            </div>
                        </section>
                        <section class="text-left" style="padding: 0 25px; text-align:justify">
                            <div v-if="activeType == 'ar'" style="direction:rtl; text-align:right">
                                <p>وعليه، أقر بما يلي:</p>
                                <p>لقد تمت مناقشة جميع المعلومات المتعلقة بالأدوية/ المكملات العشبية للأيورفيدا، والعلاجات، والمعلومات المتعلقة بالعلاج المحدد، والآثار السلبية المحتملة والتفاعلات الدوائية التي يتم توفيرها من قبل طبيب الأيورفيدا في سري سري تاتفا بانشاكارما عُمان.</p>
                                <p>وأقر كذلك بأنني أخضع لعلاجات الأيورفيدا بملئ إرادتي وليس كنتيجة لأي إغراء أو وعود يقدمها ممارس الأيورفيدا أو أي شخص/ موظف آخر في سري سري تاتفا بانشاكارما عُمان . وأنني أدرك أيضًا أن هذه الاستشارة/ العلاج لا تحل محل الرعاية الطبية لبعض الحالات الطارئة/ التي تهدد الحياة.</p>
                                <p>أرغب في المضي قدمًا بحرية وطواعية في العلاج الأيورفيدي وأفوض الممارس/ المعالجين الأيورفيديين في سري سري تاتفا بانشاكارما عُمان للمضي قدمًا في مثل هذا العلاج بموافقة كاملة من جانبي على جميع الحقائق ذات الصلة كما هو موضح في نموذج الموافقة هذا. تنطبق هذه الموافقة على علاجاتي الأولية وكل علاجات الأيورفيدا اللاحقة.</p>
                                <p>لقد قرأت المعلومات الواردة أعلاه ، أو تم قرأتها لي بلغتي.</p>
                                <p>أنا أفهم هذه المعلومات.</p>
                                <p>
                                    <span style="width:120px; display:inline-block" class="align-bottom">توقيع المريض: </span>
                                    <img :src="'/files/consents/'+customer.sign" style="height:100px">
                                </p>
                                <p class="m-0">
                                    <span style="width:120px; display:inline-block" class="d-inline-block"> اسم المريض:</span>
                                    <span class="border-bottom"> {{ customer.first_name+' '+customer.last_name}} </span>
                                </p>
                                <p>
                                    <span style="width:120px; display:inline-block" class="d-inline-block"> التاريخ:</span>
                                    <span class="border-bottom">{{ customer.sign_date | setdate }} </span>
                                </p>
                            </div>
                            <div v-else>
                                <p>I hereby acknowledge the following:</p>
                                <p>The information concerning the Ayurveda Herbal medicines/supplements, therapies, treatment are discussed, and information related to the specific therapy, possible adverse effects and drug interactions are provided by Ayurveda doctor in SRI SRI TATTVA PANCHAKARMA OMAN. </p>
                                <p>I further acknowledge that I am not seeking or undergoing Ayurvedic treatments as a result of any inducement or representation or promises made by the Ayurvedic practitioner or any other person/Staff in the SRI SRI TATTVA PANCHAKARMA OMAN.</p>
                                <p>I also understand that this consultation/therapy are not intended to replace medical care for certain emergency/ life-threatening conditions. </p>
                                <p>I wish to proceed freely and voluntarily with AYURVEDIC treatment and authorize AYURVEDIC PRACTIONER/THERAPISTS in SRI SRI TATTVA PANCHAKARMA OMAN to proceed with such treatment with the full and informed consent on my part of all the relevant facts as set forth in this consent form. This consent shall apply to my initial and all subsequent Ayurvedic treatments.</p>
                                <p>I have read the above information, or have had it read to me in my own language.</p>
                                <p>I understand this information.</p>
                                <p>
                                    <span style="width:120px; display:inline-block" class="align-bottom">Patient’s Signature: </span>
                                    <img :src="'/files/consents/'+customer.sign" style="height:100px">
                                </p>
                                <p class="m-0">
                                    <span style="width:120px; display:inline-block" class="d-inline-block"> Patient’s Name:</span>
                                    <span class="border-bottom"> {{ customer.first_name+' '+customer.last_name}} </span>
                                </p>
                                <p>
                                    <span style="width:120px; display:inline-block" class="d-inline-block"> Date:</span>
                                    <span class="border-bottom">{{ customer.sign_date | setdate }} </span>
                                </p>
                            </div>


                        </section>


                        <footer>
                            <p style="text-align:center; color: #800"><i>This Document is digitally signatured by the Customer.</i></p>
                            <div class="container clearfix">
                                <p class="thanks">
                                    Location : {{ configs.location }}<br>
                                    Address: {{ configs.address }}<br>
                                    Contact: Tel : {{ configs.contact }}, Fax : {{ configs.fax }}, email : {{ configs.email }}
                                </p>
                            </div>
                        </footer>
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
                bus: new Vue(),
                svgurl: '/svg/',
                docurl: '/files/docs/',
                imgurl: '/images/',
                editmode: false,
                catchmessage: '',
                activeID: '',
                activeType:'',
                customer: {},
                configs:'',
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
            getCustomer() {
                this.$Progress.start();
                axios.get('/api/customers/'+this.activeID).then(({ data }) => {
                    this.customer = data[0];
                    this.$Progress.finish();
                });
            },
        },
        beforeMount() {
            let activeId = this.$route.path.split("/");
            this.activeID = activeId[3];
            this.activeType = activeId[4];
            this.getProfile();
        },
        mounted() {
            this.getCustomer();

        }
    }
</script>
