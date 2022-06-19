<style scoped>
.product_img {
  width: 150px;
  height: 150px;
  margin: 0 auto;
}
.cart-item .product_img .img-wrapper{
  background: #ffbf00;
  border-radius: 10px;
}
</style>
<template>
  <div class="row cart-item">
    <div class="col-sm-12 d-flex flex-wrap justify-content-between flex-md-row align-items-center">
      <a :href="`/carrinho/remove/${index}`" style="margin-right:10px; color:#534f4f7d">
        <i class="mi mi-delete"></i>
      </a>
      <div class="product-img flex-grow-1 flex-md-grow-0">
        <div class="d-flex justify-content-center product_img">
          <img
            class="img-fluid flex-shrink-1"
            v-if="!product.files.length"
            src="/assets/images/placeholder-thumbnail.png"
          >
          <img-loader
            class="flex-shrink-1"
            v-else
            :width="150"
            :height="150"
            :thumb="true"
            :url="product.files[0].raw_url"
          />
        </div>
      </div>
      <div
        class="col d-flex flex-wrap text-center text-sm-left flex-column flex-md-row justify-content-center justify-content-md-start justify-content-md-between"
      >
        <div class="info col-md-6 px-0 flex-grow-1">
          <!--<h3>{{item.name}}</h3>-->
          <div class="sku-details">
            <p class="f-12 mb-0">
              <b>{{product.name}}</b>
            </p>
            <template v-for="(variation, key) in item.options">
              <p :key="variation" class="f-12 mb-0">
                <b>{{key}} :</b>
                {{variation}}
              </p>
            </template>
            <template v-if="item.cover">
              <p class="f-12 mb-0">
                <b>Capa :</b>
                {{item.cover}}
              </p>
            </template>
            <template v-if="item.measures">
              <p class="f-12 mb-0">
                <b>Medidas :</b>
                {{item.measuresText}}
              </p>
            </template>
            <div v-if="item.sizes" class="mt-3">
              <p class="mb-0">
                <b>Tamanhos:</b>
              </p>
              <div class="col-sm-12 d-flex align-items-center align-items-sm-start flex-column">
                <template v-for="(group,i) in item.sizes">
                  <div class="row" :key="i">
                    <template v-for="size in group">
                      <div style="min-width:100px;" :key="`${i}-${size.label}`">
                        <p :key="size.label" class="f-12 mb-1 mr-2">
                          <b>{{size.label}} :</b>
                          {{size.quantity}}
                        </p>
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </div>
            <div>
              <!-- <div v-b-toggle="`${index}`">
                <span class="when-opened">
                  Ver menos
                  <i class="mi">keyboard_arrow_up</i>
                </span>
                <span class="when-closed">
                  Ver mais
                  <i class="mi">keyboard_arrow_down</i>
                </span>
              </div> -->
              <!-- <b-collapse :id="index"> -->
                <div v-if="item.additionalConfig">
                  <template v-for="add in item.additionalConfig.additional_attributes">
                    <p :key="`${index} ${add.name}`" class="f-12 mb-0">
                      <b>{{add.name}} :</b>
                      <span v-if="add.price>0">{{add.price.currency()}}</span>
                      <span v-else>Grátis</span>
                    </p>
                  </template>
                </div>

                <div v-if="item.finishes" class="mt-3">
                  <template v-for="fin in item.finishes">
                    <p :key="`${index} ${fin.name}`" class="f-12 mb-0">
                      <b>{{fin.qty}}x {{fin.name}} :</b>
                      <span v-if="fin.price>0">{{fin.price.currency()}}</span>
                      <span v-else>Grátis</span>
                    </p>
                    <template v-if="fin.name == 'Corte e Vinco' && fin.qty>0">
                      <p :key="fin.price" class="f-12 mb-0">
                        <b>1x Faca :</b> R$ 150,00
                        <el-tooltip
                          placement="top"
                          content="Preço adicional aplicado quando há corte e vinco selecionado"
                        >
                          <span class="ml-1 py-1 badge badge-pill badge-dark rounded-circle">?</span>
                        </el-tooltip>
                      </p>
                    </template>
                  </template>
                </div>
                <div>
                  <p class="f-12 mb-0" v-for="file in item.files">
                    <b>{{file.label}} : </b> 
                    <a target="_blank" class="link" :href="file.file.raw_url"> Baixar o arquivo enviado</a>
                  </p>
                </div>
              <!-- </b-collapse> -->
            </div>
          </div>
        </div>
        <div class="info">
          <h3>Quantidade</h3>
          <p class="f-12 mb-1 text-center text-sm-left text-md-center">{{item.quantity}}</p>
        </div>
        <div class="info">
          <h3>Tempo de Produção</h3>
          <p class="f-12 mb-2">{{item.prepared_in}}</p>
        </div>        
        <div class="info">
          <h3>Total</h3>
          <p class="f-18 mb-1 text-center text-sm-left text-md-center" style="color: #7d1756">
            {{item.price.currency()}}
          </p>
        </div>
        
          
          
          <!-- <div class="justify-content-end d-flex">
            <a :href="`/carrinho/remove/${index}`">
              <i class="mi mi-delete"></i>
            </a>
          </div> -->
        
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["item", "index"],
  data() {
    console.log(this.item.files)
    return {
      price: this.item.price,
    };
  },
  computed: {
    product() {
      return this.item.product;
    }
  },
  methods: {
    has_rule(value) {
      return value.match(/[^\d]/) != null;
    }
  }
};
</script>