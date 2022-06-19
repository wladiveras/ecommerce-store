<style>
.el-card {
    background-color: #f6f6f6;
}
.el-radio-button__orig-radio:checked + .el-radio-button__inner {
  background-color: #f1a960;
  border-color: #f1a960;
  color: #000;
}
.el-radio-button__orig-radio:checked + .el-radio-button__inner {
  box-shadow: -1px 0 0 0 #f1a960;
}
.el-button--primary:focus, .el-button--primary {
  background-color: #f1a960;
  border-color: #f1a960;
  color: #000;
}

</style>
<template>
  <el-form
    :validate-on-rule-change="false"
    :rules="rules"
    :model="{...reseller,...company,...person}"
    ref="form"
    label-position="top"
  >
    <el-card>
      <section slot="header">
        <h4 class="my-0 f-18 d-flex flex-row align-items-center">
          DADOS CADASTRAIS
          <small class="ml-3">Preencha os campos abaixo para cadastrar-se</small>
          <div v-if="reseller.provider=='google'" class="ml-auto custom-icon google">
            <img src="/assets/images/google_completo.png" />
          </div>
          <div v-if="reseller.provider=='facebook'" class="ml-auto custom-icon facebook">
            <img src="/assets/images/facebook_completo.png" />
          </div>
        </h4>
      </section>
     
      <el-row>
        <el-col :sm="12">
          <el-form-item :label="nameLabel" prop="name">
            <el-input v-model="reseller.name" @change="saveProgress" />
          </el-form-item>
        </el-col>
       
        <el-col :sm="12">
          <el-form-item label="Email" prop="email">
            <el-input
              v-model="reseller.email"
              :disabled="reseller.provider"
              @change="saveProgress"
            />
          </el-form-item>
        </el-col>
        <el-col :sm="12">
          <el-form-item :label="docData[0]" prop="doc">
            <el-input v-model="reseller.doc" v-mask="docData[1]" @change="saveProgress" />
          </el-form-item>
        </el-col>
        <el-col class="px-0">
          <el-col :sm="12">
            <el-form-item label="Senha" prop="password">
              <el-input type="password" v-model="reseller.password" @change="saveProgress" />
            </el-form-item>
          </el-col>
        </el-col>

        <template v-if="reseller.type == 1">
          <el-col :sm="12">
            <el-form-item label="Inscrição Municipal" prop="im">
              <el-input :disabled="company.no_im" v-model="company.im" @change="saveProgress">
                <div slot="append" class="d-flex align-items-center justify-content-center">
                  <el-checkbox v-model="company.no_im" @change="company.im = ''" class="m-0">Isento</el-checkbox>
                </div>
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :sm="12">
            <el-form-item key="ie" label="Inscrição Estadual" prop="ie">
              <el-input v-model="company.ie" @change="saveProgress" />
            </el-form-item>
          </el-col>
        </template>
      

        <el-col :sm="8">
          <el-form-item label="Telefone" prop="phone">
            <el-input
              v-model="reseller.phone"
              v-mask="['(##) ####-####', '(##) #####-####']"
              @change="saveProgress"
            />
          </el-form-item>
        </el-col>
        <el-col :sm="8">
          <el-form-item label="Celular (Whatsapp)" prop="whatsapp">
            <el-input
              v-model="reseller.whatsapp"
              v-mask="['(##) #####-####']"
              @change="saveProgress"
            />
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :sm="24">
          <el-button type="primary" class="float-right" @click="proceed">Continuar</el-button>
        </el-col>
      </el-row>
    </el-card>
    <hr class="hr-text my-0 mt-3" data-content="OU" />
    <div class="row mt-3">
      <div class="col-md-6 col-sm-12 text-left">
        <a
          :href="`${$root.root_url}/auth/google`"
          type="button"
          class="btn btn-block btn-social google"
          @click="$loading()"
        >
          <span>
            Entrar com
            <img src="/assets/images/google_completo.png" />
          </span>
        </a>
      </div>
      <div class="col-md-6 col-sm-12 text-right">
        <a
          :href="`${$root.root_url}/auth/facebook`"
          type="button"
          class="btn btn-block btn-social facebook"
          @click="$loading()"
        >
          <span>
            Entrar com
            <img src="/assets/images/facebook_completo.png" />
          </span>
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
  props: ["_reseller","_company","_person"],
  data() {
    return {
      reseller: this._reseller,
      company: this._company,
      person: this._person,
    }
  },
  methods: {
    saveProgress($e) {
      if ($e) {
        this.$emit('update')
      }
    }
  },
  computed: {
    rules() {
      return {
        "name": [{ type: "string",required: true },{ type: 'string',min: 6 }],
        "doc": [{ type: this.reseller.type ? 'cnpj' : 'cpf' },{ required: true }],
        "email": [{ type: 'email',required: true }],
        "ie": [{ required: true },{ type: 'string',min: 6 }],
        "im": [{ required: !this.company.no_im },{ type: 'string',min: 6 }],
        "password": [{ required: true },{ type: 'string',min: 6 }],
        "phone": [{ required: true },{ type: 'string',min: 14,max: 15 }],
        "whatsapp": [{ required: true },{ type: 'string',min: 15 }],
      }
    },
    nameLabel() {
      return this.reseller.type ? "Razão Social" : "Nome Completo"
    },
    docData() {
      return this.reseller.type ? ["CNPJ",'##.###.###/####-##'] : ["CPF",'###.###.###-##']
    },
  }
}
</script>