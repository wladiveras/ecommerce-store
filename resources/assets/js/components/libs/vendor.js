import Vue from 'vue';

/*
  OTHER LIBS
*/
// bootstrap vue
require("bootstrap/dist/js/bootstrap.min.js");
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);

// vue-toasted

import Toasted from 'vue-toasted';
Vue.use(Toasted,{
  theme:'outline',
  position: "top-center",
  duration: 3000,
  //iconPack: "fontawesome",
  action : {
    text : '',
    icon: 'mi-close',
    onClick : (e, toastObject) => {
      toastObject.goAway(0);
    }
  },
})
