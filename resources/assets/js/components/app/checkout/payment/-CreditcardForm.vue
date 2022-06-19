<template>
<div v-loading="loading">
	<div class="row my-2">
		<div class="col-md-3 col-sm-12">
			<label><strong>Parcelas :</strong></label>
			<input type="hidden" v-model="creditcard.installment" value="1">
			<select
				v-model="creditcard.installment"
				class="form-control"
				ref="creditcard_installment"
				:disabled="form.payment.confirmed"
			>
				<option disabled selected>Parcelamento</option>
				<option
					v-for="installment in installments"
					:value="installment.qty"
					:key="installment.desc"
					>{{installment.desc}}
				</option>
			</select>
		</div>
		<div class="col-md-3 col-sm-12">
			<div :class="{'cvv_error' : !form.payment.data.cvv}">
				<label><strong>CVV :</strong></label>
				<input class="form-control" placeholder="Código de Verificação" v-model="form.payment.data.cvv" :disabled="!form.payment.data.card_id || form.payment.confirmed">
			</div>
		</div>
		<div class="col-md-6 col-sm-12 d-flex align-items-center justify-content-end">
			<a style="margin-top: 20px;" class="link" target="_blank" href="/meus-dados">Editar Cartões Salvos</a>
		</div>
	</div>
	<div class="row">
		<b-form-radio-group v-model="selectedCard" class="hide-mark d-sm-flex flex-wrap col-sm-12" >
			<template v-for="(card,key) in shared.cards">
				<b-form-radio :key="key+1" :value="key+1" class="col-sm-4 mb-3" :disabled="form.payment.confirmed" @change="selectCard(card)">
					<creditcard-div class="mb-2" :selectedCard="selectedCard" :index="key+1" :card="card" ></creditcard-div>
					<a href="#" @click.prevent="editCard()" class="link mt-2" v-if="!card.id && key+1==selectedCard">Editar Cartão</a>
				</b-form-radio>
			</template>
			<div class="col-sm-4 mb-3 custom-control custom-control-inline custom-radio" v-if="!form.payment.confirmed">
				<a class="shipping-item h-100 item rounded p-4 border w-100" @click.prevent="newCard(false)" style="cursor: pointer;">
					<div class="flex-wrap d-flex justify-content-center">
						<i class="mi mi-add f-48 g-5"></i>
					</div>
					<div class="f-12 g-5 text-center">
						<strong class="f-12 g-6" v-if="shared.cards.length>0">Outro Cartão de Crédito</strong>
						<strong class="f-12 g-6" v-else>Inserir Dados do Cartão de Crédito</strong>
						<br>
						Clique aqui para inserir os dados do cartão que deseja utilizar
					</div>
				</a>
			</div>
		</b-form-radio-group>
	</div>
	<modal name="modal_confirm" trasition="fade" height="auto" width="800" :adaptive="true" :scrollable="true" >
		<div class="card" v-loading="frmNewCard.loading">
			<div class="card-header">
				<div class="row">
					<div class="col-12">
						<strong><a href="#" @click.prevent="$modal.hide('modal_confirm')" class="mr-3 link d-lg-none">Voltar</a>Dados do Cartão de Crédito</strong>
						<small class="ml-2">Insira os dados do cartão que deseja utilizar</small>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 mb-2">
						<input placeholder="Nome impresso no cartão" class="form-control text-uppercase" v-model="frmNewCard.name"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-12 mb-2">
						<input placeholder="Vencimento" v-mask="'##/##'" class="form-control text-uppercase" v-model="frmNewCard.expiring_date"/>
					</div>
					<div class="col-md-6 col-sm-12 mb-2">
						<input placeholder="Número do cartão" v-mask="'#### #### #### ####'" class="form-control text-uppercase" v-model="frmNewCard.number"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-12 mb-2">
						<input placeholder="CVV" v-mask="['###', '####']" class="form-control text-uppercase" v-model="frmNewCard.cvv"/>
					</div>
					<div class="col-md-6 col-sm-12 mb-2">
						<input placeholder="CPF/CNPJ do Titular" v-mask="['###.###.###-##', '##.###.###/####-##']" class="form-control text-uppercase" v-model="frmNewCard.documentNumber"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 col-sm-12">
						<input placeholder="Telefone" v-mask="['(##) ####-####','(##) #####-####']" class="form-control text-uppercase" v-model="frmNewCard.phoneNumber"/>
					</div>
					<div class="col-sm-7 mb-3" >
						<label class="d-flex align-items-center mt-2" v-if="shared.credit_card_config.save_card">
							<el-switch 
								class="mr-2"
								v-model="frmNewCard.save_card"
								active-color="#13ce66"
								inactive-color="#d32626"
								inactive-text=""
								:inactive-value="false"
								active-text="Salvar Cartão"
								:active-value="true"
							/>
							
						</label>
					</div>
				</div>
				<billing-address v-model="frmNewCard.address" :_address="frmNewCard.address"/>
				<div class="row" v-if="frmNewCard.save_card&&shared.credit_card_config.save_card">
					<div class="col-md-12 col-sm-12">
						<span class="text-success">
							Este cartão será gravado apenas após a primeira transação bem sucedida, para garantir que todos os dados fornecidos estão corretos.
						</span>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-12">
						<button type="button" v-if="canSubmitNewCard" :disabled="!canSubmitNewCard" v-bind:class="{'shaking' : canSubmitNewCard }" 
							class="btn btn-finish-custom" @click.prevent="newCard(true)">
								CONFIRMAR DADOS DO CARTÃO
						</button>
						<span v-if="!canSubmitNewCard">
							<strong>PREENCHA O FORMULÁRIO CORRETAMENTE PARA PROSSEGUIR ...</strong>
						</span>
					</div>
				</div>
			</div>
		</div>
	</modal>
</div>
</template>

<script>
export default {
  props:["form","confirmed","_subtotal","shared"] ,
  data() {
    return {
			interest : [0,4.02,4.56,4.89,5.13,5.8],
			creditcard: this.form.payment.data,
			loading : false,
			selectedCard : null,
			frmNewCard : {
				editing : null,
				same_address : true,
				loading : false,
				name : null,
				number : null,
				expiring_date : null,
				documentNumber : null,
				cvv : null,
				phoneNumber : null,
				save_card : false,
				address : {
					firstName : null,
					lastName : null,
					country: "Brasil",
					street: "",
					number: "",
					complement: "",
					district: "",
					city: "",
					country: "",
					state: "",
					postalCode: ""
				}
			}
		}
	},
	watch : {
		selectedCard(val) {
			return this.$emit("input",val)
		}
	},
	components: {
		'creditcard-div' : require("./-CreditcardDiv.vue").default,
    	'billing-address': require("./-BillingAddress.vue").default
	},
	computed: {
		canSubmitNewCard() {
			if(!this.frmNewCard.name) 
				return false
			if(!this.frmNewCard.number) 
				return false
			if(!this.frmNewCard.expiring_date) 
				return false
			if(!this.frmNewCard.documentNumber) 
				return false
			if(!this.frmNewCard.cvv) 
				return false
			if(!this.frmNewCard.phoneNumber) 
				return false
			if(!this.frmNewCard.address.firstName)
				return false
			if(!this.frmNewCard.address.lastName)
				return false
			if(!this.frmNewCard.address.country)
				return false
			if(!this.frmNewCard.address.street)
				return false
			if(!this.frmNewCard.address.number)
				return false
			if(!this.frmNewCard.address.district)
				return false
			if(!this.frmNewCard.address.state)
				return false
			if(!this.frmNewCard.address.postalCode)
				return false
			return true
		},
		installments() {
			let interest = this.interest
			let installments = []
			let rawValue = this.rawValue()
			installments[0] = {
				qty: 1,
				value: rawValue,
				desc: "1x de " + rawValue.currency()
			}
			for (var i = 2; i <= 3; i++) {
				let _val = rawValue + ((rawValue/100) * interest[i - 1]);
				installments[i - 1] = {
					qty: i,
					value: _val,
					desc: i + `x de ${(_val / i).currency()} (total de ${_val.currency()})`
				}
			}
			return installments
		},
	},
	methods: {
		editCard() {
			let index = this.selectedCard
			this.frmNewCard = this.shared.cards[index-1]
			this.frmNewCard.address = this.shared.cards[index-1].address
			this.frmNewCard.editing = index-1 == 0 ? "PRIMEIRO" : index-1
			this.frmNewCard.loading = false
			return this.$modal.show('modal_confirm')
		},
		newCard(confirmed) {
			if(!confirmed) 
				return this.$modal.show('modal_confirm')
			else {
				this.frmNewCard.loading = true
				this.setNewCard()
			}
		},
		cleanNewCardForm() {
			for(let index in this.frmNewCard) {
				this.frmNewCard[index] = null
			}
			this.frmNewCard.address = {
				firstName : null,
				lastName : null,
				country: "Brasil",
				street: "",
				number: "",
				complement: "",
				district: "",
				city: "",
				country: "",
				state: "",
				postalCode: ""
			}
		},
		setNewCard() {
			setTimeout( () => {
				if(this.frmNewCard.editing) {
					let index = this.frmNewCard.editing=="PRIMEIRO" ? 0 :this.frmNewCard.editing
					this.shared.cards[index] =Object.assign({},this.frmNewCard)
				}
				else
					this.shared.cards.push(Object.assign({},this.frmNewCard))
				this.cleanNewCardForm()
				this.$modal.hide('modal_confirm')
				this.frmNewCard.loading = false
				this.selectedCard = this.shared.cards.length
				this.selectCard(this.shared.cards[this.selectedCard-1])
			},200)
			
		},
		rawValue() {
			let price = this.shared.shippingPrice;
			if (!price)
				return this._subtotal;
			let total = Number(this._subtotal) + Number(price);
			return total;
		},
		selectCard(card) {
			if(!card.brand) 
				return this.selectNewCard(card)
			let loading = this.$loading()
			this.$http.post(window.location.href+"/getcard",{cardAction:"getCard", cardId : card.card_id,action:5}).then( res => {
				res = res.data
				if(!res.success) {
					loading.close()
					return this.$toastr.error(res.message)        
				}
				this.form.payment.data.card_id = (res.data.card_id ? res.data.card_id : null)
				this.form.payment.data.address = card.billing_address
				this.form.payment.data.cvv = null
				this.form.payment.data.save_card = false
				loading.close()
			}).catch( er => {
				loading.close()
				this.$toastr.error("Erro ao receber dados de cartão")        
			})
		},
		selectNewCard(card) {
            this.form.payment.data.card_id = card.card_id ?card.card_id : null
            this.form.payment.data.number  = card.number
            this.form.payment.data.expiring_date = card.expiring_date
            this.form.payment.data.name = card.name
            this.form.payment.data.cvv  = card.cvv
            this.form.payment.data.documentNumber  = card.documentNumber
            this.form.payment.data.documentType = (card.documentNumber.length > 11 ? "CNPJ" : "CPF")
            this.form.payment.data.phoneNumber = card.phoneNumber
            this.form.payment.data.save_card = card.save_card
            this.form.payment.data.address = card.address
		}
	}
}
</script>
<style scoped lang="scss">
.cvv_error {
	label {
		strong {
			color : red;
		}
	}
	input.form-control {
		border : 1px solid red!important;
	}
}
</style>