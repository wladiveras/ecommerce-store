<style scoped lang="scss">
.btn{
  &:hover {
    color:white!important;
  }
}
.card.resume {
    border: 1px solid #00acb7;
    border-radius: 8px;
    .card-header {
        background-color: #00acb7;
        color: #fff;
    }
}
</style>
<template>
<div class="container" id="order-complete">
    <b-card class="resume">
        <template slot="header">
            <span class="f-18">Resumo</span>
        </template>
        <div class="d-flex justify-content-between">
            <strong>Subtotal</strong>
            <span>{{fSubtotal}}</span>
        </div>
        <div class="d-flex justify-content-between">
            <template v-if="form.shipping.method>=0">
                <span><strong>Frete</strong> <small v-if="form.shipping.type>=0">  {{ selected_shippingType_name }} </small></span>
                <span v-if="form.shipping.type!='payment'">{{shippingPrice}}</span>
                <span v-else>Custo do Frete</span>
            </template>
        </div>
        <div class="d-flex justify-content-between" v-if="form.shipping_address.name">
            <strong>{{(shared.extraStep == 'withdrawal')? 'Retirada' : 'Entregar para'}}: </strong>
            <span>{{form.shipping_address.name}}</span>
        </div>
        <template slot="footer">
            <div class="d-flex justify-content-between">
                <strong>Total</strong>
                <span>{{fTotal}}</span>
            </div>
        </template>
    </b-card>
    <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-block btn-finish-custom" v-bind:class="{ 'shaking': form.payment.confirmed }" v-if="form.payment.confirmed" @click="finish">FINALIZAR PEDIDO</button>
    </div>
</div>
</template>

<script>
export default {
    props: {
        shared: null,
        form: null,
        _subtotal: null,
        _route_store_order: null
    },
    computed: {
        selected_shippingType_name() {
            return this.$parent.$refs.shippingdata.shippingTypes[this.form.shipping.type].name;
        },
        fSubtotal() {
            return this._subtotal.currency();
        },
        shippingPrice() {
            let price = this.shared.shippingPrice
            if (!price && price !== 0)
                return "N/A";
            if (price === 0)
                return "Grátis";
            return price.currency();
        },
        fTotal() {
            let price = this.shared.shippingPrice;
            if (!price)
                return this.fSubtotal;
            let total = Number(this._subtotal) + Number(price);
            return total.currency();
        },
        loadingMessage() {
            if (this.form.payment.type == "creditcard") {
                return "Aguarde, processando transação ...";
            }
            if (this.form.payment.type == "bankslip") {
                return "Gerando boleto ...";
            }
            return "Processando...";
        },
    },
    mounted() {
        // console.log(this.$parent.$refs.shippingdata)
    },
    methods: {
        finish() {
            if(!this.form.address.number)
                return this.$parent.$parent.breakInUpdateResellerAddres()
            let loading = this.$loading({
                lock: true,
                text: this.loadingMessage,
                background: 'rgba(0, 0, 0, 0.7)'
            });
            this.$http.post(this._route_store_order, this.form).then((res)=>{
                res = res.data
                if (!res.success) {
                    loading.close();
                    return this.$toastr.error(res.message);
                }
                window.location.href = "/checkout/thank-you"
                //this.shared.completed = true;
                //this.shared.order = response.data.order;
                //this.shared.data = response.data;
                return loading.close();
            }).catch(()=> {
                loading.close();
            });
        }
    }
}
</script>
