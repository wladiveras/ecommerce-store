<template>
    <div>
        <div class="d-flex flex-wrap">
            <order-card :order="order" :statusList="status_list" :steps="steps" />
        </div>
        <div class="d-flex flex-wrap">
            <shippping-address-card :order="order" />
            <shipping-card :order="order" :shipping="shipping" />
        </div>
        <div class="d-flex flex-wrap">
            <billing-address-card :order="order" />
            <payment-card :order="order" :statusPaymentList="status_payment_list" />
        </div>
        <div class="d-flex flex-wrap">
            <div class="col-12 px-0 mb-3">
                <div class="card detail-card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="col-md-8 col-12 pl-0">
                                <h4 class="title d-flex align-items-center">
                                    <i class="material-icons icon mr-2">shopping_cart</i>
                                    Pedidos
                                </h4>
                            </div>
                            <div v-if="order.skus.length>1" class="col-md-4 col-12 text-right pr-0">
                                <template v-if="grouped">Agrupado</template>
                                <template v-else>Listagem Simples</template>
                                <el-switch
                                    class="white ml-2"
                                    v-model="grouped">
                                </el-switch>
                            </div>
                        </div>
                    </div>
                    <order-item :canResendart="can_resendart" :grouped="grouped" :order="order" :statusItemList="status_item_list" :canCancel="can_cancel"/>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-end">
            <order-total :order="order" />
        </div>
    </div>
</template>
<script>
export default {
    props : ["order","status_list","status_payment_list","status_item_list","can_cancel","can_resendart","steps", "shipping"],
    data() {
        return {
            grouped : false
        }
    },
    components : {
        "order-card" : require("./-OrderCard.vue").default,
        "shipping-card" : require("./-OrderShipping.vue").default,
        "shippping-address-card" : require("./-ShippingAddress.vue").default,
        "payment-card" : require("./-PaymentCard.vue").default,
        "billing-address-card" : require("./-BillingAddress.vue").default,
        "order-item" : require("./-OrderItem.vue").default,
        "order-total" : require("./-OrderTotal.vue").default,
    }
}
</script>