<template>
  <div class="shipping-locations">
    <slot></slot>
    <div class="d-flex flex-wrap row">
      <b-form-radio-group v-model="chosen" class="hide-mark d-sm-flex flex-wrap col-sm-12">
        <template v-for="(location,i) in rLocations">
          <b-form-radio :key="location.id" :value="location.id" class="col-sm-4" style="padding-bottom:15px">
            <div class="shipping-item item rounded p-4" @click="choose(location,i)">
              <div class="flex-wrap d-flex justify-content-between">
                <span class="f-16 font-weight-bold text-uppercase">{{location.name}}</span>
                <check-circle v-if="mode == 'select'" />
              </div>
              <strong class="f-12 f-space">Endereço</strong>
              <div class="f-12 f-space">
                {{location.street}}, {{location.number}} - {{location.district}} - {{location.zip_code}} - {{location.state}} - {{location.city}}
              </div>
            </div>
          </b-form-radio>
        </template>
        <div class="col-sm-4 new-address px-0" @click="bAddress = Object.assign({},emptyAddress); display = !display">
          <div class="shipping-item item rounded p-4">
            <div class="flex-wrap d-flex justify-content-center">
              <i class="mi mi-add f-48 g-5"></i>
            </div>
            <div class="f-12 g-5 text-center">
              <strong class="f-12 g-6">Outro local de entrega</strong><br/>
              Insira um novo local de entrega para Frete
            </div>
          </div>
        </div>
      </b-form-radio-group>
    </div>
    <sweet-portal ref="portal" :trigger="display" v-on:close="display = false" title="Cadastrar Endereço" size="full-width">
      <shipping-modal ref="modal" v-on:new="newAddress" :type="type" v-on:saved="insertAddress" :index="index" v-on:updated="display = false" v-on:deleted="erased" :mode="mode" :b-address="bAddress"/>
    </sweet-portal>
  </div>
</template>

<script>
export default {
  props:{
    'locations':{
      default(){
        return [];
      }
    },
    'main':{},
    type:{
      default:'normal'
    },
    displayMain:Boolean,
    mode:{
      default:'select',
      validator: function (v) {
        return ['edit','select'].indexOf(v) !== -1
      }
    }
  },
  components:{'shipping-modal':require('./-ShippingModal.vue').default},
  data(){
    return {
      rLocations: this.locations,
      index:null,
      chosen: '',
      display: false,
      bAddress:{
        id:'',
        type: '',
        name:'',
        city:'',
        district:'',
        state:'',
        zip_code:'',
        street:'',
        number:'',
        complement:'',
        reference:'',
      },
      emptyAddress:{
        id:'',
        type: '',
        name:'',
        city:'',
        district:'',
        state:'',
        zip_code:'',
        street:'',
        number:'',
        complement:'',
        reference:'',
      }
    }
  },
  watch:{
    locations(n){
      this.rLocations = n
      this.$refs.modal.$forceUpdate()
    }
  },
  methods:{
    erased(index){
      this.rLocations.splice(index,1);
    },
    insertAddress(address){
      this.emit(address);
      if(this.display){
        this.display = false;
      }
    },
    choose(address,i){
      this.index = i;
      this.bAddress = address;
      switch (this.mode) {
        case 'select':
          return this.emit(address)
        case 'edit':
          return this.edit(address)
      }
    },
    edit(address){
     this.display = true;
    },
    newAddress(address){
      this.$emit('new',address,this.type)
      if(this.mode == 'edit'){
        this.rLocations.push(address)
      }
      this.chosen = address.id
      this.index = 0
    },
    emit(address){
      this.bAddress = {
        id:'',
        type: '',
        name:'',
        city:'',
        district:'',
        state:'',
        zip_code:'',
        number:'',
        complement:'',
        reference:''
      }
      this.$emit('chosen',address)
    }
  }
}
</script>
