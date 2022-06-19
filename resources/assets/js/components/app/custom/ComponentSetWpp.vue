<style scoped>
.logoWpp
{
    width:30px;
    margin-right:10px;
}
h5.text1
{
    font-weight:400;
    font-size:18px;
}
</style>
<template>
<div>
    <modal name="modal" :clickToClose="false">
        <form v-on:submit.prevent="submit">
            <div class="card" v-loading="loading">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <img :src="$root.root_url+'/svg/whatsapp-logo.svg'" class="logoWpp"><b>Informe seu whatsapp para notificações</b>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text1 text-center">
                                <p class="m-0">Não perca nenhuma atualização dos seus pedidos.</p>
                                <p class="m-0">Insira seu número de whatsapp para receber notificações e atualizações sobre suas compras.</p>
                            </h5>
                        </div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-md-12">
                            <the-mask placeholder="Digite seu número..." required :mask="['(##) ####-####', '(##) #####-####']" v-model="phonenumber" class="form-control text-center" />
                        </div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-primary btn-block">Confirmar</button>
                        </div>
                    </div>
                    <div class="row pt-4 mb-0">
                        <div class="col-md-12 text-center">
                            <small><a href="#" @click.prevent="close">Não desejo receber notificações</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </modal>
</div>
</template>

<script>
export default {
    props: ["user"],
    data() {
        return {
            phonenumber : null,
            loading : false
        }
    },
    computed : {
        isHome() {
            return window.location.pathname=="/"
        }
    },
    mounted() {
        this.open()
    },
    methods: {
        open() {
            this.$modal.show('modal');
        },
        close() {
            this.loading=true;
            this.$http.post(this.$root.root_url+"/api/wpp/set_wpp_phone",{wpp_phone:null,wpp_notification:false}).then(function (response)
            {
                response = response.data;
                this.$modal.hide('modal');
                this.initIntroJs()
            }.bind(this));
        },
        submit()
        {
            if(this.phonenumber.length<9)
            {
                return this.$toastr.error("Digite o número de telefone corretamente.")
            }
            this.loading=true;
            this.$http.post(this.$root.root_url+"/api/wpp/set_wpp_phone",{wpp_phone:this.phonenumber,wpp_notification:true}).then(function (response)
            {
                response = response.data;
                this.$modal.hide('modal');
                this.initIntroJs()
            }.bind(this));
        },
        initIntroJs() {
            if(this.isHome) {
                this.$intro().start()
            }
        }
    }
}
</script>
