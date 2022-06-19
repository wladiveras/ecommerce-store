<template>
  <b-card>
    <template slot="header">
      <span class="font-weight-bold text-uppercase f-18">Trabalhe Conosco</span>
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
          <h4 class="f-18 font-weight-bold">Anexar currículo</h4>
          <file-uploader></file-uploader>
        </div>
      </div>
    </b-form-group>
      <div class="row">
        <div class="col-sm-12">
          <b-button type="submit" class="btn-padding float-right">ENVIAR</b-button>
        </div>
      </div>
    </b-form>
  </b-card>
</template>
<script>
export default {
  components:{
    'file-uploader':require("../../user/register/-UploadImage.vue").default
  },
  data(){
    return {
      person:{
        firstName:'',
        lastName:'',
        email:'',
        phone:'',
        curriculum:''
      }
    };
  },
  methods:{
    submit(){
      // console.log(`test`);
    }
  },
  computed:{

  }
}
</script>
