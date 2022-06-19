<template>
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-7 col-sm-12 product_img_container">
          <div class="product_img_thumb_container">
            <img                        
                  v-for="(file,i) in product.files.slice(1, 3)" :key="i"
                  class="product_img_thumb"
                  :src="file.raw_url"
                  style="max-width:100%"                       
              />
          </div>
          <div>
            <img                        
                  v-for="(file,i) in product.files.slice(0,1)" :key="i"
                  class="product_img"
                  :src="file.raw_url"
                  style="max-width:100%"                       
              />
          </div>
            
                
                    
                
            
        </div>
        <div class="col-md-5 col-sm-12">
            <div class="title_row">
                <slot name="categories" />
                <slot name="title" />
            </div>
            <component-rate v-if="product.rate" :val="product.rate" readonly>
                <span class="pt-1">
                    <b>{{Number(product.rate).toFixed(1)}}</b>
                </span>
            </component-rate>
            <!-- <slot name="short_description" /> -->
            <div class="price_row">
                <template v-if="show_price">
                    <div
                        class="from_for"
                        v-if="product.viewPrice.promo_value>0 && product.viewPrice.promo_type!='hide'"
                    >De {{promo_price.currency()}} por</div>
                    <div class="beginning">A partir de</div>
                    <h4 class="price">
                        {{product.viewPrice.price.currency()}}
                        <small
                            class="rule"
                        >{{product.viewPrice.rule}}</small>                        
                    </h4>
                    <slot name="short_description" />

                    <div v-if="product_buy == 1" class="d-flex flex-row align-content-center align-items-start">
                       <a class="btn btn-primary config mr-2 text-uppercase d-flex justify-content-center align-items-center bt-product-name" :href="`/produtos/${product.slug}/configuracao_produto`"  style="width:100%;">Comprar <slot name="title" /></a>                       
                    </div>

                    <div v-if="product_buy == 1" class="d-flex mt-4 flex-row align-content-center align-items-start">
                        <br>
                        <div>
                          <h5>Digite a quantidade</h5>
                          <input type="number" @change="change" class="pl-5 form-control count-input" :min="min" :max="max" v-model="qty" placeholder="Digite a quantidade aqui...">
                      </div>
                    </div>


                    <div v-else class="d-flex flex-row align-content-center align-items-start"
                        v-bind:style= "[product.status==2 ? {'display': 'none !important'} : {}]">
                       <a class="btn btn-primary config mr-2 text-uppercase d-flex justify-content-center align-items-center bt-product-name" :href="`/produtos/${product.slug}/configuracao_produto`"  style="width:100%;">Criar <slot name="title" /></a>                       
                    </div>
                   
                   <div v-bind:style= "[product.status!=2 ? {'display': 'none !important'} : {}]">
                       <div class="text-uppercase"
                            style=" background: #ccc;
                                    text-align: center;
                                    padding: 10px;
                                    font-weight: bold;
                        ">
                            Temporariamente Indisponivel
                        </div>
                       <div style="margin-top: 10px; border-top: solid 1px #ccc;">
                           <p style="
                                    padding: 8px 10px;
                                    color: #07a92a;
                                    font-weight: bold;
                                    margin: 0;">
                                    Se quiser receber um aviso quando este produto estiver disponivel novamente, informe seu email:
                            </p>                           
                           <form action="" style="display:flex">
                               <input type="text"
                                    placeholder="Digite seu email"
                                    style="flex-grow: 1;
                                           margin-right: 5px;
                                ">
                               <button class="btn btn-secondary">Avise-me quando chegar</button>
                           </form>
                        </div>                       
                   </div>
                </template>
                <template v-else>
                    <div class="beginning">Você ainda não efetuou login</div>
                    <h4 class="price sm">Entre para ver os preços</h4>
                    <a
                        class="btn btn-primary config btn-block"
                        :href="`/login?redirect=/produtos/${product.slug}`"
                    >ENTRAR</a>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['product', 'user', 'product_buy'],
    components: {
        showMoreComponent: require("./-showMore.vue").default
    },
    data(){
      return{
        min: 1,
        max: 999999999,
        qty: null
      }
    },
    methods:{
      change() {
        localStorage.setItem('qty_item', this.qty)
      },
    },
    computed: {
        promo_price() {
            let pricing_data = this.product.viewPrice
            if (pricing_data.promo_type == "pp") {
                let price = pricing_data.price
                let value = pricing_data.promo_value
                let percentage = price * value / 100
                return Number(price) + Number(percentage)
            }
            if (pricing_data.promo_type == "rl") {
                let price = pricing_data.price
                let value = pricing_data.promo_value
                return Number(price) + Number(value)
            }
            if (pricing_data.promo_type == "ex") return pricing_data.promo_value
        },
        show_price() {
            return true
        },
        config_route() {
            return `${window.location.href}/configuracao_produto`
        },
    }
}
</script>
<style lang="scss">
.bt-product-name
  .product_name {
  color: #fff !important;
  font-size: 0.9rem !important;
  margin-left: 5px;
}
.product_img_container {
  display: flex;
  .product_img_thumb_container {
    width: 45%;
    margin-right: 30px;
  }
  .product_img_thumb, .product_img {
    background: #ffa401;
    border-radius: 15px;
  }
  .product_img_thumb:first-child {
    margin-bottom: 10%;
  }
  
}
.wrapper-tooltip {
  --background: #62ABFF;
  --icon-color: #414856;
  --shape-color-01: #B8CBEE;
  --shape-color-02: #7691E8;
  --shape-color-03: #FDD053;
  --width: 100%;
  --height: 30px;
  --border-radius: var(--height);
  width: var(--width);
  height: var(--height);
  position: relative;
  border-radius: var(--border-radius);
  display: flex;
  justify-content: center;
  align-items: center;
  .btn {
    background: #f5a660;
    width: 25px;
    height: 25px;
    position: relative;
    z-index: 3;
    border-radius: var(--border-radius);
    box-shadow: 0 10px 30px rgba(#414856, 0.05);
    display: flex;
    justify-content: center;
    align-items: center; 
    animation: plus-animation-reverse .5s ease-out forwards;
    &::before,
    &::after {
      content: "";
      display: block;
      position: absolute;
      border-radius: 4px;
      background: #fff;
    }
    &::before {
      width: 3px;
      height: 15px;
    }
    &::after {
      width: 15px;
      height: 3px;
    }
  }
  .tooltip {
    width: 220px;    
    border-radius: 20px;
    position: absolute;
    background: #f5a660;
    z-index: 2;
    padding: 15px;
    box-shadow: 0 10px 30px rgba(#414856, 0.05);
    opacity: 0;
    top: 0;
    transition: opacity .15s ease-in, top .15s ease-in, width .15s ease-in;
    > svg {
      width: 100%;
      height: 26px;
      display: flex;
      justify-content: space-around;
      align-items: center;
      cursor: pointer;
      .icon {
        fill: none;
        stroke: var(--icon-color);
        stroke-width: 2px;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: .4;
        transition: opacity .3s ease;
      }
      &:hover {
        .icon {
          opacity: 1;
        }
      }
    }
    &::after {
      content: "";
      width: 20px;
      height: 20px;
      background: #f5a660;
      border-radius: 3px;
      position: absolute;
      left: 50%;
      margin-left: -10px;
      bottom: -8px;
      transform: rotate(45deg);
      z-index: 0;
    }
  }
  > svg {
    width: 300px;
    height: 300px;
    position: absolute;
    z-index: 1;
    transform: scale(0);
    .shape {
      fill: none;
      stroke: none;
      stroke-width: 3px;
      stroke-linecap: round;
      stroke-linejoin: round;
      transform-origin: 50% 20%;
    }
  }
  input {
    height: 30px;
    width: 100%;
    border-radius: var(--border-radius);
    cursor: pointer;
    position: absolute;
    z-index: 5;
    opacity: 0;
    &:checked {
      ~ svg {
        animation: pang-animation 1.2s ease-out forwards;
        .shape {
          @for $shape from 1 through 9 {
            &:nth-of-type(#{$shape}) {
              transform: translate(random(50) - 25 + px, 30%) rotate(40deg*$shape);
            }
          }
        }
      }
      ~ .btn {
        animation: plus-animation .5s ease-out forwards;
      }
      ~ .tooltip {
        width: 220px;
        
        animation: stretch-animation 1s ease-out forwards .15s;
        top: -270px;
        opacity: 1;
      }
    }
  }
}

@keyframes pang-animation {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(1.1);
    opacity: 0;
  }
}
@keyframes plus-animation {
  0% {
    transform: rotate(0) scale(1);
  }
  20% {
    transform: rotate(60deg) scale(.93);
  }
  55% {
    transform: rotate(35deg) scale(.97);
  }
  80% {
    transform: rotate(48deg) scale(.94);
  }
  100% {
    transform: rotate(45deg) scale(.95);
  }
}
@keyframes plus-animation-reverse {
  0% {
    transform: rotate(45deg) scale(.95);
  }
  20% {
    transform: rotate(-15deg);
  }
  55% {
    transform: rotate(10deg);
  }
  80% {
    transform: rotate(-3deg);
  }
  100% {
    transform: rotate(0) scale(1);
  }
}
@keyframes stretch-animation {
  0% {
    transform: scale(1,1)
  }
  10% {
    transform: scale(1.1,.9)
  }
  30% {
    transform: scale(.9,1.1)
  }
  50% {
    transform: scale(1.05,.95)
  }
  100% {
    transform: scale(1,1)
  }
}
</style>
