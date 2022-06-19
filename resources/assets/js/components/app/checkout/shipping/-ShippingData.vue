<style lang="scss">
#checkout-page .shipping-data .custom-control label .item {
  padding: 5px 10px;
  margin-bottom: 5px;
}
#checkout-page .shipping-data .custom-control > input[type="radio"]:checked + label > .item, #checkout-page .shipping-data .custom-control > input[type="checkbox"]:checked + label > .item {
  border: 2px solid #7d1756 !important;
}

.card.shipping-data {
    background: transparent;
    border: none;
    .custom-control.custom-control-inline.custom-radio:nth-child(3),
    .custom-control.custom-control-inline.custom-radio:first-child {
      display: none;
    }
    input[disabled="disabled"].custom-form-checkout {
        border: none;
        background: transparent;
        color: #000;
    }
}
</style>
<template>
  <div id="order-complete">
    <b-card no-body class="shipping-data">
      <div v-b-toggle.shippingdata v-bind:class="{ 'section-completed': completed }">
        <!-- <i class="mi mi-check" v-if="completed"></i> -->
        <h4 class="d-flex align-items-baseline">Entrega ou Retirada <img src="/assets/images/icon-shipping-checkout.jpg" alt="" style="max-width: 100%;"></h4>
      </div>
      <b-collapse visible id="shippingdata" role="tabpanel" ref="accordionShipping">
        <b-card-body>
          <div class="row mb-3 hide-mark">
            <b-form-radio-group :class="{'justify-content-between': changeFormation}" v-model="form.shipping.type" >
              <template v-for="(shipping, i) in shared.shippingTypes">
                <b-form-radio :key="i" :value="i" v-show="shipping.rawPrice !== null">
                  <div class="item d-sm-flex border rounded" @click="togglePayment(i,shipping.option)">
                    <div class="d-sm-flex flex-wrap align-items-center">
                      <strong class="f-15 ">{{shipping.name}}</strong>
                      <template v-if="shipping.price.length">
                        <span class="text-muted f-14 ml-1">( {{shipping.price}} )</span>
                      </template>
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
<style lang="scss">
  .space-between{
    justify-content: space-between!important;
    >*{
      margin-right: 0px!important;
    }
  }
</style>
<script>
export default {
  props: {
    form:null,
    shared: {}
  },
  data(){
    return {
      shippingTypes: this.shared.shippingTypes,
    }
  },
  mounted()
  {

  },
  methods: {
    togglePayment(type,option = 'normal')
    {
      this.form.payment.confirmed = false;
      this.form.shipping.type = type
      if(type === "retirada_balcao"){
        this.form.shipping.id = "H"
        console.log("TYPE"+type)
      }
      this.form.shipping.option = option
      this.form.payment.type = "creditcard"
      this.shared.shippingPrice = this.shared.shippingTypes[type].rawPrice;
      this.form.shipping_address = {};
      this.shared.step = type;
      this.shared.extraStep = type;
      if(type == 'client_shipping'){
        this.shared.extraStep = 'shipping'
      }
      //this.$refs.accordionShipping.toggle();
      if(type == 'payment'){
        this.form.shipping_address = this.shared.user.address;
        this.$parent.$refs.paymentdata.$refs.accordionPayment.toggle();
      }else{
        //this.$parent.$refs.extradata.$refs.accordionExtraData.toggle();
      }
    }
  },
  computed:{
    completed(){
      return this.form.shipping.type.length
    },
    changeFormation(){
      let data = Object.keys(this.shared.shippingTypes).length;
      return !(data % 3);
    }
  }
}
</script>
