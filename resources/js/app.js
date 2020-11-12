/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
// Vue.mixin(require('./trans.js')) //langguage

import VueRouter from 'vue-router'
import { myRoutes } from './routes.js';
import { userInfo } from 'os';

Vue.use(VueRouter)
//routes
let router = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'is-active',
    routes: myRoutes
});
//alert
import Swal from 'sweetalert2'
window.Swal = Swal;
const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
})
window.toast = toast;

//fire
window.Fire = new Vue();

//vform
import {Form, HasError, AlertError } from 'vform';
window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

//date picker
import DatePicker from 'vue2-datepicker';

//pagination
Vue.component('pagination', require('laravel-vue-pagination'));

//progresBar 
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
    color: 'rgb(50, 227, 23)',
    failedColor: 'red',
    height: '3px'
})

//v-money
import money from 'v-money'
Vue.use(money, {precision: 0})

//toogle
import ToggleButton from 'vue-js-toggle-button'
import Axios from 'axios';
 
Vue.use(ToggleButton)

//pdf

//vue countdown


//vue moment for time
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
 
Vue.use(VueMoment, {
    moment,
})
//scrol otomatis
import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)



const app = new Vue({
    el: '#app',
    router,
    data: {
        searchData: '',
        reset: false,
    },
    methods: {
        searchAll(){
            Fire.$emit('searchDataOnPage');
            this.reset = true
        },
        resetData(){
            Fire.$emit('resetDataOnPage');
            this.reset = false
        },
    }
});
