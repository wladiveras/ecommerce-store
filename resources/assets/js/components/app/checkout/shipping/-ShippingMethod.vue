<style lang="scss">
#checkout-page .shipping-method .custom-control label .item {
  padding: 5px 10px;
  margin-bottom: 5px;
}

.card.shipping-method {
    background: transparent;
    border: none;
}
</style>
<template>
  <div id="order-complete">
    <b-card no-body class="mb-3 shipping-method">
      <!-- <b-card-header role="tab" v-b-toggle.shippingmethod v-bind:class="{ 'section-completed': completed }">
        <i class="mi mi-check" v-if="completed"></i>
        <span class="ml-2 f-18">MÃ©todo de Entrega</span>
      </b-card-header> -->
      <span class="f-18">Escolha o MÃ©todo de Entrega</span>
      <b-collapse visible id="shippingmethod" role="tabpanel" ref="accordionShippingMethod">
        <b-card-body v-loading="isLoading" element-loading-text="Calculando fretes...">
          <div class="row mb-3 hide-mark">
            <b-form-radio-group v-model="form.shipping.method" class="">
              <template v-for="(shipping, i) in shippingMethods">
                <b-form-radio :value="i" :key="i">
                  <div class="item d-sm-flex border rounded" @click="togglePayment(shipping.rawPrice)">
                    <div class="d-sm-flex flex-wrap align-items-center">
                      <strong class="f-18 ">{{shipping.name}}</strong>
                      <span class="text-muted f-14 ml-1">( {{shipping.price}} )</span>
                      <span class="text-muted f-12 flexb-12">{{shipping.time}}</span>
                    </div>
                  </div>
                </b-form-radio>
              </template>
            </b-form-radio-group>
          </div>
          <slot></slot>
        </b-card-body>
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
export default {
  props: {
    form:null,
    shared:{},
    shippingMethods: [Array, Object],
    isLoading:Boolean
  },
  data() {
    return {
    }
  },
  computed:{
    completed(){
      return this.form.shipping.method >= 0
    }
  },
  methods: {
    togglePayment(price){
      this.form.payment.confirmed = false;
      this.shared.step = 'payment';
      this.shared.shippingPrice = price;

    //   ANTES ESTAVA
    //   this.$parent.$refs.paymentdata.$refs.accordionPayment.show();
      this.$parent.$refs.paymentdata.$refs.accordionPayment.show;
    }
  }
}
</script>
