<template>
  <div class="retirada_balcao-locations">
    <div class="d-flex flex-wrap row">
      <b-form-radio-group v-model="chosen" class="hide-mark">
        <template v-for="location in locations">
          <b-form-radio :key="location.id" :value="location.id" class="mb-2">
            <div class="shipping-item item rounded p-2 border" @click="togglePayment(location)">
              <div class="flex-wrap d-flex justify-content-between">
                <span class="f-15 font-weight-bold text-uppercase">{{location.razao}}</span>
                <check-circle />
              </div>
              <strong class="f-12 f-space">Endereço</strong><br>
              <small class="text-danger" v-if="location.shipping_delay">+{{location.shipping_delay}} dia útil na entrega</small>
              <div class="f-12 f-space">
                {{location.street}}, {{location.number}} - {{location.district}} - {{location.zip_code}} - {{location.state}} - {{location.city}}
              </div>
            </div>
          </b-form-radio>
        </template>
      </b-form-radio-group>
    </div>
  </div>
</template>

<script>
export default {
  props:['locations'],
  data(){
    return {
      chosen: ''
    }
  },
  methods:{
    togglePayment(location){
      this.$parent.$parent.$refs.accordionExtraData.toggle();
      this.$parent.$parent.$parent.$refs.paymentdata.$refs.accordionPayment.toggle();
      this.$emit('chosen',Object.assign({
        id:location.id,
        name:location.razao,
      },location));
    },
  }
}
</script>
