/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import store from './store'
import 'jquery-ui/ui/widgets/datepicker.js';
import Chart from 'chart.js';
window.Chart = Chart;
import Swal from 'sweetalert2'
window.Swal = Swal;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import VueRouter from 'vue-router'
import Routes from './routes'
Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: Routes,
    linkActiveClass: 'active'
})

import { currency } from './helper/currency'
Vue.filter('currency', currency)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import { mapState, mapActions, mapMutations } from 'vuex'
const app = new Vue({
    el: '#app',
    router,
    store,
    computed: {
        ...mapState('user', {
            user: state => state.user
        }),
        userPhoto() {
            return this.$store.state.user.user.photo
        },
    },
    methods: {
        ...mapActions('user', ['getUserAuth'])
    },
    async created() {
        if (window?.Laravel?.api_token != undefined) {
            await this.$store.dispatch('user/getUserAuth')
        }
    }
});
