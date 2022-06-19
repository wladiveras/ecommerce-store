<template>
<div id="listadesejos-view">
    <div class="row" v-if="_notempty">
        <div class="col-md-9 mb-3 mb-md-0 view_content">
            <b-card class="cart-card">
                <template slot="header">
                    <div class="d-flex justify-content-md-between flex-column flex-md-row align-items-center">
                        <h3 class="f-18 header-title mb-2 mb-md-0">Sua Lista de Desejos</h3>
                        <div class="d-none d-lg-block">
                            <div class="d-flex">
                                <b-button variant="g-2" class="btn-delete mr-2" @click="clear"><span class="g-6 f-12">Limpar Carrinho <i class="mi mi-delete"></i> </span> </b-button>
                                <b-button variant="blue" class="f-12" @click="download">Baixar Orçamento</b-button>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="items-list">
                    <cart-item v-for="(item, key) in cart" :item="item" :key="key" :index="key" />
                </div>
            </b-card>
        </div>
        <!-- <div class="col-md-3 finish_content" >
            <summary-card :_cart="cart" />
            <b-button class="mt-3 btn-block" variant="secondary" @click="toCheckout">Finalizar Compra</b-button>
        </div> -->
    </div>
    <div class="row mb-5 mt-5" v-else>
        <div class="col-sm-12 text-center">
            <h1>Não há itens no carrinho</h1>
            <h3><a class="text-primary" href="/">Voltar à loja</a> </h3>
        </div>
    </div>
    <div class="row d-lg-none">
        <div class="col-12">
            <b-button variant="blue" class="f-12 btn-block mb-3" @click="download">Baixar Orçamento</b-button>
            <b-button variant="g-2" class="btn-delete btn-block" @click="clear"><span class="g-6 f-12">Limpar Carrinho <i class="mi mi-delete"></i> </span> </b-button>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ['_cart', 'user', '_notempty'],
    components: {
        'summary-card': require("./-CartSummary.vue").default,
        'cart-item': require("./-CartItem.vue").default
    },
    
    data() {
        return {
            cart: this._cart.item,
            group_list: []
        }
    },
    methods: {
        clear() {
            window.location = "/carrinho/limpar";
        },
        download() {
            window.open("/orcamento", '_blank');
        },
        toCheckout() {
            window.location = "/checkout";
        }
    }
}
</script>

<style scoped lang="scss">
    @media only screen and (max-width: 640px)  {
        .finish_content {
            order:1;
            margin-bottom : 20px;
        }
        .view_content {
            order:2;
        }
    }
    
</style>
