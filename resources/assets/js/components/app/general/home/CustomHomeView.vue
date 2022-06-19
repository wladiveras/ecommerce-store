<template>
    <div class="d-flex flex-column home-template">
        <!-- <img src="assets/images/banner-home.png" alt="" style="max-width:100%"> -->
        <!-- <div class="top-banner-div"></div> -->
        <mobile-carousel :banners="slider_mobile" />
        <banner-mobile v-if="mobile" :mobiles="mobile" />
        <banner-carousel :banners="banners" />
        <!-- <banner-trio v-if="trio" :trio="trio" /> -->
        <div class="d-flex justify-content-center">
            <div class="col-sm-6 col-md-2 mb-2"><img src="assets/images/icon-frete.jpg" alt="" style="max-width:100%"></div>
            <div class="col-sm-6 col-md-2 mb-2"><img src="assets/images/icon-desconto.jpg" alt="" style="max-width:100%"></div>
            <div class="col-sm-6 col-md-2 mb-2"><img src="assets/images/icon-parcele.jpg" alt="" style="max-width:100%"></div>
            <div class="col-sm-6 col-md-2 mb-2"><img src="assets/images/icon-compra-segura.jpg" alt="" style="max-width:100%"></div>
        </div>
        {{cUser}}
    </div>
</template>
<script>
export default {
    data() {
        return {
            route: null,
        };
    },
    props: ['banners', 'user', 'config', 'trio', 'mobile', 'slider_mobile'],
    components: {
        "mobile-carousel": require("./partials/-mobile-carousel.vue").default,
        "banner-carousel": require("./partials/-banners-carousel.vue").default,
        "banner-mobile": require("./partials/-banners-mobile.vue").default,
        "banner-trio": require("./partials/-banners-trio.vue").default,
    },
    mounted() {
        if (JSON.parse(localStorage.getItem('step')) && JSON.parse(localStorage.getItem('user')) && JSON.parse(localStorage.getItem('route')) && JSON.parse(localStorage.getItem('shared'))){
            const route = JSON.parse(localStorage.getItem('route'))
            localStorage.removeItem('route')
            window.location.href = route
        } else {
            localStorage.removeItem('shared')
        }
    },
    computed: {
        cUser() {
            localStorage.setItem('user', JSON.stringify(this.user ? true : false));
            return '';
        }
    },
}
</script>
