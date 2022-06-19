<style lang="scss">
.collapsed > .mi-remove,
    :not(.collapsed) > .mi-add {
        display: none;
    }
.card.extra-data {
    border: 1px solid #7d1756;
    border-radius: 7px;
    padding-top: 10px;
    max-height: 300px;
    overflow-y: auto;
    .card-body {
      padding-top: 0;
    }
}
</style>
<template>
  <div>
    <b-card no-body class="mb-3 extra-data">
      <div role="tab" v-b-toggle.extradata :class="{'section-completed': completed}">
        <i class="mi mi-add float-right"></i>
        <i class="mi mi-remove float-right"></i>
        <h5 class="ml-2 f-16">{{title}}</h5>
      </div>
      <b-collapse visible id="extradata" role="tabpanel" ref="accordionExtraData">
        <b-card-body>
            <shipping-locations v-on:new="getAddresses" :type="form.shipping.option" :display-main="form.shipping.option == 'normal'" v-on:chosen="proceed" :main="mainLocation" :locations="locations" v-if="shared.extraStep == 'shipping'">
              <div class="">
                <div class="col-sm-12 d-flex justify-content-end">
                  <a target="_blank" href="/meus-dados" class="link"><p>Editar endereços</p> </a>
                </div>
              </div>
            </shipping-locations>
            <template v-if="shared.extraStep == 'withdrawal'">
              <withdrawal-stores v-on:chosen="proceed" :locations="stores" v-show="shared.extraStep == 'withdrawal'" />
            </template>
            <template v-if="shared.extraStep == 'retirada_balcao'">
              <span style="font-size: 16px;">Informe um cep e escoha em qual <strong>balcão</strong> deseja retirar seu pedido:</span>
              <div class="d-flex mb-4">                
                <the-mask placeholder="CEP" id="cep" mask="#####-###"  class="custom-form-checkout form-control" style="width: 200px;" />
                <button class="btn btn-primary" v-on:click="getNovosEnderecos">Buscar Balcão</button>
              </div>
              <balcaoRetirada-stores v-on:chosen="proceed" :locations="addressPickup" v-show="shared.extraStep == 'retirada_balcao'" />
            </template>  
            
        </b-card-body>
      </b-collapse>
    </b-card>
  </div>
</template>

<script>
export default {
  props: {
    form:null,
    shared: {}
  },
  components:{
    'withdrawal-stores':require('./-WithdrawalStores.vue').default,
    'balcaoRetirada-stores':require('./-BalcaoRetirada.vue').default
  },
  data(){
    return {
      mainLocation:{
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
      shippingLocations:[],
      stores:[],
      addressPickup:[]
    }
  },
  created(){
    this.getSupplyStores();
    this.getAddresses();
    this.mainLocation = Object.assign(this.shared.user.address,{name:"Meu endereço"});
    this.getAddressPickup(this.shared.user.address.zip_code);
  },
  methods: {
    updateList(address,index){
      if(this.shippingLocations[index] == undefined)
      this.shippingLocations[index] = []
      this.shippingLocations[index].push(address)
    },
    getAddresses(){
      this.$http.get('/api/user/addresses')
      .then((res) => {
        if(res.error)
          return this.$toastr.error("Não foi possível carregar os endereços");
        this.shippingLocations = res.data.data
      })
    },
    getNovosEnderecos: function (event) {
      // `this` dentro de métodos aponta para a instância Vue
      this.$http.get('/api/pickup/'+document.getElementById('cep').value)
      .then((res) => {
        if(res.error)
          return this.$toastr.error("Não foi possível carregar as lojas");
        this.addressPickup = res.data;
      })
      .catch((e) => this.$toastr.error("Não foi possível carregar as lojas"))
    },
    getSupplyStores(){
      this.$http.get('/supply/stores')
      .then((res) => {
        if(res.error)
          return this.$toastr.error("Não foi possível carregar as lojas");
        this.stores = res.data.data;
      })
      .catch((e) => this.$toastr.error("Não foi possível carregar as lojas"))
    },
     getAddressPickup(zip_code){
      this.$http.get('/api/pickup/'+zip_code)
      .then((res) => {
        if(res.error)
          return this.$toastr.error("Não foi possível carregar as lojas");
        this.addressPickup = res.data;
      })
      .catch((e) => this.$toastr.error("Não foi possível carregar as lojas"))
    },
    proceed(address){
      this.form.shipping_address = address;
      if(this.shared.extraStep == 'shipping'){
        this.$emit('chosen');
        if(this.form.shipping.type == 'shipping'){
          this.shared.step = 'shippingmethod';
          //this.$parent.$refs.shippingmethod.$refs.accordionShippingMethod.toggle();
        }else{
          this.shared.step = 'consignment';
          this.$parent.$refs.consignment.$refs.accordionConsignmentMethod.toggle();
        }
      }
      if(this.shared.extraStep == 'withdrawal'){
        this.shared.step = 'payment';
      }
      if(this.shared.extraStep == 'retirada_balcao'){
         this.$emit('chosen');
        //this.$parent.$refs.shippingmethod.$refs.accordionShippingMethod.toggle();
        this.shared.step = 'shippingmethod';
      }
    }
  },
  computed:{
    locations(){
      let res = this.shippingLocations[this.form.shipping.option]
      return res ? res : []
    },
    completed(){
      return this.shared.extraStep != this.shared.step
    },
    title(){
      switch (this.shared.extraStep) {
        case 'withdrawal':
          return 'Lojas Cria Fácil';
          break;
        case 'retirada_balcao':
          return 'Balcões de Entrega';
          break;  
        case 'shipping':
          return 'Endereço de Entrega';
          break;
      }
    }
  }
}
</script>
