<template>
  <div>
    <section slot="header">
      <el-row type="flex" align="center" class="mt-5">
        <el-col>
          <h3 class="my-0 text-center">{{header}}</h3>
        </el-col>
      </el-row>
    </section>
    <el-row type="flex" justify="center" class="my-5">
      <el-image fit="contain" src="/svg/email.svg" />
    </el-row>
    <el-row type="flex" justify="center" class="my-5">
      <el-col class="text-content">
        <h4 class="text-center">{{content}}</h4>
      </el-col>
    </el-row>
    <el-row type="flex" justify="center" class>
      <a href="/">
        <el-button type="success" round>Ir para o login</el-button>
      </a>
    </el-row>
  </div>
</template>
<script>
export default {
  props: ["_reseller", "_address", "_index"],
  data() {
    return {
      reseller: this._reseller,
      address: this._address
    }
  },
  methods: {
    chooseType(t) {
      this.reseller.type = t
      this.$emit('move')
    }
  },
  computed: {
    messageIndex() {
      return this._index || Number(this.isInTheState)
    },
    isInTheState() {
      return ["RJ"].includes(this.address.state)
    },
    header() {
      // return [
      //   "PRONTO! CADASTRO EM ANÁLISE",  //pessoa fisica
      //   "FALTA SÓ MAIS UM PASSO!",      //pessoa juridica 1/2
      //   "PRONTO! CADASTRO EM ANÁLISE"   //pessoa juridica 2/2
      // ][this.messageIndex]
      return [
        "PRONTO! CADASTRO REALIZADO COM SUCESSO.",  //pessoa fisica
        "PRONTO! CADASTRO REALIZADO COM SUCESSO.",      //pessoa juridica 1/2
        "PRONTO! CADASTRO REALIZADO COM SUCESSO."   //pessoa juridica 2/2
      ][this.messageIndex]
    },
    content() {
      return [
        "Faça seu login agora mesmo, utilizando o nome da revenda e senha informados durante o cadastro.",
        "Faça seu login agora mesmo, utilizando o nome da revenda e senha informados durante o cadastro.",
        "Faça seu login agora mesmo, utilizando o nome da revenda e senha informados durante o cadastro."
      ][this.messageIndex]
    }
  }
}
</script>
<style lang="scss">
.text-content {
  max-width: 380px;
}
</style>