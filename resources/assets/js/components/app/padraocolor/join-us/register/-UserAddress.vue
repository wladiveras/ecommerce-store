<template>
  <el-form :rules="rules" :model="{...address}" ref="form" label-position="top">
    <el-card>
      <section slot="header">
        <h4 class="my-0 f-18 d-flex flex-row align-items-center">
          <el-button
            class="mr-3"
            @click="goBack"
            icon="el-icon-arrow-left"
            circle
            type="success"
            plain
            size="small"
          />ENDEREÇO
          <small class="ml-3">Preencha os campos abaixo para cadastrar-se</small>
          <div v-if="reseller.provider=='google'" class="ml-auto custom-icon google"><img src="/assets/images/google_completo.png"></div>
          <div v-if="reseller.provider=='facebook'" class="ml-auto custom-icon facebook"><img src="/assets/images/facebook_completo.png"></div>
        </h4>
      </section>
      <el-row type="flex" align="middle">
        <el-col :xs="24" :sm="6">
          <el-form-item label="CEP" prop="zipcode">
            <el-input v-model="address.zipcode" placeholder="Ex: 00000-000" v-mask="`#####-###`" />
          </el-form-item>
        </el-col>
        <el-col :xs="24" :sm="18">
          <el-row type="flex" align="bottom">
            <el-col>
              <el-link
                target="_blank"
                href="http://www.buscacep.correios.com.br/sistemas/buscacep/"
                type="danger"
                :underline="false"
              >
                Não sei meu CEP
                <i class="el-icon-search el-icon--right"></i>
              </el-link>
            </el-col>
          </el-row>
        </el-col>
      </el-row>
      <el-row>
        <el-col :xs="8" :sm="6">
          <el-form-item label="Estado" prop="state">
            <el-input v-model="address.state" readonly />
          </el-form-item>
        </el-col>
        <el-col :xs="16" :sm="9">
          <el-form-item label="Cidade" prop="city">
            <el-input v-model="address.city" readonly />
          </el-form-item>
        </el-col>
        <el-col :sm="9">
          <el-form-item label="Bairro" prop="district">
            <el-input v-model="address.district" />
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :sm="8">
          <el-form-item label="Rua" prop="street">
            <el-input v-model="address.street" />
          </el-form-item>
        </el-col>
        <el-col :xs="12" :sm="8">
          <el-form-item label="Número" prop="number">
            <el-input v-model="address.number" />
          </el-form-item>
        </el-col>
        <el-col :xs="12" :sm="8">
          <el-form-item label="Complemento" prop="complement">
            <el-input v-model="address.complement" />
          </el-form-item>
        </el-col>
        <el-col :sm="24">
          <el-form-item label="Referência" prop="reference">
            <el-input v-model="address.reference" />
          </el-form-item>
        </el-col>
        <el-col :sm="24">
          <el-button type="primary" class="float-right" round @click="conclude">Finalizar</el-button>
        </el-col>
      </el-row>
    </el-card>
    <hr class="hr-text my-0 mt-3" data-content="OU">
    <div class="row mt-3">
      <div class="col-md-6 col-sm-12 text-left">
          <a :href="`${$root.root_url}/auth/google`" type="button" class="btn btn-block btn-social google"  @click="$loading()">
            <span>Entrar com <img src="/assets/images/google_completo.png"></span>
          </a>
      </div>
      <div class="col-md-6 col-sm-12 text-right">
          <a :href="`${$root.root_url}/auth/facebook`" type="button" class="btn btn-block btn-social facebook" @click="$loading()">
            <span>Entrar com <img src="/assets/images/facebook_completo.png"></span>
          </a>
      </div>
    </div>
  </el-form>
</template>
<style lang="scss">
.person-type-option {
  width: 100%;
  .el-radio-button {
    &,
    > span {
      width: 100%;
    }
  }
}
</style>
<script>
import StepControl from "../-mixins/step-control"
export default {
  mixins: [StepControl],
  props: ["_address","_reseller"],
  data() {
    return {
      reseller: this._reseller,
      address: this._address,
      rules: {
        "zipcode": [{ required: true }],
        "state": [{ required: true }],
        "city": [{ required: true }],
        "district": [{ required: true }],
        "street": [{ required: true }],
        "number": [{ required: true }],
      },
      newCep: true

    }
  },
  methods: {
    getAddress(cep) {
      if (!this.newCep) return
      let url = `https://viacep.com.br/ws/${cep}/json/`
      this.$emit('update');
      this.$http.get(url)
        .then((res) => {
          if (res.data.erro) this.$toastr.error("CEP inválido")
          res = res.data
          let location = this.address
          location.district = res.bairro
          location.city = res.localidade
          location.street = res.logradouro
          location.state = res.uf
          this.validCep = true
        })
      this.newCep = false
    }
  },
  watch: {
    'address.zipcode'(n, o) {
      let zipcode = n.replace('-', '')
      if (zipcode && zipcode.length == 8) {
        this.getAddress(zipcode)
      } else if (n.length < o.length) {
        this.address.district = ''
        this.address.city = ''
        this.address.street = ''
        this.address.state = ''
        this.validCep = null
        this.newCep = true
      }
    }
  }
}
</script>