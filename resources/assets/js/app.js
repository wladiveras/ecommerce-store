
/**
* First we will load all of this project's JavaScript dependencies which
* includes Vue and other libraries. It is a great starting point when
* building robust, powerful web applications using Vue and Laravel.
*/


import './config/setup'
import './config/introjs'
import './components/start'
import Vue from 'vue'

require('codemirror')
require('summernote')
require('./libs/summernote-ptbr')
require('./libs/summernote-ptbr')
require('datatables.net-bs4')
require('datatables.net-rowreorder-bs4')
require('datatables.net-responsive-bs4')
window.toastr = require('toastr')

import VueToastr2 from 'vue-toastr-2'
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
Vue.use(VueToastr2)

import VModal from 'vue-js-modal'
Vue.use(VModal)

import './config/element-plugins'
const vue = new Vue({
    el: '#app',
    data() {
        return {
            csrf_token: $('meta[name="csrf-token"]').attr('content'),
            root_url: $('meta[name="root-url"]').attr('content'),
            show_menu: false,
            products: [],
            request: null,
            loadingProducts: false,
            productFiltered: false
        }
    },
    computed: {
        ismobile() {
            return window.innerWidth <= 800 && window.innerHeight <= 600 ? true : false
        },
    },
    methods:
    {
        showLoginForm() {
            this.$root.closeMenu()
            this.$modal.show('form_login')
        },
        closeMenu() {
            this.$root.$refs.menu.drawer = false
        },
        async getProducts(amount = 0, query = false) {
            var result = []
            amount = (amount > 0) ? '/' + amount : ''
            query = (query != false) ? `?search=${query}` : ''
            await this.$http.get('/api/products/list' + amount + query).then(res => {
                if (typeof (res.data) == 'object') result = res.data
                this.loadingProducts = false
            }).catch(e => {
                this.loadingProducts = false
            })
            return result
        },
        findProduct() {
            this.searching = true
        },
        showMenu() {
            $(this.$refs.menu).toggleClass("active")
            $("body").css("overflow", "hidden")
            this.show_menu = true
        },
    }
})
window.vue = vue
