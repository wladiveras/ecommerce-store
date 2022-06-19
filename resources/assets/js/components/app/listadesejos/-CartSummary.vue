<template>
<b-card>
    <template slot="header">
        <div style="height:38px" class="d-flex justify-content-between flex-row align-items-center">
            <h3 class="f-18 header-title">Resumo</h3>
        </div>
    </template>

    <div class="d-flex justify-content-between">
        <strong>Subtotal</strong>
        <span>{{subTotal.currency()}}</span>
    </div>
    <div v-if="false" class="d-flex justify-content-between">
        <span><strong>Entrega</strong></span>
        <span>Grátis</span>
    </div>

    <template slot="footer">
        <div class="d-flex justify-content-between">
            <strong>Total</strong>
            <span>{{subTotal.currency()}}</span>
        </div>
    </template>
</b-card>
</template>

<script>
export default {
    props: ['_cart'],
    data() {
        return {
            cart: this._cart
        };
    },
    computed: {
        subTotal() {
            try {
                let total = 0;
                for(let i in this._cart) {
                    total += Number(this._cart[i].price);
                }
                return total;
            } catch (e) {
                return 0;
            }
        }
    },
    methods: {

    }
}
</script>
