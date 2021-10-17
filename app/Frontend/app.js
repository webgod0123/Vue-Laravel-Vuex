import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './App.vue';
import vuetify from './Plugins/vuetify.js'
import * as moment from 'moment'
import VueMobileDetection from 'vue-mobile-detection'

import Store from "./Store.ts";
import Routes from './Router.ts'

Vue.use(VueMobileDetection)

// disable warning about double click on same route
const originalPush = VueRouter.prototype.push;
VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => {
        if(err.name !== 'NavigationDuplicated') {
            throw err
        }
    });
}

//router
Vue.use(VueRouter)
let router =  new VueRouter(Routes);
router.beforeEach(function(to, from, next) {
    Store.commit('clearError');
    let user = Store.state.user;
    if((user === null || user.id == 0) && to.name !== 'login')  {
        next({ path: '/login' });
    } else {
        next();
    }
});

// global vue stuff
Vue.filter('humanDate', function (value) {
    if(!value) {
        return '';
    }
    return moment(value).format('DD.MM.YYYY');
});

const app = new Vue({
    vuetify,    
    el: '#app',
    store: Store,
    router: router,
    render(h) {
        return h(App);
    },
})