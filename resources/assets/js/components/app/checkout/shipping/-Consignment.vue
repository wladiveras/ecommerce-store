<template>
  <div id="order-consignment">
    <b-card no-body class="mb-3 shipping-method">
      <b-card-header
        role="tab"
        v-b-toggle.consignments
        v-bind:class="{ 'section-completed': completed }"
      >
        <i class="mi mi-check" v-if="completed"></i>
        <span class="ml-2 f-18">Envio para o cliente</span>
      </b-card-header>
      <b-collapse
        visible
        id="consignments"
        role="tabpanel"
        accordion="checkout"
        ref="accordionConsignmentMethod"
      >
        <b-card-body>
          <div class="col-sm-12">
            <div class="row mb-4" >
              <b><h4>Sua Logo Etiqueta</h4></b>
              <user-logo class="w-100 logo-upload" v-on:has-logo="updateLogo">
                <div class="el-upload__text">Nós ainda não temos a imagem para sua etiqueta. <em>Envie ela aqui</em></div>
              </user-logo>
            </div>
            <div class="row mb-4">
              <b><h4>Informações da Remessa</h4></b>
            </div>
            <div class="row mb-4">
              <div class="col-4 px-0 col-sm-6 font-weight-bold">Trabalho</div>
              <div class="col-4 px-0 col-sm-3 font-weight-bold">Preço de compra</div>
              <div class="col-4 px-0 col-sm-3 font-weight-bold">Preço para o cliente</div>
            </div>
              <template v-for="(item, index) in consignment">
              <div class="row my-3" :key="index" :ref="index">
                <div class="col-4 px-0 col-sm-6">{{item.job_name}}</div>
                <div class="col-4 px-0 col-sm-3">{{item.real_price.currency()}}</div>
                <div class="col-4 px-0 col-sm-3">
                  <money class="form-control" v-model.lazy="item.clientPrice"/>
                </div>
              </div>
            </template>
            <div class="row mt-4">
              <b-button :disabled="!valid" @click="proceed" block variant.success>Continuar</b-button>
            </div>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>
  </div>
</template>
<script>
import UserLogo from '../../user/-LogoUpload.vue'
export default {
  components:{
    'user-logo': UserLogo
  },
  props: ["form", "shared"],
  data() {
    return {
      consignment: [],
      cart: {},
      valid:false,
      hasLogo:false,
    };
  },
  created() {
      this.cart = this.shared.cart
  },
  methods: {
    updateLogo(v){
      this.hasLogo = v
      this.validate()
    },
    validate(event){
      let valid = true
      for(let k in this.consignment){
        if(this.consignment[k].client_price < 1){
          valid = false
        }
      }
      if(!this.hasLogo) valid = false
      this.valid = valid
    },
    createConsignment() {
      if(this.form.shipping.consignment.length) return;

      for (let index in this.cart) {
        let item = this.cart[index]
        var self = this
        let cartItem = {
          group: index,
          job_name: item.name,
          real_price: item.price,
          client_price: "",
          set clientPrice(v){
            this.client_price = v
            self.validate()
          },
          get clientPrice(){
            return this.client_price
          }
        }
        this.consignment.push(cartItem)
      }
      this.$set(this.form.shipping, "consignment", this.consignment);
      //this.form.shipping.consignment = this.consignment;
    },
    proceed(){
      this.shared.step = 'shippingmethod';
      this.$parent.$refs.shippingmethod.$refs.accordionShippingMethod.toggle();
    }
  },
  computed: {
    completed() {
      return this.consignment.length
    }
  },
  watch:{
    'shared.step'(n){
      if(n == 'consignment'){
        this.createConsignment();
      }
      if(this.form.shipping.option != 'client'){
        this.consignment = []
        this.form.shipping.consignment = this.consignment
      }
    }
  }
};
</script>
<style lang="scss">
  #order-consignment{
    .logo-upload{
      padding: inherit;
    }
  }
</style>