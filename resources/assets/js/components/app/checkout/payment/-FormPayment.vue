<style lang="scss">
.paymentblock-custom {
  padding: 0px 0px 12px 0px;
}

.footer-custom-payment {
  padding: 24px;
}
.card.payment-method {
    background: transparent;
    border: none;
    
}
</style>
<template>
  <div class="mb-3">
    <b-card no-body class="payment-method">
      <div
        role="tab"
        
        v-bind:class="{ 'section-completed': confirmed}"
      >
        <i class="mi mi-check" v-show="confirmed"></i>
        <h4 class="d-flex align-items-baseline">Pagamento <img src="/assets/images/icon-payment-checkout.jpg" alt="" style="max-width: 100%;"></h4>
      </div>
      <b-collapse visible id="paymentmethod" role="tabpanel" ref="accordionPayment">
        <b-card-body>
          <form @submit.prevent="ConfirmPaymentMethod">
            <div class="d-flex">
              <b-form-group class="hide-mark">
                <b-form-radio-group
                  id="radios2"
                  v-model="form.payment.type"
                  name="radioSubComponent"
                  :disabled="confirmed"
                >
                  <div class="payment-options">
                    <b-form-radio
                      value="creditcard"
                      v-if="canUseCreditCard"
                      :class="{active:form.payment.type == 'creditcard'}"
                    >Cartão de crédito <img src="/assets/images/creditcard.png" alt="" style="width: 30px;margin-left: 10px;"></b-form-radio>
                    <b-form-radio
                      value="bankslip"
                      v-if="canUseBankslip"
                      :class="{active:form.payment.type == 'bankslip'}"
                    >Boleto bancário <img src="/assets/images/icon-boleto-checkout.png" alt="" style="width: 30px;margin-left: 10px;"></b-form-radio>
                    <b-form-radio
                      value="paylater"
                      v-if="canPayLater"
                      :class="{active:form.payment.type == 'paylater'}"
                    >Pagar ao {{shippingVerb}}</b-form-radio>
                  </div>
                </b-form-radio-group>
              </b-form-group>
              <div class="col-md pl-5 pt-3">
                <div v-if="form.payment.type=='creditcard'">
              <creditcard-form
                :confirmed="confirmed"
                :shared="shared"
                :form="form"
                v-model="selectedCard"
                :_subtotal="_subtotal"
                v-if="shared.allowedPaymentMethods.includes('creditcard') && form.payment.type=='creditcard'"
                ref="credicarddata"
              />
				</div>

                <div class="mt-4" v-if="form.payment.type=='bankslip'">
                  <hr>
                  <p>O prazo de entrega poderá variar conforme a confirmação do pagamento.</p>
                </div>
                <div class v-if="form.payment.type == 'paylater'">
                  <p
                    v-if="shared.extraStep == 'withdrawal'"
                  >Pague ao retirar seu produto no balcão selecionado.</p>
                  <p
                    v-if="shared.extraStep == 'payment'"
                  >Pague ao receber seu produto pela sua rota escolhida.</p>
                </div>
              </div>
            </div>

            <div class="d-flex" style="margin-top:15px">
				<template v-if="form.payment.type == 'creditcard'">
					<template v-if="!canConfirm">
						<strong>Selecione um cartão e preencha corretamente as de segurança para prosseguir...</strong>
					</template>
					<template v-else>
						<button
							type="button"
							@click="ConfirmPaymentMethod"
							class="btn btn-finish-custom shaking"
							v-if="form.payment.confirmed==false"
						>SELECIONAR ESTE MEIO DE PAGAMENTO</button>
						<button
							type="button"
							class="btn btn-finish-custom"
							@click="form.payment.confirmed=false"
							v-else
						>EDITAR MEIO DE PAGAMENTO</button>
					</template>
				</template>
				<template v-else>
					<button
						type="button"
						@click="ConfirmPaymentMethod"
						class="btn btn-finish-custom shaking"
						v-if="form.payment.confirmed==false"
					>SELECIONAR ESTE MEIO DE PAGAMENTO</button>
					<button
						v-else
						type="button"
						class="btn btn-finish-custom"
						@click="form.payment.confirmed=false"
					>EDITAR MEIO DE PAGAMENTO</button>
				</template>
            </div>
          </form>
        </b-card-body>
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
export default {
  props: {
    form: null,
    cart: null,
    shippingTypes: null,
    _subtotal: null,
    shared: {}
  },
  data() {
	  return {
		  selectedCard : null
	  }
  },
  components: {
    'creditcard-form': require('./-CreditcardForm.vue').default,
  },
  methods: {
    canConfirmPayment: function () {
		let result = true;
		let message = "<b>Campos obrigatórios em informações de compra</b><ul>";
		if (this.form.shipping.type < 0) {
			this.$toastr.error("Selecione o forma de envio para poder prosseguir...");
			return false;
		}

		if (this.form.payment.type == "creditcard") {
			if(!this.$refs.credicarddata.selectedCard) {
				this.$toastr.error("Selecione um cartão salvo ou insira os dados do cartão que deseja");				
				return false;
			}
      if(!this.form.payment.data.cvv) {
        this.$toastr.error("Digite o cvv do cartão selecionado");				
				return false;
      }
		}
      	return result;
    },
    ConfirmPaymentMethod() {
      if (!this.canConfirmPayment()) 
        return false;
      if (this.form.payment.type == "creditcard")
        if(!this.form.payment.data.cvv)
          return false;
      this.form.payment.confirmed = true;
      //this.$refs.accordionPayment.toggle();
    },
  },
  computed: {
	canConfirm() {
		if(!this.selectedCard) return false
    if(!this.form.payment.data.cvv) return false
		return true
	},
    confirmed() {
      return this.form.payment.confirmed
    },
    accordionNumber() {
      if (this.shared.extraStep != 'payment') 
        return 5
      else 
        return 4
    },
    canPayLater() {
      if (this.shared.extraStep == 'shipping') 
        return 
      else if (this.shared.extraStep == 'retirada_balcao') {
        return
      } else {
        return this.shared.allowedPaymentMethods.includes('paylater')
      }
        

      //return this.shared.allowedPaymentMethods.includes('paylater') && this.shared.extraStep != 'shipping'
    },
    canUseCreditCard() {
      return this.shared.allowedPaymentMethods.includes('creditcard')
    },
    canUseBankslip() {
      return this.shared.allowedPaymentMethods.includes('bankslip') && this.form.shipping.type != 'payment'
    },
    shippingVerb() {
      switch (this.shared.extraStep) {
        case 'withdrawal':
          return 'Retirar';
          break;
        case 'payment':
          return 'Receber';
          break;
      }
    }
  }
}
</script>
