<style scoped>
.icon-close2 {
    font-weight: 400;
    font-size: 18px;
}
.icon-close {
    color: rgb(214, 227, 73);
    font-size: 33px;
}
.dark {
    background-color: #313131;
    color: #d6e349;
}
.all-products {
    width: auto !important;
}
</style>
<template>
    <div id="navbarResponsive" class="collapse navbar-collapse menu-default">
        <div
            class="w-100 d-flex align-content-stretch flex align-items-center"
            style="max-height:121px;"
        >
            <div
                class="all-products d-none d-md-flex no-select menu-btn-start dark"
                @click="closeMenu"
                style="padding-left:183px;"
            >
                <div class="d-flex align-items-center">
                    <i class="material-icons icon-close">close</i>
                    <div class="text d-flex flex-wrap">
                        <span class="tiny align-self-start">FECHAR</span>
                        <span class="large align-self-baseline">PRODUTOS</span>
                    </div>
                </div>
            </div>
            <div class="menu-md d-flex w-100" style="margin-right: 150px;">
                <a
                    class="navbar-brand d-none h-100 d-md-flex align-items-center w-100"
                    :href="$root.root_url"
                >
                    <img src="/assets/images/logo.png" alt="Padrãocolor" />
                </a>
                <span
                    class="d-flex align-items-center icon-close2 float-right mr-5"
                    @click="closeMenu"
                >
                    FECHAR
                    <i class="material-icons ml-2">close</i>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [],
    data() {
        return {

        }
    },
    methods:
    {
        closeMenu() {
            $(this.$root.$refs.menu).toggleClass("active")
            $("body").css("overflow", "scroll")
            this.$root.show_menu = false
        }
    }
}
</script>
