<template>
    <div class="d-flex flex-row">
        <!-- <a
            @click.prevent="closeDrawer"
            style="border: unset;background-color: transparent;color: #4a4a4a;;font-weight: 600;"
            class="btn d-flex align-items-center btn-secondary dropdown-toggle all-product-btn pr-3"
        >
            <i
                :class="`${drawer2 ? 'el-icon-s-fold mr-2' : 'el-icon-s-unfold mr-2'}`"
                style="font-size:25px;"
            ></i>MENU
        </a> -->
        <el-drawer :visible.sync="drawer2" direction="ltr" :show-close="false">
            <template slot="title">
                <img
                    src="/assets/images/logo-white.png"
                    class="w-100 px-5"
                    style="width:100px"
                    alt="Padrãocolor"
                />
            </template>
            <div class="d-flex flex-column">
                <strong class="mt-3 drawer-strong">Informações</strong>
                <a class="icon-drawer" href="/institucional/politica-de-privacidade" @click="drawer2 = false">Tutoriais</a>
                <a class="icon-drawer" href="/institucional/politica-de-privacidade" @click="drawer2 = false">Downloads</a>
                <a class="icon-drawer" href="/lancamentos" @click="drawer2 = false">Lançamentos</a>
                <strong class="mt-3 drawer-strong">Institucional</strong>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Sobre Nós</a>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Política de Privacidade</a>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Contatos</a>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Material de apoio COVID</a>
                <strong class="mt-3 drawer-strong">Conteúdo</strong>
                <a
                    class="icon-drawer"
                    href="https://www.instagram.com/padraocolor"
                    target="_blank"
                >Instagram</a>
                <a
                    class="icon-drawer"
                    href="https://www.facebook.com/padraocolor"
                    target="_blank"
                >Facebook</a>
                <strong class="mt-3 drawer-strong">Utilidades</strong>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Projetos Especiais</a>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Compra Rápida</a>
                <a class="icon-drawer" href="/institucional/sobre-nos" @click="drawer2 = false">Manual de Envio de Arquivos</a>                
            </div>
        </el-drawer>

        <a
            @click.prevent="$root.closeMenu"
            style="border: unset;background-color: transparent;color: #fff;font-weight: 600;"
            class="btn d-flex align-items-center btn-secondary dropdown-toggle all-product-btn pr-3"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Produtos
        </a>
        <!-- <a class="icon-drawer" href="/projetos_especiais">Projetos Especiais</a> -->
        
        <div
            id="prevent_close_menu"
            class="prevent_close_menu dropdown-menu p-2"
            style="margin-top: 65px;border-radius: 20px; width: 1090px;"
        >
            <div class="menu-content" v-loading="!loaded">                
                <div class="op-list d-flex">
                    
                    <a
                        href="#"
                        class="op-item mb-2"
                        style="cursor:pointer;"
                        v-bind:class="{'selected' : option.actual,'non-selected' : !option.actual}"
                        v-for="(option,i) in options"
                        :key="i"
                        @mouseover.prevent="select_option(option)"
                    >
                    <div class="op-item-container"></div>
                    <div class="op-item-title-div">
                        <div style="background: white; margin-top: 3px;">{{option.name}}</div>
                    </div>
                    </a>
                </div>
                <div class="option-values flex-column flex-grow-1">
                    <!-- <h4 class="value-title mb-3">{{selected_option ? selected_option.name : null}}</h4> -->
                    <div class="d-flex flex-row value-list h-100" v-loading="content_loading">
                        <div
                            class="d-flex flex-column value-column h-100"
                            v-loading="content_loading"
                            v-for="(chunk,i) in (selected_option ? selected_option.values : [])"
                            :key="i"
                        >
                            <div
                                class="mb-3 d-flex flex-column value-item"
                                v-for="(value,y) in chunk"
                                :key="y"
                            >
                                <a :href="value.url">{{value.name}}</a>
                                <small v-if="value.department">{{value.department}}</small>
                            </div>
                        </div>
                        <!-- <a href="/institucional/catalogos-de-precos" class="button-catalogo">CATALOGO DE PREÇOS</a> -->
                    </div>
                </div>
                <div style="display: flex;justify-content: center;">
                    <a href="/institucional/catalogos-de-precos-lancamentos" target="_blank" class="button-gradient">Catálogo de Lançamentos</a>
                    <a href="/institucional/catalogos-de-precos" target="_blank" class="button-gradient">Tabela de Preços</a>
                </div>
            </div>            
            
        </div>
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
        load_options() {
            this.$http.post(this.$root.root_url + "/api/menu/get_menu_options", {}).then(res => {
                res = res.data
                this.options = res.options
                this.loaded = true
                if (this.options.length > 0) this.select_option(this.options[0])
            }).catch(er => {
                this.loaded = true
                console.log(er)
            })
        },
        select_option(op) {
            if (!op) return
            this.options.map(x => x.actual = (op.value == x.value) ? true : false)
            this.$set(op, "actual", true)
            this.selected_option = op
            this.selected_option.values = op.values
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
.el-drawer.ltr {
    width: 300px !important;
}
.v-modal {
    margin-top: 0 !important;
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
.menu-content {
    display: flex;
    .option-values {
        padding-left: 55px;
        .value-title {
            color: #1b4e01;
            font-weight: bold;
            font-size: 30px;
            font-weight: 500;
        }
        .value-list {
            .value-column {
                margin-right: 20px;
                min-width: 180px;
                padding-right: 20px;
            }
            .value-item {
                font-size: 14px;
                font-weight: 400;
                cursor: pointer;
                opacity: 0.7;
                a {
                    font-weight: 500;
                    color: black;
                }
                small {
                    font-weight: 500;
                    color: gray;
                }
                &:hover {
                    transition: 0.5s;
                    -webkit-transform: scale(1.08);
                    transform: scale(1.08);
                    opacity: 1;
                }
            }
        }
    }
    .op-list {
        padding-right: 0px;
        padding-top: 10px;
        padding-bottom: 20px;
        min-width: 220px;
        width: 200px;
        margin-bottom: 30px;
        flex-direction: column;
        border-right: 1px solid #7d1756;
        .subtitle {
            color: #1b4e01;
            font-weight: bold;
            font-size: 18px;
            font-weight: 500;
            border-bottom: 1px solid #1b4e01;
        }
        .op-item {
            margin: 0 3px;
            padding: 3px;
            position: relative;
            &.non-selected {
                color: black;
                font-weight: 500;
                opacity: 1;
                font-size: 15px;
                padding: 3px;
                display: flex;       
                &:hover {
                    transition: 0.5s;
                    -webkit-transform: scale(1.08);
                    transform: scale(1.08);
                    opacity: 1;
                }
            }
            &.selected {
                color: #1b4e01;
                font-weight: bold;
                font-size: 18px;
                font-weight: 500;
                margin-top: 10px;
                padding-top: 3px;
                display: flex;
                margin-right: -2px;
                border-top: 1px solid #7d1756;  
                border-radius: 10px 0 0 10px;
                border-left: 1px solid #7d1756;
                border-bottom: 1px solid #7d1756;
                border-right: 2px solid #fff;

                .op-item-title-div {
                    font-size: 13px;
                    text-align: center;
                    color: #2e2e2e;
                    margin-top: 10px;                    
                    padding-top: 3px;                    
                    margin-left: -3px;
                }
            }
            .op-item-title-div {
                    font-size: 13px;
                    text-align: center;
                    color: #2e2e2e;
                    margin-top: 10px;                    
                }
                
            
        }
        .op-item.container {
            font-size: 13px;
            text-align: center;
            color: #2e2e2e;
            &.non-selected {
                background: #cccccc;             
            }
            &.selected {
                background:  linear-gradient(to right, red, purple);
            }
        
        }
    }
    
}

</style>