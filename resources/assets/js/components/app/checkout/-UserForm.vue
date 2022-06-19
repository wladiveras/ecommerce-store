<template>
  <div id="checkout-page">
    <div class="row">
      <div class="col-sm-9">
        <div role="tablist">
          <div class="row">
            <div class="col-md-6">
              <user-data :shared="shared" :form="form"></user-data>
              <address-data ref="addressdata" :shared="shared" :form="form"></address-data>
            </div>
            <div class="col-md-6">
              <shipping-data
              :loading="gettingShippingTypes"
              :shared="shared"
              :form="form"
              ref="shippingdata"
              ></shipping-data>
              <extra-data
                v-on:chosen="getShippingPrices"
                v-show="['shipping','withdrawal','retirada_balcao'].includes(shared.extraStep)"
                :shared="shared"
                :form="form"
                ref="extradata"
              />
              <!-- <consignment-card
                v-show="form.shipping.consignment.length || (shared.step == 'consignment' && shared.extraStep == 'shipping')"
                :shared="shared"
                :form="form"
                ref="consignment"
              /> -->
              <shipping-method
                :is-loading="gettingPrices"
                :shipping-methods="shippingMethods"
                v-show="shared.step == 'shippingmethod' || (shared.step == 'payment' && shared.extraStep == 'shipping')"
                :shared="shared"
                :form="form"
                ref="shippingmethod"
              />
            </div>
          </div>          
          
          <payment-data
            v-show="shared.step == 'payment'"
            :shared="shared"
            :form="form"
            ref="paymentdata"
            :_cards="_cards"
            :_subtotal="_subtotal"
          ></payment-data>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="row">
          <summary-data
            :form="form"
            :_subtotal="_subtotal"
            :_route_store_order="_route_store_order"
            :shared="shared"
          ></summary-data>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    shared: {},
    _user: null,
    _cards : [],
    _route_store_order: null,
    _subtotal: null
  },
  data() {
    return {
      form: {
        shipping: {
          id:'',
          type: -1,
          option:'',
          consignment: []
        },
        payment: {
          data: {
            card_id : 0,
            number: "",
            expiring_date: "",
            name: "",
            installment: "1",
            cvv: "",
            documentNumber: "",
            documentType: "",
            phoneNumber: "",
            email: "",
            save_card : false
          },
          confirmed: false,
          type: null,
          address: null,
        },
        address: this._user.address,
        shipping_address: {
          name: ''
        },
      },
      shippingMethods: [{
        name: "Normal",
        time: "até 5 dias úteis",
        price: "Carregando",
        rawPrice: 0
      },
      {
        name: "Rápida",
        time: "até 3 dias úteis",
        price: "Carregando",
        rawPrice: 0
      },
      {
        name: "Expressa",
        time: "até 2 dias úteis",
        price: "Carregando",
        rawPrice: 0
      },
      ],
      gettingPrices: false,
      gettingShippingTypes: false,
    }
  },
  components: {
    'user-data': require('./basic/-UserData.vue').default,
    'address-data': require('./basic/-AddressData.vue').default,
    'shipping-data': require('./shipping/-ShippingData.vue').default,
    'summary-data': require('./-Summary.vue').default,
    'payment-data': require('./payment/-FormPayment.vue').default,
    'extra-data': require('./shipping/-ExtraData.vue').default,
    'shipping-method': require('./shipping/-ShippingMethod.vue').default,
    'consignment-card': require(`./shipping/-Consignment.vue`).default
  },
  created() {
    this.loadData();
  },
  methods: {
    getShippingPrices() {
      console.log(this.form.shipping.type)
      if(this.form.shipping.type == "retirada_balcao"){
        let url = `/api/fretePickup`;
        this.form.shipping.id = "H"
        this.gettingPrices = true;
        this.$http.post(url, this.form.shipping_address)
          .then(function (res) {
            this.shippingMethods = res.data.calcs;
            this.gettingPrices = false;
          }.bind(this))
          .catch(function (res) {
            return this.$toastr.error("Não foi possível calcular o frete, tentando novamente...");
            this.gettingPrices = false;
          }.bind(this));
      }else{
        let url = `/api/frete`;
        this.gettingPrices = true;
        this.$http.post(url, this.form.shipping_address)
          .then(function (res) {
            this.shippingMethods = res.data.calcs;
            this.gettingPrices = false;
          }.bind(this))
          .catch(function (res) {
            return this.$toastr.error("Não foi possível calcular o frete, tentando novamente...");
            this.gettingPrices = false;
          }.bind(this));
        }
    },
    loadData() {
      this.$http.get('/api/checkout/load-data')
        .then(function (res) {
          if (res.data.error)
            return this.$toastr.error("Não foi possível carregar os endereços");
          res = res.data.data;
          this.shared.shippingTypes = res.shipping;
          this.shared.allowedPaymentMethods = res.payment;
          this.form.payment.type = res.payment[0]
          console.log("res.shipping "+res.shipping)
        }.bind(this))
        .catch(function (e) {

        }.bind(this));
    },
  }
}
</script>
