<template>
  <div
    id="container-form"
    class="finish"
    v-loading.fullscreen.lock="sending"
    element-loading-background="rgba(0, 0, 0, 0.8)"
    element-loading-text="Enviando informações..."
  >
      <el-row type="flex" justify="center">
        <el-col :lg="12">
          <el-row class="position-relative">
            <transition name="el-zoom-in-center">
              <component
                id="component-render"
                v-on:finish="sendForm"
                :_reseller="form.reseller"
                :_clients="form.clients"
                :_files="form.files"
                :_company="form.company"
                :_index="2"
                :is="actualStep"
              />
            </transition>
          </el-row>
        </el-col>
      </el-row>
  </div>
</template>
<style lang="scss">
.el-form--label-top .el-form-item__label {
  padding-bottom: 0;
  margin-bottom: 0;
}
</style>

<script>
import FormRecover from './-mixins/form-recover'
var components = {
  "user-validation": require("./finish/-UserValidation").default,
  "done" : require("./-Done").default
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
          type: null,
          businessName: "",
          name: "",
          email: "",
          doc: "",
          phone: "",
          whatsapp: ""
        },
        address: {
          zipcode: "",
          state: "",
          city: "",
          district: "",
          street: "",
          number: "",
          complement: "",
          reference: ""
        },
        clients: [{ name: '', lastName: '', phone: '' }, { name: '', lastName: '', phone: '' }],
        files: {},
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
  beforeMount() {
    this.form = Object.assign(this.form, this.data)
  },
  methods: {
    sendForm() {
      let form_data = Object.toFormData(this.form)
      this.sending = true
      this.$http.post(window.location.pathname, form_data,{headers: {'Content-Type': 'multipart/form-data'}})
        .then(({ data }) => this.step++)
        .catch(e => this.displayError(e.response.data.message))
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
  computed:{
    actualStep() {
      return Object.keys(components)[this.step]
    }
  }
}
</script>