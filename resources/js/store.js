import createPersistedState from "vuex-persistedstate";
import axios from 'axios'

export default {
    state:{
        notificaitons:'',
        comments:'',
        locations:'',
        olocations:'',
        yearList:'',
        configs:'',
        user:'',
        invoices:''

    },
    getters:{
        notificaitons(state){
            return state.notificaitons;
        },
        comments(state){
            return state.comments;
        },
        locations(state){
            return state.locations;
        },
        olocations(state){
            return state.olocations;
        },
        yearList(state) {
            return state.yearList;
        },
        configs(state){
            return state.configs;
        },
        user(state){
            return state.user;
        },
    },
    mutations:{
        SET_NOTIFICATION(state, val){
            state.notificaitons = val;
        },
        SET_COMMENT(state, val){
            state.comments = val;
        },
        SET_LOCATION(state, val){
            state.locations = val;
        },
        SET_OLOCATION(state, val){
            state.olocations = val;
        },
        SET_YEARLIST(state, val){
            state.yearList = val;
        },
        SET_CONFIG(state, val){
            state.configs = val;
        },
        SET_USER(state, val){
            state.user = val;
        }
    },
    actions: {
        setNotificaitons({commit}, notificaitons) {
            commit("SET_NOTIFICATION", notificaitons);
        },
        setComment({commit}, comments) {
            commit("SET_COMMENT", comments);
        },
        setOlocation: async (context,payload) => {
            let { data } = await axios.get('/api/getNonactiveLocationsList')
            context.commit('SET_OLOCATION',data);
        },
        setLocation : async (context,payload) => {
            let { data } = await axios.get('/api/getLocationsList')
            context.commit('SET_LOCATION',data);
        },
        setYearlist : async (context,payload) => {
            let { data } = await axios.get('/api/getPrevYearsList')
            context.commit('SET_YEARLIST',data);
        },
        setUser: async (context,payload) => {
            let { data } = await axios.get('/api/get-active-user');
            context.commit('SET_USER',data);
        },
        setConfig : async (context,payload) => {
            let { data } = await axios.get('/api/get-configs');
            context.commit('SET_CONFIG',data);
        },
    },
    plugins: [createPersistedState({ storage: window.sessionStorage })]
};
