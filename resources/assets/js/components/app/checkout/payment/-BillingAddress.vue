<template>
  <div>
	<h5 class="mt-4">
		Endereço de Registro do Cartão
	</h5>
	<div class="row mt-2">
		<div class="col-md-6 col-sm-12 mb-2">
			<input placeholder="Nome" class="form-control text-uppercase" v-model="address.firstName"/>
		</div>
		<div class="col-md-6 col-sm-12 mb-2">
			<input placeholder="Sobrenome" class="form-control text-uppercase" v-model="address.lastName"/>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-12 mb-2">
			<the-mask placeholder="CEP" mask="#####-###" required v-model="cep" class="form-control"/>
		</div>
		<div class="col-md-6 col-sm-12 mb-2">
			<b-form-input
				v-model="address.city"
				required
				placeholder="CIDADE"
			/>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 col-sm-12 mb-2">
			<b-form-input
				v-model="address.state"
				required
				placeholder="ESTADO"
			/>
		</div>
		<div class="col-md-6 col-sm-12 mb-2">
			<b-form-input
				v-model="address.district"
				required
				placeholder="BAIRRO"
			/>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 col-sm-12 mb-2">
			<b-form-input
			v-model="address.street"
			required
			placeholder="RUA"
			/>
		</div>
		<div class="col-md-6 col-sm-12 mb-2">
			<b-form-input  v-model="address.number" required placeholder="NÚMERO"/>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12 mb-2">
			<b-form-input
			v-model="address.complement"
			placeholder="COMPLEMENTO"
			/>
		</div>
	</div>
  </div>
</template>
<script>
export default {
  props: ["index","confirmed","type","mode","_address"],
  data() {
    return {
      address: this._address,
      cep:"",
      saveAddress: true
    }
  },
  created() {
    this.cep = this.address.postalCode;
  },
  methods: {
    getAddress() {
      let url = `https://viacep.com.br/ws/${this.address.postalCode}/json/`;
      this.$http.get(url)
        .then((res) => {
			if (res.data.erro) this.$toastr.error("CEP inválido")
			res = res.data
			let location = this.address
			location.district = res.bairro
			location.city = res.localidade
			location.street = res.logradouro
			location.state = res.uf
			location.country = "Brasil"
        })
    }
  },
  watch: {
	address : {
		handler: function (val) {
			if(!this.address.firstName)
				return this.$emit("input",null)
			if(!this.address.lastName)
				return this.$emit("input",null)
			if(!this.address.country)
				return this.$emit("input",null)
			if(!this.address.street)
				return this.$emit("input",null)
			if(!this.address.number)
				return this.$emit("input",null)
			if(!this.address.district)
				return this.$emit("input",null)
			if(!this.address.state)
				return this.$emit("input",null)
			if(!this.address.postalCode)
				return this.$emit("input",null)

			return this.$emit("input",val)
      	},
      	deep: true
	},
    cep(n, o) {
		this.address.postalCode = this.cep;
		if (this.cep && this.cep.length == 8) {
			this.getAddress();
		} else if (n.length < o.length) {
			let location = this.address;
			location.district = '';
			location.city = '';
			location.street = '';
			location.state = '';
		}
    }
  }
}
</script>
<style scoped lang="scss">
.shipping-form {
  min-height: 200px;
  button {
    padding: 12px 77px;
    color: white;
    text-transform: uppercase;
    &.save {
      background-color: #477032;
    }
  }
}
</style>