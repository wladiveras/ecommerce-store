<template>
<modal name="modal" trasition="fade" height="auto" width="800" :adaptive="true" :scrollable="true" >
    <div class="card">
        <div class="card-header">
			<div class="row">
				<div class="col-12">
					<strong>Dados do Cartão de Crédito</strong>
                    <template v-if="card.id">
                        <span class="ml-2">Cartão <b>{{card.brand}}</b> final : <b>{{card.last_four_digits}}</b></span>
                    </template>
                    <br>
                    <small>Informações como número do cartão e código de verificação, não ficam salvos em nossa base de dados por segurança de nossos usuários.</small>
				</div>
			</div>
		</div>
        <div class="card-body">
            <template v-if="type=='create'">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <h4>Dados do Cartão</h4>
                    </div>
                </div>
                <div class="row mb-2">
					<div class="col-md-12 col-sm-12">
						<input placeholder="Nome impresso no cartão" class="form-control text-uppercase" v-model="card.name"/>
					</div>
				</div>
				<div class="row  mb-2">
					<div class="col-md-6 col-sm-12">
						<input placeholder="Vencimento" v-mask="'##/##'" class="form-control text-uppercase" v-model="card.expiring_date"/>
					</div>
					<div class="col-md-6 col-sm-12">
						<input placeholder="Número do cartão" v-mask="'#### #### #### ####'" class="form-control text-uppercase" v-model="card.number"/>
					</div>
				</div>
				<div class="row  mb-4">
					<div class="col-md-6 col-sm-12">
						<input placeholder="CVV" v-mask="['###', '####']" class="form-control text-uppercase" v-model="card.cvv"/>
					</div>
					<div class="col-md-6 col-sm-12">
						<input placeholder="CPF/CNPJ do Titular" v-mask="['###.###.###-##', '##.###.###/####-##']" class="form-control text-uppercase" v-model="card.documentNumber"/>
					</div>
				</div>
            </template>

            <div class="row">
				<div class="col-md-12 col-sm-12">
                    <h4>Dados Pessoais</h4>
                </div>
            </div>
			<div class="row mb-4">
				<div class="col-md-6 col-sm-12">
					<input placeholder="Nome" class="form-control text-uppercase" v-model="card.billing_address.firstName"/>
				</div>
                <div class="col-md-6 col-sm-12">
					<input placeholder="Sobrenome" class="form-control text-uppercase" v-model="card.billing_address.lastName"/>
				</div>
			</div>
            <div class="row mb-2">
				<div class="col-md-6 col-sm-12">
					<input placeholder="Email" class="form-control text-uppercase" v-model="card.billing_address.email"/>
				</div>
                <div class="col-md-6 col-sm-12">
                    <input placeholder="CPF/CNPJ do Titular" v-mask="['###.###.###-##', '##.###.###/####-##']" class="form-control text-uppercase" v-model="card.billing_address.documentNumber"/>
				</div>
			</div>
            <div class="row mt-4">
				<div class="col-md-12 col-sm-12">
                    <h4>Localização</h4>
                </div>
            </div>
            <div class="row mb-2">
				<div class="col-md-4 col-sm-12">
                    <the-mask placeholder="CEP" mask="#####-###" required v-model="card.billing_address.postalCode" class="form-control"/>
				</div>
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.city" disabled placeholder="CIDADE"/>
                </div>
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.state" disabled placeholder="ESTADO" />
                </div>
			</div>
            <div class="row mt-2">
                <div class="col-md-6 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.district" disabled placeholder="BAIRRO" />
                </div>
                <div class="col-md-6 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.street" disabled placeholder="RUA" />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.number" placeholder="NÚMERO" />
                </div>
                <div class="col-md-8 col-sm-12">
                    <input class="form-control" v-model="card.billing_address.complement" placeholder="COMPLEMENTO" />
                </div>
            </div>
		</div>
        <div class="card-footer">
			<div class="row">
				<div class="col-12">
                    <button type="button" class="btn btn-danger float-left" @click="destroyCard" v-if="type=='edit'" >
							EXCLUIR
					</button>
					<button type="button" v-if="canSubmitNewCard" :disabled="!canSubmitNewCard"
						class="btn btn-success float-right" :class="{' btn-block' : !card.id}" @click="submit">
							CONFIRMAR DADOS DO CARTÃO
					</button>
					<span v-if="!canSubmitNewCard">
						<strong>PREENCHA O FORMULÁRIO CORRETAMENTE PARA PODER SALVAR ...</strong>
					</span>
				</div>
			</div>
		</div>
    </div>
</modal>
</template>

<script>
export default {
  props:[] ,
  data() {
    return {
			loading : false,
            type : null,
            card : {
                id : null,
                name : null,
				number : null,
				expiring_date : null,
				documentNumber : null,
				cvv : null,
                billing_address : {
                    firstName : "",
                    lastName : "",
                    documentType : "",
                    postalCode : "",
                    city : "",
                    state : "",
                    district : "",
                    street : "",
                    number : "",
                    complement : ""
                }
            }
		}
	},
    watch : {
        "card.billing_address.documentNumber"(val) {
            this.card.billing_address.documentType = val.length > 14 ? "CNPJ" : "CPF"
        },
        "card.billing_address.postalCode"(n, o) {
            n = n ? n : ""
            o = o ? o : ""
            if (n && n.length == 8) 
                this.getAddress()
            else if (n.length < o.length) {
                this.card.billing_address.district = ""
                this.card.billing_address.city     = ""
                this.card.billing_address.street   = ""
                this.card.billing_address.state    = ""
            }
        }
    },
    computed : {
        canSubmitNewCard() {
            if(this.type=="create") {
                if(!this.card.name)            return false
                if(!this.card.number)          return false
                if(!this.card.expiring_date)   return false
                if(!this.card.documentNumber)  return false
                if(!this.card.cvv)             return false
            }

            if(!this.card.billing_address.firstName)       return false
			if(!this.card.billing_address.lastName)        return false
			if(!this.card.billing_address.documentNumber)  return false
            if(!this.card.billing_address.postalCode)      return false
            if(!this.card.billing_address.city)            return false
            if(!this.card.billing_address.state)           return false
            if(!this.card.billing_address.district)        return false
            if(!this.card.billing_address.street)          return false
            if(!this.card.billing_address.number)          return false
			return true
        }
    },
	methods: {
        destroyCard() {
            let loading = this.$loading()
            this.$http.post(`${window.location.href}/card/delete`,this.card).then( res => {
                window.location.reload()
            }).then( er => {
                console.log(er)
                loading.close()
                this.hide()
            })
        },
        getAddress() {
            let url = `https://viacep.com.br/ws/${this.card.billing_address.postalCode}/json/`
            this.$http.get(url).then((res) => {
                if (res.data.erro) this.$toastr.error("CEP inválido")
                    res = res.data
                this.card.billing_address.district = res.bairro
                this.card.billing_address.city     = res.localidade
                this.card.billing_address.street   = res.logradouro
                this.card.billing_address.state    =  res.uf
                this.card.billing_address.country  = "Brasil"
            })
        },
        show(type,card) {
            if(card) this.card = card
            this.type = type
            return this.$modal.show('modal')
        },
        hide() {
            return this.$modal.hide('modal')
        },
        submit() {
            let loading = this.$loading()
            this.$http.post(`${window.location.href}/card/edit`,this.card).then( res => {
                window.location.reload()
            }).then( er => {
                console.log(er)
                loading.close()
                this.hide()
            })
        }
	}
}
</script>