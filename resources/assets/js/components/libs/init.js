import Vue from 'vue';
require('./vendor.js')
Vue.component('chosen',require("./Chosen.vue").default);
Vue.component('checkbox',require("./Switch.vue").default);
Vue.component('check-circle', require("./CheckCircle.vue").default);
Vue.component('sweet-portal',require("./SweetPortal.vue").default);
Vue.component('datatable',require("./Datatable.vue").default);
Vue.component('img-loader',require("./Image.vue").default);
Vue.prototype.$scrollTo = function(id)
{
  setTimeout(() => {
    $("html").stop().animate({scrollTop:$(id).offset().top}, "slow")
  }, 200)
}

Vue.prototype.$money = {
  decimal: ',',
  thousands: '.',
  prefix: 'R$ ',
  suffix: '',
  precision: 2,
  masked: false /* doesn't work with directive */
};

import VueTheMask from 'vue-the-mask'
Vue.use(VueTheMask)

import money from 'v-money';
Vue.use(money, Vue.prototype.$money)
