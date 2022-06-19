<template>
  <div>
        <!-- <transition name="bounce"> -->
            
                <a class="wpp-button" href="https://api.whatsapp.com/send?phone=5521971019355&text=Ol%C3%A1" target="_blank"></a>
            
            <!-- <template v-if="opened">
                <div class="card d-flex aling-items-center" >
                    <div class="card-body" v-loading="loading">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-column">
                                <h1 class="float-help title mb-0">PRECISA DE AJUDA ?</h1>
                                <div class="float-help item big">-<a class="link" :href="helper_route"> Central de Ajuda</a></div>
                                <div class="float-help item big">-<a class="link" :href="order_route"> Acompanhar Pedido</a></div>

                                <h2 class="float-help title small mt-4 mb-0">GERENTE DE CONTA</h2>
                                <div class="float-help item small mb-2">{{manager.name}}</div>
                                <div class="float-help phone d-flex align-items-center" v-if="manager.mobile"><img src="/assets/images/wpp_black.png" style="width: 20px;margin-right: 11px;"> {{manager.mobile}}</div>
                                <div class="float-help phone d-flex align-items-center" v-if="manager.phone"><i class="material-icons mr-2">phone</i> {{manager.phone}}</div>
                                <small class="mt-2">Horário de atendimento de segunda a sexta das 9h às 18h</small>
                            </div>
                        </div>
                    </div>
                </div>
            </template> -->
        <!-- </transition> -->
    <!-- <button type="button"  class="btn-floating" @click="open">{{icon}}</button> -->
  </div>
</template>
<script>
export default {
    props:["manager"],
    data() {
        return {
        opened : false,
        loading : false,
        }
    },
    computed : {
        icon() {
            return this.opened ? "X" : "?"
        },
        helper_route() {
            return this.$root.root_url+"/central-de-ajuda"
        },
        order_route() {
            return this.$root.root_url+"/compras"
        }
    },
    methods : {
        open() {
            this.opened = !this.opened
        }
    }
}
</script>
<style scoped lang="scss">
.wpp-button {
    background: url('/assets/images/icon-whatsapp.png') #00a55a no-repeat;
    width: 70px;
    height: 70px;
    position: fixed;
    bottom: 15px;
    right: 20px;
    cursor: pointer;
    box-shadow: 4px 8px 14px -7px;
    border-radius: 100%;
}
.bounce-enter-active {
  animation: bounce-in .5s;
}
.bounce-leave-active {
  animation: bounce-in .5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}
textarea {
  resize : none;
}
.card {
    border-radius: 30px;
    position: fixed;
    border:none;
    right: 50px;
    bottom: 90px;
    -webkit-box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    -moz-box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    z-index: 10;
}
.float-help {
    &.title {
        color: #3C4800;
        font-size: 25px;
        font-weight: 800;
        text-transform: capitalize;
        &.small {
            font-size:20px;
        }
    }
    &.item {
        color : #6C6C6C;
        font-weight: 400;
        &.big {
            font-size: 16px;
        }
        &.small {
            font-size:14px;
        }
    }
    &.phone {
        color :  #3E3E3E;
    }
}
.btn-floating {
    background-color:#2FC5FD;
    color:white;
    border:none;
    height: 60px;
    width: 60px;
    font-size: 30px;
    border-radius: 100%;
    z-index: 10;
    font-weight: 100;
    right: 50px;
    position: fixed;
    bottom: 20px;
    -webkit-box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    -moz-box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    box-shadow: 10px 10px 15px -8px rgba(52,58,64,0.72);
    &:hover {
        background-color:#6cd3f9;
        transition:.5s;
    }
    &:focus {
        outline: none;
    }
}
@media (max-width: 768px) {
    .card {
        right:20px;
    }
    .btn-floating {
        right : 20px;
    }
}
</style>
