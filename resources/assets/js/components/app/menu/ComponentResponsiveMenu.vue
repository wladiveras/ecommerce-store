<template>
    <nav class="navbar navbar-expand-lg navbar-light w-100 pb-0 px-0">
        <div class="d-flex flex-row justify-content-between px-2 w-100 align-items-center mb-3">            
            <a class="navbar-brand d-flex align-items-center mx-2" href="/">
                <img
                    :src="logo"
                    style="margin-top: 6px;width: 100%;height: auto;"
                    alt="Padrãocolor"
                />
            </a>
            <div class="d-flex align-items-center pt-2">
                <button
                    class="navbar-toggler p-0"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                    @click.prevent="showMenu = !showMenu"
                >
                    <div
                        :class="`navbar-burger cursor-pointer hover-opacity hover-scale ${showMenu ? 'is-active' : ''}`"
                    >
                        <span></span>
                    </div>
                </button>
            </div>

            <div class="d-flex flex-row pt-3">
                <a
                    href="/central-de-ajuda"
                    class="menu-option cart-button col d-flex align-items-center justify-content-center hover-item px-2"
                >
                    <div class="d-flex flex-column align-items-center">
                        <i class="el-icon-question icon"></i>
                        <span class="text">Ajuda</span>
                    </div>
                </a>
                <a
                    :href="`${user ? '/logout' : '/login'}`"
                    class="menu-option cart-button col d-flex align-items-center justify-content-center hover-item px-2"
                >
                    <div class="d-flex flex-column align-items-center">
                        <i :class="`${user ? 'el-icon-error' : 'el-icon-user-solid'} icon`"></i>
                        <span class="text">{{`${user ? 'Sair' : 'Entrar'}`}}</span>
                    </div>
                </a>
                <a
                    href="/carrinho"
                    class="menu-option cart-button col d-flex align-items-center justify-content-center hover-item px-2"
                >
                    <div class="d-flex flex-column align-items-center">
                        <i class="el-icon-shopping-cart-full icon"></i>
                        <span class="text">Carrinho</span>
                    </div>
                </a>
            </div>
        </div>

        <div
            class="collapse navbar-collapse px-4"
            id="navbarSupportedContent"
            style="background-color:black;"
        >
            <ul class="navbar-nav mr-auto pt-2">
                <li
                    class="nav-item dropdown"
                    style="min-heigth:100px;"
                    v-for="(option,index) in options.options"
                    :key="`option_${index}`"
                >
                    <a
                        class="nav-link dropdown-toggle text-white"
                        href="#"
                        @click="select_option(option)"
                        id="navbarDropdown"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >{{option.name}}</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <template
                            v-for="(chunk,c) in (selected_option ? selected_option.values : [])"
                        >
                            <a
                                class="dropdown-item"
                                :href="value.url"
                                v-for="(value,v) in chunk"
                                :key="`${c}_${v}`"
                            >{{value.name}}</a>
                        </template>
                    </div>
                </li>
                <li class="mt-4">
                    <a
                        class="nav-link dropdown-toggle text-white"
                        href="#"
                        id="navbarDropdown"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >Institucional</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a
                            class="dropdown-item"
                            href="/institucional/politica-de-privacidade"
                        >Politicas de Privacidade</a>
                    </div>
                </li>
                <li class="mb-4">
                    <a
                        class="nav-link dropdown-toggle text-white"
                        href="#"
                        id="navbarDropdown"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >Midias Sociais</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a
                            class="dropdown-item"
                            href="https://www.facebook.com/padraocolor"
                        >Facebook</a>
                    </div>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a
                            class="dropdown-item"
                            href="https://www.instagram.com/padraocolor"
                        >Instagram</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>
<script>
export default {
    props: ["user", "logo"],
    data() {
        return {
            loaded: false,
            options: [],
            content_loading: false,
            selected_option: { name: null },
            showMenu: false

        }
    },
    created() {
        this.load_options()
    },
    methods: {
        load_options() {
            this.$http.post(this.$root.root_url + "/api/menu/get_menu_options", {}).then(res => {
                res = res.data
                this.options = res
                this.loaded = true
            }).catch(er => {
                this.loaded = true
                console.log(er)
            })
        },
        select_option(op) {
            if (!op) return
            if (this.selected_option.name == op.name) {
                this.selected_option.name = null
                return this.$set(op, "actual", false)
            }
            this.options.options.map(x => x.actual = (op.value == x.value) ? true : false)
            this.$set(op, "actual", true)
            this.selected_option = Object.assign({}, op)
            this.selected_option.values = op.values
        },
    },
}
</script>
<style lang="scss" scoped>
.navbar {
    background-color: transparent;
    .navbar-brand {
        img {
            height: 50px;
        }
    }
    .nav-link {
        .fixed {
            color: #639648;
        }
    }
    .menu-option {
        .icon {
            font-size: 20px;
            color: black;
        }
        .text {
            font-size: 12px;
            color: black;
        }
    }
    .navbar-light {
        .navbar-toggler {
            border-color: transparent;
            color: black !important;
            .text-white {
                color: white !important;
            }
        }
    }
    .navbar-toggler {
        &:focus {
            outline: unset;
        }
        .navbar-burger {
            margin-left: 10px;
            margin-right: 10px;
            width: 30px;
            &:after,
            &:before,
            span {
                background-color: black;
                border-radius: 3px;
                content: "";
                display: block;
                height: 5px;
                margin: 7px 0;
                transition: all 0.2s ease-in-out;
            }
            &.is-active {
                &:before {
                    transform: translateY(12px) rotate(135deg);
                }
                &:after {
                    transform: translateY(-12px) rotate(-135deg);
                }
                span {
                    transform: scale(0);
                }
            }
        }
    }
}
</style>
