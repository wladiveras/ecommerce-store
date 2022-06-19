<template>
  <b-card>
    <template slot="header">
      <div style="height:38px" class="d-flex justify-content-between flex-row align-items-center">
        <h3 class="f-18 header-title">Calcule seu frete</h3>
      </div>
    </template>
    <div class="" v-loading="gettingPrices">
      <div class="">
        <the-mask v-model="cep" class="form-control" mask="#####-###" placeholder="00000-000"/>
      </div><br>
        <template v-for="(method, i) in shippingMethods">
          <p><strong>{{method.name}}</strong>: em {{method.time}} por {{method.rawPrice.currency()}}</p>
        </template>
    </div>

  </b-card>
</template>
<script>
export default {
  props:[`subtotal`],
  data(){
    return {
      gettingPrices:false,
      cep:``,
      shippingAddress:{
        name:"",
        street:"",
        number:"",
        district:"",
        state:"",
        city:"",
        complement:"",
        reference:"",
        zip_code:""
      },
      shippingMethods:[]
    };
  },
  mounted(){

  },
  methods:{
    getShippingPrices(){
      let url = `/api/frete`;
      this.gettingPrices = true;
      this.$http.post(url,this.shippingAddress)
      .then(function(res){
        this.shippingMethods = res.data.calcs;
        this.gettingPrices = false;
      }.bind(this))
      .catch(function(res){
        return this.$toastr.error("Não foi possível calcular o frete");
        this.gettingPrices = false;
      }.bind(this));
    }
  },
  watch:{
    cep(){
      this.shippingAddress.zip_code = this.cep;
      if(this.cep && this.cep.length == 8){
        this.getShippingPrices();
      }
    }
  }
}
</script>
