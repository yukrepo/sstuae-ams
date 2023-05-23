require('./bootstrap');
window.Vue = require('vue');

import { Form, HasError, AlertError } from 'vform';
import VueRouter from 'vue-router';
import moment from 'moment';
import numeral from 'numeral';
import swal from 'sweetalert2';
import VueAutosuggest from "vue-autosuggest";
import Vue2Filters from 'vue2-filters';
import VueImg from 'v-img';
import datePicker from 'vue2-datepicker';
import momentTimezone from 'moment-timezone';
import VueProgressBar from 'vue-progressbar';
import { ModelSelect } from 'vue-search-select';
import VueHtmlToPaper from 'vue-html-to-paper';
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
import Toaster from 'v-toaster';
import JsonExcel from "vue-json-excel";
import Vuex from 'vuex';
import storeData from './store';
import { ToggleButton } from 'vue-js-toggle-button'
import './global-components'

Vue.component('vp-date-picker', VuePersianDatetimePicker);

import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import 'v-toaster/dist/v-toaster.css';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import Vue from 'vue';

momentTimezone.tz.setDefault('Asia/Muscat');
Vue.prototype.$momentTimezone = momentTimezone;

require('vue-axios-interceptors');

Vue.use(datePicker);
Vue.use(VueImg);
Vue.use(Vue2Filters);
Vue.use(VueRouter);
Vue.use(VueAutosuggest);
Vue.component('ToggleButton', ToggleButton);
Vue.component('model-select', ModelSelect);
Vue.use(Toaster, {timeout: 5000});
window.Toaster = Toaster;

window.swal = swal;

Vue.use(require('vue-moment'));

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast = toast;

Vue.use(VueHtmlToPaper, {
                        name: '_blank',
                        specs: ['fullscreen=yes', 'titlebar=no', 'scrollbars=no'],
                        styles: ['/css/print.css']
                    });


window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component("JsonExcel", JsonExcel);
Vue.component('pagination', require('laravel-vue-pagination'));

Vue.use(VueProgressBar, {
    color: '#06A23F',
    failedColor: '#D91E18',
    thickness: '5px',
    location: 'bottom',
});

window.Fire = new Vue();

Vue.filter('setdate', function(mydate){
    return moment(mydate).format('DD.MM.YYYY');
});
Vue.filter('validdate', function(mydate){
    return moment(mydate).format('YYYY/MM/DD');
});
Vue.filter('setFulldate', function(mydate){
    moment.locale('en');
    return moment(mydate).format('DD-MM-YYYY hh:mm A');
});
Vue.filter('setfulldate', function(mydate){
    moment.locale('en');
    return moment(mydate).format('DD-MM-YYYY hh:mm A');
});
Vue.filter('dbdate', function(mydate){
    return moment(mydate).format('YYYY-MM-DD');
});
Vue.filter('titledate', function(mydate){
    moment.locale('en');
    return moment(mydate).format('ddd - Do MMM, YYYY');
});
Vue.filter('titlemonth', function(mydate){
    moment.locale('en');
    return moment(mydate).format('MMM YYYY');
});
Vue.filter("formatNumber", function (value) {
    return numeral(value).format("0,0.00");
});
Vue.filter("formatOMR", function (value) {
    return numeral(value).format("0,0.00");
});
Vue.filter("freeNumber", function (value) {
    return numeral(value).format("0,0");
});

let routes = [];

const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});

const store = new Vuex.Store(storeData);

const app = new Vue({
    el: '#app',
    store,
    router,
    data: {
        invoice_types:['Unknown','Consultation','Treatment','Insurance Medicine','Cash Medicine','Course Package','Prescribed Course', 'Direct Medicine','Direct Course','Direct Invoice'],
        invoice_type_labels:['text-warning','text-dark','text-primary','text-dark','text-primary','text-primary','text-dark','text-dark', 'text-secondary','text-dark'],
        payment_modes: {'cash':'Cash','credit':'Credit','epay':'E-Payment','visa':'VISA Card','cash+visa':'Cash + VISA'},
        payment_status:['Pending','Credit','Partial','Paid'],
        payment_css:['bg-pink','bg-primary','bg-purple','bg-success'],
        currency:'AED '
    },
    mounted() {
        if(this.$store.getters.user == '') {
            this.$store.dispatch('setUser');
        }
        if(this.$store.getters.configs == '') {
            this.$store.dispatch('setConfig');
        }
        if(this.$store.getters.locations == '') {
            this.$store.dispatch('setLocation');
        }
        if(this.$store.getters.yearList == '') {
            this.$store.dispatch('setYearlist');
        }
    },
});
