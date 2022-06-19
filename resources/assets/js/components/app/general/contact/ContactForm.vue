<template>
  <b-card  v-loading.fullscreen.lock="sending">
    <template slot="header">
      <span class="font-weight-bold text-uppercase f-18">Envie-nos um email</span>
    </template>
    <b-form @submit.prevent="submit">
      <b-form-group>
        <div class="row">
          <div class="col-sm-6">
            <b-form-input placeholder="Nome" required v-model="person.firstName"></b-form-input>
          </div>
          <div class="col-sm-6">
            <b-form-input placeholder="Sobrenome" required v-model="person.lastName"></b-form-input>
          </div>
        </div>
      </b-form-group>
      <b-form-group>
        <div class="row">
          <div class="col-sm-6">
            <b-form-input placeholder="Email" type="email" required v-model="person.email"></b-form-input>
          </div>
          <div class="col-sm-6">
            <the-mask placeholder="Telefone" required v-model="person.phone" required :mask="['(##) ####-####', '(##) #####-####']" class="form-control" />
          </div>
        </div>
      </b-form-group>
      <b-form-group>
        <div class="row">
          <div class="col-sm-12">
            <b-form-textarea placeholder="Mensagem" rows="5"required  v-model="person.message"></b-form-textarea>
          </div>
        </div>
      </b-form-group>
      <div class="row">
        <div class="col-sm-12 d-flex align-items-center flex-wrap justify-content-between">
          <slot></slot>
          <b-button type="submit" class="btn-padding float-right" style="height:50px">ENVIAR</b-button>
        </div>
      </div>
    </b-form>
  </b-card>
</template>
<script>
export default {
  data(){
    return {
      person:{
        firstName:'',
        lastName:'',
        email:'',
        phone:'',
        message:''
      },
      sending:false
    };
  },
  methods:{
    submit(e){
      this.sending = true;
      let res = Object.assign({'captcha':$("#g-recaptcha-response").val()},this.person);
      this.$http.post('/contato',res)
      .then(function(res){
        this.sending = false;
        return this.$toastr.success(res.data);
      }.bind(this))
      .catch(function(res){
        this.sending = false;
        if(res.response.data.errors){
          for(let key in res.response.data.errors){
            return this.$toastr.error(res.response.data.errors[key][0]);
          }
        }
      }.bind(this));
    }
  },
  computed:{

  }
}
</script>
