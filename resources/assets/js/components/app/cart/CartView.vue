<style lang="scss">

.sugestao-produto > div {
  display: none;
}
.sugestao-produto  > div:first-child, .sugestao-produto  > div:nth-child(2), .sugestao-produto  > div:nth-child(3) {
  display: block;
}
.sugestao-header {
  background: #eef1ff;
  color: #0f0806;
  padding: 20px;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
  margin-top: 20px;
}
.quemviu-header {
  background: #f5a661;
  color: #391109;
  padding: 20px;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
  margin-top: 20px;
}
.card.cart-card {
  border: none;
  background-color: transparent;
  .card-body {
    background: transparent;
  }
  .card-header {
    background-color: transparent;
  }
}

.cart-card .card-header .btn-delete {
  background-color:#d5d8e7;
}
.finish_content .card-header {
  background-color:#eef1ff;
}

.btn-block.btn-color-2 {
  background-color: #7d1756;
  border-color: #7d1756;
  color: #fff;
}
.btn-block.btn-color-3 {
  background-color: transparent;
  border-color: #370700;
  color: #370700;
}

</style>
<template>
<div id="cart-view">
    <div class="row mb-5" v-if="_notempty">      
      <div class="col-sm-12">
        <h2 class="header-title" style="color:#159dad; text-align:center">Leve Também</h2>
        <div class="d-flex flex-wrap justify-content-between product-list-content sugestao-produto">
          <div style="flex-grow:1;" class="col-sm-4" v-for="(ingredient, index) in randomList(ingredients)" :key="'ing'+index">
            <a :href="ingredient.url" class="d-flex">
              <div class="d-flex justify-content-center mt-2" style="width:130px;">
                <div class="product-img">
                  <div class="img-wrapper">
                    <div class="ratio-fill">
                      <div class="img-container d-flex justify-content-center img-loaded">
                        <img crossorigin="Anonymous" :alt="ingredient.title" class="real-image" style="max-width:100%" :src="ingredient.img">                  
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content mt-2 ">
                <p class="f-16 f-space font-weight-bold">{{ ingredient.title }}</p>
                <p class="beggining mb-0">A partir de </p>
                <p class="price mb-0" style="font-size:1.5rem; color:#159dad;">
                  {{ ingredient.price }}
                  <small class="rule"></small>
                </p>
                <div style="background: rgb(23, 153, 168) none repeat scroll 0% 0%; color: rgb(255, 255, 255); padding: 3px 10px; border-radius: 5px; text-align: center;">
                    Ver Produto
                </div>
              </div>        
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="row" v-if="_notempty">
        <div class="col-md-12 mb-3 mb-md-0 view_content">
            <b-card class="cart-card">
                <template slot="header">
                    <div class="d-flex justify-content-md-between flex-column flex-md-row align-items-center" style="border-bottom: 1px solid #159dad;">
                        <h3 class="header-title mb-2 mb-md-0">Sua Sacola de Compras</h3>
                        <div class="d-none d-lg-block">
                            <div class="d-flex">
                                <b-button variant="g-2" class="btn-delete mr-2" @click="clear"><span class="g-6 f-12">Limpar Sacola <i class="mi mi-delete"></i> </span> </b-button>
                                <!-- <b-button variant="blue" class="f-12" @click="download">Baixar Orçamento</b-button> -->
                            </div>
                        </div>
                    </div>
                </template>
                <div class="items-list">
                    <cart-item v-for="(item, key) in cart" :item="item" :key="key" :index="key" />
                </div>
            </b-card>
        </div>        
    </div>
    <div class="row" v-if="_notempty">
      <div class="col-md-6 finish_content" >
      </div>
      <div class="col-md-6 finish_content" >
          <summary-card :_cart="cart" />
          <div class="d-flex">            
            <a href="/" class="btn btn-block btn-color-3 btn-secondary">continuar comprando</a>
            <b-button class="btn-block btn-color-2" variant="secondary" @click="toCheckout">Finalizar Compra</b-button>
          </div>          
      </div>
    </div>
    <div class="row mb-5 mt-5" v-else>
        <div class="col-sm-12 text-center">
            <img :src="'assets/images/empty-cart.png'" >
            
            <h5>Não há itens no carrinho</h5>
            <!-- <p>Você será redirecionado para a loja em <span class="countdown"></span>...</p> -->

            <h3><a class="text-primary" href="/">Voltar à loja</a> </h3>
        </div>
    </div>
    <!-- <div class="row">
      <div class="col-sm-6">
        <div class="sugestao-header">Sugestões para você</div>
        <div class="row product-list-content sugestao-produto">
          <div style="background:#fff; flex-grow:1;" class="col-sm-4" v-for="(ingredient, index) in ingredients.slice(Math.floor(Math.random() * (9 + 1)+1),Math.floor(Math.random() * (9 + 1)+3))" :key="'ing'+index">
            <a :href="ingredient.url" class="col-sm-4 col-md-3 mb-3">
              <div class="d-flex justify-content-center mt-2">
                <div class="product-img">
                  <div class="img-wrapper">
                    <div class="ratio-fill">
                      <div class="img-container d-flex justify-content-center img-loaded">
                        <img crossorigin="Anonymous" :alt="ingredient.title" class="real-image" style="max-width:100%" :src="ingredient.img">                  
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content mt-2 ">
                <p class="f-16 f-space font-weight-bold">{{ ingredient.title }}</p>
                <p class="beggining mb-0">A partir de </p>
                <p class="price mb-0">
                  {{ ingredient.price }}
                  <small class="rule"></small>
                </p>
              </div>        
            </a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="quemviu-header">Quem viu também comprou</div>
        <div class="d-flex flex-wrap justify-content-between product-list-content sugestao-produto">
          <div style="background:#fff; flex-grow:1;" class="col-sm-4" v-for="(ingredient, index) in randomList(ingredients)" :key="'ing'+index">
            <a :href="ingredient.url" class="col-sm-4 col-md-3 mb-3">
              <div class="d-flex justify-content-center mt-2">
                <div class="product-img">
                  <div class="img-wrapper">
                    <div class="ratio-fill">
                      <div class="img-container d-flex justify-content-center img-loaded">
                        <img crossorigin="Anonymous" :alt="ingredient.title" class="real-image" style="max-width:100%" :src="ingredient.img">                  
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content mt-2 ">
                <p class="f-16 f-space font-weight-bold">{{ ingredient.title }}</p>
                <p class="beggining mb-0">A partir de </p>
                <p class="price mb-0">
                  {{ ingredient.price }}
                  <small class="rule"></small>
                </p>
              </div>        
            </a>
          </div>
        </div>
      </div>
    </div> -->
    <div class="row d-lg-none">
        <div class="col-12">
            <b-button variant="blue" class="f-12 btn-block mb-3" @click="download">Baixar Orçamento</b-button>
            <b-button variant="g-2" class="btn-delete btn-block" @click="clear"><span class="g-6 f-12">Limpar Carrinho <i class="mi mi-delete"></i> </span> </b-button>
        </div>
    </div>
</div>
</template>

<script>
// var count = 5;
//   var countdown = setInterval(function(){
//     $("span.countdown").text(count + " segundos");
//     if (count == 0) {
//       clearInterval(countdown);
//       window.open('http://127.0.0.1:8000/', "_self");

//     }
//     count--;
//   }, 1000);
export default {
    props: ['_cart', 'user', '_notempty'],
    components: {
        'summary-card': require("./-CartSummary.vue").default,
        'cart-item': require("./-CartItem.vue").default
    },
    data() {
        return {
          cart: this._cart,
            group_list: [],
            teste: Math.floor(Math.random() * 80),
            ingredients: [
            { title: 'Abadá', price: 'R$ 17,00', url: '/produtos/abada', img: 'https://cdn.otimize.me:82/padraocolor//ABADA.png?height=140&width=140&gravity=center' },
            { title: 'Acrílico', price: 'R$ 23,90', url: '/produtos/acrilico', img: 'https://cdn.otimize.me:82/padraocolor//display-acrilico.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo', price: 'R$ 17,00', url: '/produtos/adesivo', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO APLICADO NO PS .png?height=140&width=140&gravity=center' },
            { title: 'Adesivo Aplicado no PS', price: 'R$ 98,00', url: '/produtos/adesivo-aplicado-no-ps', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO APLICADO NO PS .png?height=140&width=140&gravity=center' },
            { title: 'Adesivo Quadrado', price: 'R$ 9,90', url: '/produtos/adesivo-quadrado', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO QUADRADO-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo Redondo', price: 'R$ 9,90', url: '/produtos/adesivo-redondo', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO REDONDO-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo Retangular', price: 'R$ 4,50', url: '/produtos/adesivo-retangular', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO RETANGULAR-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo Seta', price: 'R$ 73,90', url: '/produtos/adesivo-seta', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO SETA-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo Tampa Marmita', price: 'R$ 56,00', url: '/produtos/adesivo-tampa-marmita', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO PARA TAMPA DE MARMITA-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Adesivo Triângulo', price: 'R$ 8,00', url: '/produtos/adesivo-triangulo', img: 'https://img.padraocolor.com.br/padraocolor//ADESIVO TRIANGULAR-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Agenda', price: 'R$ 16,00', url: '/produtos/agenda', img: 'https://img.padraocolor.com.br/padraocolor//AGENDA.png?height=140&width=140&gravity=center' },
            { title: 'Álbum de figurinhas', price: 'R$ 25,00', url: '/produtos/album-de-figurinhas', img: 'https://img.padraocolor.com.br/padraocolor//mockupok.jpg?height=140&width=140&gravity=center' },
            { title: 'Almofada para colorir', price: 'R$ 24,65', url: '/produtos/almofada-para-colorir', img: 'https://img.padraocolor.com.br/padraocolor//ALMOFADA PARA COLORIR-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Almofada para o pescoço', price: 'R$ 30,00', url: '/produtos/almofada-para-pescoco', img: 'https://img.padraocolor.com.br/padraocolor//ALMOFADA PARA PESCOÇO-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Almofada sublimática', price: 'R$ 26,10', url: '/produtos/almofada-sublimatica', img: 'https://img.padraocolor.com.br/padraocolor//ALMOFADA .png?height=140&width=140&gravity=center' },
            { title: 'Avental', price: 'R$ 38,25', url: '/produtos/avental', img: 'https://img.padraocolor.com.br/padraocolor//avental-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Avental para colorir', price: 'R$ 38,25', url: '/produtos/avental-para-colorir', img: 'https://img.padraocolor.com.br/padraocolor//AVENTAL INFANTIL-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bandana', price: 'R$ 15,00', url: '/produtos/bandana', img: 'https://img.padraocolor.com.br/padraocolor//stylish-brunet-man-wearing-red-bandana-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bandana Pet', price: 'R$ 15,90', url: '/produtos/bandana-pet', img: 'https://img.padraocolor.com.br/padraocolor//young-man-wearing-red-bandana-and-black-shirt-and-his-dog-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bandeira Grande', price: 'R$ 23,90', url: '/produtos/bandeira-grande', img: 'https://img.padraocolor.com.br/padraocolor//MOCKUP-BANDEIRA2.jpg?height=140&width=140&gravity=center' },
            { title: 'Bandeira Pequena', price: 'R$ 23,90', url: '/produtos/bandeira-pequena', img: 'https://img.padraocolor.com.br/padraocolor//MOCKUP-BANDEIRA2.jpg?height=140&width=140&gravity=center' },
            { title: 'Bandeirola', price: 'R$ 23,90', url: '/produtos/bandeirola', img: 'https://img.padraocolor.com.br/padraocolor//4 - BANDEIROLA.png?height=140&width=140&gravity=center' },
            { title: 'Bandeja', price: 'R$ 23,90', url: '/produtos/bandeja', img: 'https://img.padraocolor.com.br/padraocolor//BANDEJA.png?height=140&width=140&gravity=center' },
            { title: 'Banner', price: 'R$ 23,90', url: '/produtos/banner', img: 'https://img.padraocolor.com.br/padraocolor//BANNER2.png?height=140&width=140&gravity=center' },
            { title: 'Blusa com Manga Longa U.V', price: 'R$ 23,90', url: '/produtos/blusa-com-manga-longa-u-v', img: 'https://img.padraocolor.com.br/padraocolor//BLUSA COM MANGA UV-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bolsa com Alça', price: 'R$ 23,90', url: '/produtos/bolsa-com-alca', img: 'https://img.padraocolor.com.br/padraocolor//BOLSA215.jpg?height=140&width=140&gravity=center' },
            { title: 'Bolsa Córdoba', price: 'R$ 23,90', url: '/produtos/bolsa-cordoba', img: 'https://img.padraocolor.com.br/padraocolor//BOLSA CORDOBA-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bolsa para Vinho', price: 'R$ 23,90', url: '/produtos/bolsa-para-vinho', img: 'https://img.padraocolor.com.br/padraocolor//BOLSA PARA VINHO-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Bolsinha', price: 'R$ 23,90', url: '/produtos/bolsinha', img: 'https://img.padraocolor.com.br/padraocolor//BOLSINHA.png?height=140&width=140&gravity=center' },
            { title: 'Caderno', price: 'R$ 23,90', url: '/produtos/caderno', img: 'https://img.padraocolor.com.br/padraocolor//CADERNO.png?height=140&width=140&gravity=center' },
            { title: 'Caixa Combinado Japonês', price: 'R$ 153,90', url: '/produtos/caixa-combinado-japones', img: 'https://img.padraocolor.com.br/padraocolor//14 - CAIXA COMBINADO JAPONÊS.jpg?height=140&width=140&gravity=center' },
            { title: 'Caixa de Batata Frita', price: 'R$ 35,00', url: '/produtos/caixa-de-batata-frita', img: 'https://img.padraocolor.com.br/padraocolor//CAIXA DE BATATA FRITA.png?height=140&width=140&gravity=center' },
            { title: 'Caixa de Hambúrguer', price: 'R$ 49,90', url: '/produtos/caixa-de-hamburguer', img: 'https://img.padraocolor.com.br/padraocolor//CAIXA DE HAMBURGUER.png?height=140&width=140&gravity=center' },
            { title: 'Caixa de Pipoca', price: 'R$ 28,00', url: '/produtos/caixa-de-pipoca', img: 'https://img.padraocolor.com.br/padraocolor//CAIXA DE PIPOCA.png?height=140&width=140&gravity=center' },
            { title: 'Caixa de Presente', price: 'R$ 158,62', url: '/produtos/caixa-de-presente', img: 'https://img.padraocolor.com.br/padraocolor//13 - CAIXA DE PRESENTE.png?height=140&width=140&gravity=center' },
            { title: 'Caixa de Presente com Alça', price: 'R$ 77,62', url: '/produtos/caixa-de-presente-com-alca', img: 'https://img.padraocolor.com.br/padraocolor//12 - CAIXA DE PRESENTE COM ALCA.png?height=140&width=140&gravity=center' },
            { title: 'Caixa Fatia de Bolo', price: 'R$ 50,62', url: '/produtos/caixa-fatia-de-bolo', img: 'https://img.padraocolor.com.br/padraocolor//11 - CAIXA FATIA DE BOLO.jpg?height=140&width=140&gravity=center' },
            { title: 'Caixa Panetone', price: 'R$ 50,62', url: '/produtos/caixa-panetone', img: 'https://img.padraocolor.com.br/padraocolor//panetone-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Caixa Presente', price: 'R$ 23,90', url: '/produtos/caixa-presente', img: 'https://img.padraocolor.com.br/padraocolor//PRESENTE-red.jpg?height=140&width=140&gravity=center' },
            { title: 'Caixa Surpresa', price: 'R$ 35,00', url: '/produtos/caixa-surpresa', img: 'https://img.padraocolor.com.br/padraocolor//CAIXA SURPRESA.png?height=140&width=140&gravity=center' },
            { title: 'Caixa Travesseiro', price: 'R$ 122,85', url: '/produtos/caixa-travesseiro', img: 'https://img.padraocolor.com.br/padraocolor//10 - CAIXA TRAVESSEIRO.png?height=140&width=140&gravity=center' },
            { title: 'Caixa Triangular', price: 'R$ 35,00', url: '/produtos/caixa-triangular', img: 'https://img.padraocolor.com.br/padraocolor//CAIXA TRIANGULAR.png?height=140&width=140&gravity=center' },
          ],
        }
    },
    methods: {
        clear() {
            window.location = "/carrinho/limpar";
        },
        download() {
            window.open("/orcamento", '_blank');
        },
        toCheckout() {
            window.location = "/checkout";
        },
        randomList: function(rand){
          return rand.sort(function(){return 0.5 - Math.random()});
        }
    }
}
</script>

<style scoped lang="scss">
    @media only screen and (max-width: 640px)  {
        .finish_content {
            order:1;
            margin-bottom : 20px;
        }
        .view_content {
            order:2;
        }
    }
    
</style>
