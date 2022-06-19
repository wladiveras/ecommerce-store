<template>
  <b-card>
    <template slot="header">
      <span class="font-weight-bold text-uppercase f-18">Nossas Unidades</span>
    </template>
    <div>
      <template v-for="(store, i) in stores">
        <div class="store border rounded border-gray mb-3 d-flex align-items-center justify-content-between">
          <div class="d-flex flex-column flex-wrap">
            <span class="f-18 font-weight-bold">{{store.name}}</span>
            <span class="f-12"><strong>Endereço: </strong> {{store.address.street}}, Nº {{store.address.number}} / {{store.address.district}} - {{store.address.city}} </span>
            <span class="f-12"><strong>Email: </strong> {{store.email}}</span>
            <span class="f-12"><strong>Telefone: </strong> {{store.phone.join(' | ')}}</span>
            <span class="f-12">{{store.works_at}}</span>
          </div>
          <img src="/svg/direction.svg">
        </div>
      </template>
    </div>
  </b-card>
</template>
<style scoped lang="scss">
.store{
  padding: 20px 24px;

}
</style>
<script>
export default {
  data(){
    return {
      stores:[]
    };
  },
  created(){
    this.getSupplyStores();
  },
  methods:{
    submit(){
      // console.log(`test`);
    },
    getSupplyStores(){
      this.$http.get('/supply/stores')
      .then(function(res){
        if(res.error)
        return this.$toastr.error("Não foi possível carregar as lojas");
        this.stores = res.data.data;
      }.bind(this))
      .catch(function(e){
        return this.$toastr.error("Não foi possível carregar as lojas");
      }.bind(this))
    }
  },
  computed:{

  }
}
</script>
