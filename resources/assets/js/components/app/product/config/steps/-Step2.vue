<template>
<div ref="content" id="step2" style="background: #cccccc40;">
    <div class="card-header finished" style="display:none; margin-bottom:0 !important" v-if="finished" v-show="data.product.customizable == '0'">
        <i class="mi mi-check"></i>
        <b>{{title}}</b>
    </div>
    <div v-else v-show="data.product.customizable == '0'">
        <b>{{title}}</b>
    </div>
    <transition name="fade">
        <div v-show="(shared.step.position==2)">
            <div>
                <content-options ref="options" :shared="shared" :data="data" />
                <div id="card-options-anchor"
                    style="position: absolute; bottom: 260px;">
                </div>
            </div>            
            <div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 text-right">
                        <button class="btn-back btn-sm-block float-left" @click="back">Voltar</button>
                        <button class="btn-continue btn-sm-block float-right" @click="confirm" :disabled="!ready" v-bind:class="{'shaking':ready}">Continuar</button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</div>
</template>

<script>
export default {
    props: ['shared', 'data'],
    data() {
        return {
            confirmed: false,
            ready: false
        }
    },
    components: {
        'content-options': require('./partials/options/-Options.vue').default,
    },
    computed: {
        title() {
            return "SELECIONE AS OPÇÕES";
        },
        finished() {
            return (this.ready && this.confirmed);
        },
    },
    mounted() {
        if (this.data.product.customizable == '1') {
            this.loadConfigs()
            this.ready = false;
            this.shared.step.the_road_so_far.push(2);
            this.shared.data.ref_name = "Cria Fácil";
            this.confirm()
            return 
        }
        if(this.shared.isPreset) {
            this.loadConfigs()
            this.shared.step.the_road_so_far.push(2);
            this.confirm()
            return
        }
        //if(this.shared.isPreset) return this.confirm()
        //if(this.shared.isPreset) return this.confirm() || this.nextStep()
        this.loadConfigs();
        this.checkOptions();
        this.$scrollTo("#products-breadcrumb");
    },
    methods: {
        calcFrete12() {
            console.log("vai");
        
            let url = `http://www.jadlog.com.br/embarcador/api/pickup/pudos/22290040`;
            this.gettingPrices = true;
            this.$http.post(url)
            .then(function (res) {
                this.shippingMethods = res.data.calcs;
                this.gettingPrices = false;
            }.bind(this))
            .catch(function (res) {
                return this.$toastr.error("Não foi possível calcular o frete, tentando novamente...");
                this.gettingPrices = false;
            }.bind(this));
            
        
        },
        checkOptions() {
            this.$http.post(this.$root.root_url + "/produtos/check_has_options", {
                product_id: this.data.product.id
            }).then(response => {
                response = response.data;
                if(!response.success) {
                    this.data.has_qty = true;
                    return this.confirm();
                }
                this.nextStep()
            });
        },
        nextStep(){
            this.shared.step.the_road_so_far.push(2);
                $(this.$refs.content).show();
        },
        loadConfigs() {
            let chosen = this.shared.data.chosen;
            let configs = this.data.configs.find(x => x.type==chosen);
            this.shared.step.configs = configs;
            this.shared.step.configs_bkp = configs;
            this.shared.step.has_configs = configs!=undefined;
        },
        back() {
            this.shared.step.position--;
            this.$parent.$refs.step1.confirmed = false;
            //this.shared.data.options = {};
            //this.shared.data.measures.height = null;
            //this.shared.data.measures.width = null;
            this.$parent.$parent.back_step();
        },
        confirm() {
            this.confirmed = true;
            this.shared.step.position++;
            window.scrollTo(0,0);
        }
    }
}
</script>
