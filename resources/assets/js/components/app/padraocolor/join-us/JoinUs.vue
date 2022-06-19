<template>
  <el-container
    id="container-form"
    v-loading.fullscreen.lock="sending"
    element-loading-background="rgba(0, 0, 0, 0.8)"
    element-loading-text="Enviando informações..."
    :class="resellerCssClass"
  >
    <el-main>
      <el-row type="flex" justify="center">
        <el-col :lg="12">
          <el-row class="position-relative">
            <transition name="el-zoom-in-center">
              <component
                :id="`${form.reseller.type==0 ? 'user-pf' : 'user-pj'}`"
                v-on:go-to="goTo"
                v-on:move="moveStep"
                v-on:update="saveRecover"
                v-on:finish="sendForm"
                :_reseller="form.reseller"
                :_address="form.address"
                :_company="form.company"
                :_person="form.person"
                :_clients="form.clients"
                :is="actualStep"
              />
            </transition>
          </el-row>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>
<style lang="scss">
.el-form--label-top .el-form-item__label {
  padding-bottom: 0;
  margin-bottom: 0;
}
</style>

<script>
import { isNumber } from 'util';
import FormRecover from './-mixins/form-recover'
var components = {
  "user-data": require("./register/-UserData").default,
  "user-address": require("./register/-UserAddress").default,
  "done": require("./-Done").default
}
export default {
  props: ["data"],
  components,
  mixins: [FormRecover],
  data() {
    return {
      step: 0,
      sending: false,
      form: {
        reseller: {
          type: 0,
          id_loja: 1,
          name: "",
          email: "",
          doc: "",
          phone: "",
          whatsapp: "",
          provider : null,
          provider_id : null
        },
        address: {
          zipcode: "",
          state: "",
          city: "",
          district: "",
          street: "",
          number: "",
          complement: "",
          reference: "",
        },
        //clients:[{name:'',lastName:'',phone:''},{name:'',lastName:'',phone:''}],
        company: {
          ie: "",
          im: "",
          no_im: false,
        },
        person: {
          identity: ""
        }
      }
    }
  },
  mounted() {
    this.$toastr.info("Complete o cadastro e clique em continuar")
    if (this.data) {
      if(this.data.length>0) {
        if (this.form.reseller.type !== null)
          this.step = 1
      }
      if(this.data.provider) this.form.reseller.provider = this.data.provider
      if(this.data.provider_id) this.form.reseller.provider_id = this.data.provider_id
      if(this.data.email) this.form.reseller.email = this.data.email
      if(this.data.name) this.form.reseller.name = this.data.name
      if(this.data.name) this.form.reseller.business_name = this.data.name


      if(this.data.message) this.$toastr.success(this.data.message)
    }
    history.replaceState(null, null, window.location.pathname)
    this.startRecover()
  },
  methods: {
    
    goTo(i) {
      if (isNumber(i)) {
        this.step = i
      }
    },
    moveStep(v = 1) {
      this.step += v
    },
    sendForm() {
      this.sending = true
      this.$http.post('/seja-um-revendedor', this.form)
        .then(({ data }) => {
          this.moveStep()
        })
        .catch(e => this.displayError(e.response.data.message,"Não foi possível enviar o formulário"))
        .finally(r => this.sending = false)
    },
    displayError(msg, title = "Erro") {
      this.$alert(msg, title, {
        confirmButtonText: 'OK',
        showClose: false,
        type: 'error'
      })
    }
  },
  computed: {
    actualStep() {
      return Object.keys(components)[this.step]
    },
    resellerCssClass(){
      let type = this.form.reseller.type
      if(type === null) return

      return `reseller-${type ? "pj" : "pf"}`
    }
  }
}
</script>