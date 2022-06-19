<template>
  <el-form :rules="rules" :model="formModel" ref="form" label-position="top">
    <el-card>
      <section slot="header">
        <h4 class="my-0 f-18">CONFIRMAÇÃO DE DADOS</h4>
      </section>
      <el-row>
        <el-col>
          <h4>Clientes ativos</h4>
        </el-col>
        <template v-for="(client,i) in clients">
          <el-col :key="client.i" :sm="8">
            <el-form-item label="Nome" :prop="`${i}.name`" :rules="rules.name">
              <el-input
                :placeholder="`Cliente ${i+1}`"
                v-model="client.name"
                @change="saveProgress"
              />
            </el-form-item>
          </el-col>
          <el-col :key="client.i" :sm="8">
            <el-form-item label="Sobrenome" :prop="`${i}.lastName`" :rules="rules.lastName">
              <el-input
                :placeholder="`Cliente ${i+1}`"
                v-model="client.lastName"
                @change="saveProgress"
              />
            </el-form-item>
          </el-col>
          <el-col :key="client.i" :sm="8">
            <el-form-item label="Telefone" :prop="`${i}.phone`" :rules="rules.phone">
              <el-input
                :placeholder="`Cliente ${i+1}`"
                v-model="client.phone"
                v-mask="['(##) ####-####', '(##) #####-####']"
                @change="saveProgress"
              />
            </el-form-item>
          </el-col>
        </template>
      </el-row>
      <el-row class="mt-3">
        <el-col :span="24">
          <h4>Upload de arquivos</h4>
        </el-col>
      </el-row>
      <user-uploads
        v-on:update="setFormModel"
        :_reseller="reseller"
        :_files="files"
        :_company="company"
        :type="this.reseller.type"
        :_form="$refs.form"
      />
      <el-row type="flex" justify="end">
        <el-button id="conclude" type="primary" @click="conclude">Enviar</el-button>
      </el-row>
    </el-card>
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
@media (max-width:768px){
  #conclude{
    width: 100%;
  }
}
</style>
<script>
import UserUploads from "./-UserUploads"
export default {
  components: { "user-uploads": UserUploads },
  props: ["_reseller", "_address", "_clients", "_company", "_person", "_files"],
  data() {
    return {
      clients: this._clients,
      reseller: this._reseller,
      company: this._company,
      person: this._person,
      files: this._files,
      formModel:{}
    }
  },
  beforeMount(){
    this.setFormModel()
  },
  methods: {
    conclude() {
      this.$refs.form.validate(v => v ? this.$emit('finish') : '' )
    },
    setFormModel(){
      this.formModel = {...this.clients,...this.files}
    },
    saveProgress($e) {
      if ($e) {
        this.$emit('update')
      }
    }
  },
  computed: {
    rules() {
      return {
        "name": { required: true },
        "lastName": { required: true },
        "phone": [{ required: true }, { type: 'string', min: 14, max: 15 }],
      }
    },
  }
}
</script>