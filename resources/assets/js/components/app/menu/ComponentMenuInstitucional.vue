<template>
    <div class="d-flex flex-row">
        <a
            @click.prevent="closeDrawer"
            style="border: unset;background-color: transparent;color: #fff;font-weight: 600; cursor:pointer"
            class="btn d-flex align-items-center btn-secondary dropdown-toggle all-product-btn pr-3"
        >
            Menu
        </a>
        
        <el-drawer :visible.sync="drawer2" direction="ltr" :show-close="false">
            <template slot="title">
                <img
                    src="/assets/images/logo-criafacil.png"
                    class="w-100 px-5"
                    style="width:100px"
                    alt="Padrãocolor"
                />
            </template>
            <div class="d-flex flex-column">
                <strong class="mt-3 drawer-strong">Institucional</strong>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Sobre Nós</a>                
                <a class="icon-drawer" href="/institucional/politica-de-privacidade" @click="drawer2 = false">Política de Privacidade</a>
                <a class="icon-drawer" href="/institucional/termos-de-servico" @click="drawer2 = false">Termos de Serviço</a>
                <a class="icon-drawer" href="/institucional/contatos" @click="drawer2 = false">Contatos</a>                                

                <strong class="mt-3 drawer-strong">Informações</strong>
                <a class="icon-drawer" href="/central-de-ajuda" @click="drawer2 = false">Central de Ajuda</a>
                <a class="icon-drawer" href="/institucional/tutoriais" @click="drawer2 = false">Tutoriais</a>                
                <a class="icon-drawer" href="/institucional/downloads" @click="drawer2 = false">Pontos de retirada</a>
                <a class="icon-drawer" href="/central-de-ajuda/montagem-de%20arquivo:%20informa%C3%A7%C3%B5es%20gerais" @click="drawer2 = false">Montagem de Arte</a>                
                
                <strong class="mt-3 drawer-strong">Conteúdo</strong>
                <a class="icon-drawer" href="/glossario" @click="drawer2 = false">Glossário</a>
                <a class="icon-drawer" href="https://www.instagram.com/padraocolor" target="_blank">Instagram</a>
                <a class="icon-drawer" href="https://www.facebook.com/padraocolor"  target="_blank">Facebook</a>

                <strong class="mt-3 drawer-strong">Utilidades</strong>                
                <a class="icon-drawer" href="/central-de-ajuda/envio-de arquivos" @click="drawer2 = false">Montagem de arquivo</a>                
                <a class="icon-drawer" href="/institucional/downloads" @click="drawer2 = false">Guia de Impressão</a>                
                <a class="icon-drawer" href="/central-de-ajuda/gabaritos/como-efetuar-download-dos-gabaritos" @click="drawer2 = false">Gabaritos</a>                

                <strong class="mt-3 drawer-strong">Comercial</strong>                
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Seja um fornecedor</a>                
            </div>
        </el-drawer>

        
    </div>
</template>
<script>
export default {
    props: ["icon", "highlights"],
    data() {
        return {
            drawer2: false,
            drawer: false,
            loaded: false,
            options: [],
            content_loading: false,
            selected_option: null
        }
    },
    mounted() {
        if (!this.loaded) this.load_options()
        if (this.drawer) this.select_option(this.options[0])
        document.getElementById("prevent_close_menu").addEventListener('click', event => event.stopPropagation())
    },
    methods: {
        getPageUrl(page) {
            window.location.href = `${this.$root.root_url}/institucional/${page.slug}`
        },        
        showMenu() {
            this.drawer = !this.drawer
            if (!this.loaded) this.load_options()
            if (this.drawer) this.select_option(this.options[0])
        },
        closeDrawer() {
            this.drawer2 = !this.drawer2
        }
    },
}
</script>
<style lang="scss" >
.drawer-strong {
    font-size: 17px;
    font-weight: 700;
}
.el-drawer.ltr {
    width: 300px !important;
}
.v-modal {
    margin-top: 0 !important;
}
.el-drawer__header {
    padding-top: 20px;
    overflow: hidden;
    background-color: #7d1756;
}
.el-drawer__body {
    background-color: #7d1756;
    color: white;
    padding-left: 40px;
    padding-right: 40px;
    padding-top: 30px;
    overflow: auto;
    .icon-drawer {
        color: rgb(250, 224, 224);
        padding-left: 10px;
        transition: 0.7s;
        &:hover {
            color: white;
        }
    }
}
.all-product-btn::after {
    display: none;
}

.all-products {
    cursor: pointer;
}
.v-modal {
    margin-top: 80px;
}


</style>