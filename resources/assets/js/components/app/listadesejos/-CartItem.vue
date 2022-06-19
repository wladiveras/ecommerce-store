<style scoped>
.product_img {
  width: 210px;
  height: 166px;
  margin: 0 auto;
}
</style>
<template>
  <div class="row cart-item">
    <div class="col-sm-12 d-flex flex-wrap justify-content-between flex-md-row">
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
            :width="210"
            :height="166"
            :thumb="true"
            :url="product.files[0].raw_url"
          />
        </div>
      </div>
      <div
        class="col d-flex flex-wrap text-center text-sm-left flex-column flex-md-row justify-content-center justify-content-md-start justify-content-md-between"
      >
        <div class="info col-md-6 px-0 flex-grow-1">
          <h3>{{item.name}}</h3>
          <div class="sku-details">
            <p class="f-12 mb-1">
              <b>{{product.name}}</b>
            </p>
            <template v-for="(variation, key) in item.options">
              <p :key="variation" class="f-12 mb-1">
                <b>{{key}} :</b>
                {{variation}}
              </p>
            </template>
            <template v-if="item.cover">
              <p class="f-12 mb-1">
                <b>Capa :</b>
                {{item.cover}}
              </p>
            </template>
            <template v-if="item.measures">
              <p class="f-12 mb-1">
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
            <div class="py-2">
              <div v-b-toggle="`${index}`">
                <span class="when-opened">
                  Ver menos
                  <i class="mi">keyboard_arrow_up</i>
                </span>
                <span class="when-closed">
                  Ver mais
                  <i class="mi">keyboard_arrow_down</i>
                </span>
              </div>
              <b-collapse :id="index.toString()">
                <div v-if="item.additionalConfig" class="mt-3">
                  <template v-for="add in item.additionalConfig.additional_attributes">
                    <p :key="`${index} ${add.name}`" class="f-12 mb-1">
                      <b>{{add.name}} :</b>
                      <span v-if="add.price>0">{{add.price.currency()}}</span>
                      <span v-else>Grátis</span>
                    </p>
                  </template>
                </div>

                <div v-if="item.finishes" class="mt-3">
                  <template v-for="fin in item.finishes">
                    <p :key="`${index} ${fin.name}`" class="f-12 mb-1">
                      <b>{{fin.qty}}x {{fin.name}} :</b>
                      <span v-if="fin.price>0">{{fin.price.currency()}}</span>
                      <span v-else>Grátis</span>
                    </p>
                    <template v-if="fin.name == 'Corte e Vinco' && fin.qty>0">
                      <p :key="fin.price" class="f-12 mb-1">
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
                <!--
                <div class="mt-3">
                  <p class="f-12 mb-1" v-for="file in item.files.product.files">
                    <b>{{file.label}} : </b> <a target="_blank" class="link" :href="file.raw_url">  Baixar o arquivo enviado</a>
                  </p>
                </div>-->
              </b-collapse>
            </div>
          </div>
        </div>
        <div class="info">
          <h3>Produção</h3>
          <p class="f-12 mb-2">{{item.prepared_in}}</p>
        </div>
        <div class="info d-flex flex-column justify-content-between">
          <div class="info">
            <h3>Quantidade</h3>
            <p class="f-12 mb-1 text-center text-sm-left text-md-center">{{item.quantity}}</p>
          </div>
          <h3
            class="f-22 text-secondary text-center text-sm-left text-md-center"
          >{{item.price.currency()}}</h3>
          <div class="justify-content-end d-flex">
            <a :href="`/lista_desejo/remove/${item.id_cart}`">
              <i class="mi mi-delete"></i>
            </a>
            <a :href="`/lista_desejo/addCart/${item.id_cart}`">
              <i class="mi mi-add"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  
  props: ["item", "index"],
  data() {
    return {
      price: this.item.price
    };
  },
  computed: {
    product() {
      return this.item.product;
    },
    name() {
      return this.item.name;
    },
    id_cart() {
      return this.item.id_cart;
    }
  },
  methods: {
    has_rule(value) {
      return value.match(/[^\d]/) != null;
    }
  }
};
</script>