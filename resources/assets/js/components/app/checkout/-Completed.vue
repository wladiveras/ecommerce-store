<template>
<div class="container" id="order-complete">
    <div class=" row d-flex justify-content-center flex-column align-items-center">

        <template v-if="shared.data.payment">
            <img src="/svg/checkout/Complete.svg" class="svg complete-svg mb-3">
            <p class="f-32 text-primary text-center">PEDIDO REALIZADO COM SUCESSO</p>
        </template>
        <template v-else>
            <img src="/svg/checkout/Failed.svg" class="svg complete-svg mb-3">
            <p class="f-32 text-danger text-center">SEU PEDIDO FOI REALIZADO, MAS A TRANSAÇÃO NÃO PÔDE SER EFETUADA</p>
        </template>
        <p class="f-14 g-5 text-center">{{shared.data.message}}</p>
        <p class="f-22 g-5 font-weight-bold f-space mb-4">CÓDIGO DO PEDIDO - {{shared.order.code}}</p>
        <div v-if="shared.data.payment_data.data.payment.bankslip" class="mb-3">
            <p><a target="_blank" :href="shared.data.payment_data.data.payment.bankslip.html" class="link">Imprimir o boleto bancário</a></p>
        </div>
        <div class="mb-4 d-flex justify-content-center flex-column flex-md-row">
            <a href="/compras" class="btn btn-secondary f-space text-white mb-3 mr-0 mr-md-2">Acompanhar Pedidos</a>
            <a href="/produtos" class="btn btn-primary f-space text-secondary mb-3 ml-0 ml-md-2">Continuar Comprando</a>
        </div>
        <p><a href="#">Politica de compra</a> • <a href="#">Politica de cancelamento</a></p>
    </div>
</div>
</template>

<script>
export default {
    props: {
        shared: null
    },
    mounted() {
        if (this.shared.data.payment_data.data.payment.bankslip) {
            this.downloadBankSlip("data:text/html,boleto!");
        }
    },
    methods: {
        downloadBankSlip() {
            var a = document.createElement("a");
            a.href = this.shared.data.payment_data.data.payment.bankslip.pdf;
            a.setAttribute("download", this.shared.order.code + ".pdf");
            var b = document.createEvent("MouseEvents");
            b.initEvent("click", false, true);
            a.dispatchEvent(b);
            return false;
        }
    }
}
</script>
