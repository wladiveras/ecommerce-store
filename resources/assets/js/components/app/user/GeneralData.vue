<template>
  <form method="POST">
    <div class="general-data">
      
      <div class="row d-flex align-items-center">
        
        <div class="col-sm-10 flex-wrap d-sm-flex flexb-6-child justify-content-between">
          <input :value="$root.csrf_token" name="_token" style="display:none;">
          <div>
            <label>Seu Nome:</label>
            <b-input placeholder="Nome" v-model="user.name" class="mb-3 custom-form-checkout"></b-input>
          </div>
          <div>
            <label>CPF:</label>
            <b-input placeholder="CPF" v-model="user.doc" class="custom-form-checkout" v-mask="['###.###.###-##', '##.###.###/####-##']" disabled></b-input>
          </div>
          <div>
            <label>Email:</label>
            <b-input placeholder="Email" name="email" v-model="user.email" class="mb-3 custom-form-checkout" disabled></b-input>
          </div>
          <div>
            <label>Telefone:</label>
            <b-input placeholder="Telefone" v-model="user.phone" v-mask="['(##) ####-####', '(##) #####-####']" class="mb-3 custom-form-checkout" ></b-input>
          </div>
        </div>
      </div>
      <!-- <p class="f-14 font-weight-bold mb-3 mt-3">Endereço</p>
      <div class="row">
        <the-mask placeholder="CEP" mask="#####-###" disabled v-model="user.address.zip_code" class="custom-form-checkout form-control flexb-6" />
        <div class="flexb-4-child col-sm-12 flex-wrap d-sm-flex justify-content-between">
          <b-input placeholder="Cidade" disabled v-model="user.address.city" class="custom-form-checkout" />
          <b-input placeholder="Estado" disabled v-model="user.address.state" class="custom-form-checkout" />
          <b-input placeholder="Bairro" disabled v-model="user.address.district" class="custom-form-checkout" />
          <b-input placeholder="Rua" disabled v-model="user.address.street" class="custom-form-checkout" />
          <b-input placeholder="Número" disabled v-model="user.address.number" class="custom-form-checkout" />
          <b-input placeholder="Complemento" disabled v-model="user.address.complement" class="custom-form-checkout" />
        </div>
      </div> -->
    </div>
    <div class="row mt-3">
      <div class="col-12">
        <button class="btn btn-primary float-right">Salvar Alterações</button>
      </div>
    </div>
  
  </form>
</template>
<script>
export default {
  props:['user'],
  components : {
    "logo-upload" : require("./-LogoUpload.vue").default
  }
}
</script>
