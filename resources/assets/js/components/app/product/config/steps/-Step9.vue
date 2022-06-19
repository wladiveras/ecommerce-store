<style>
.subsection-title {
    color: #e62229;
    text-transform: uppercase;
    margin-top: 30px;
}
#step9 .card-footer {
    background: transparent;

}

</style>
<template>
<div class="card tab_deactive"  id="step9">
    TESTE
    <div class="card-header finished" style="display:none;" v-if="finished">
        <i class="mi mi-check"></i>
        <b>{{title}}</b>
        <small class="ml-3">{{shared.data.ref_name}}</small>
    </div>
    <div class="card-header" style="display:none;" v-else>
        <b>{{title}}</b>
    </div>
    <transition name="fade">
        <div v-show="(shared.step.position==9)">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 px-4" style="border-right: 1px solid #ffffff4a;">
                        <h5 class="subsection-title" style="color: #ffbf00;">ENVIE SUA ARTE</h5>
                        <template v-if="shared.data.chosen === 'UP'">
                            <content-send-art
                                ref="sendart"
                                v-if="showComponent"
                                :shared="shared"
                                :componentInfo="componentInfo"/>
                            <div class="row" v-if="this.fileReady && shared.step.show_send_art_terms">
                                <div class="col-12">
                                    <label class="float-right">
                                        <input type="checkbox" @click="set_ref_name" v-model="accepted" class="mr-2" />
                                            Concordo que a arte enviada é de minha responsabilidade e está de acordo com os
                                            <a
                                                target="_BLANK"
                                                :href="`${$root.root_url}/termos-de-uso/normas-de-envio`"
                                                class="link"
                                            >termos de envio de arte.</a>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="col-md-6 px-4">
                        <div>
                            <p style="margin-top: 30px;">Ao enviar sua arte você concorda que:</p>
                            <ul>
                                <li>Não haverá correção ortográfica</li>
                                <li>O corte escolhido na hora da compra será respeitado, mesmo que isso interfira na arte</li>
                                <li>A arte enviada é de sua responsabilidade</li>
                            </ul>
                            <h5 style="color: #ffbf00;text-transform: uppercase;margin-top: 40px;">Está com dúvidas sobre a sua arte?</h5>
                            <h2>Solicite ajuda profissional.</h2>
                        </div>
                        <card-check-additional :shared="shared" :data="data" :config="config" />
                    </div>
                </div>
                <!-- <h5 class="subsection-title">INFORMAÇÕES DE REFERÊNCIA</h5> -->
                <div class="row">
                    <div class="col-md-12">
                        <input class="form-control mb-3" style="visibility: hidden;" ref="ref_name" v-model="shared.data.ref_name" placeholder="Digite aqui o nome da arte ..." required>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12">
                        <textarea class="form-control" rows="6" v-if="shared.step.position>=8" v-model="shared.data.comment" placeholder="Escreva aqui caso tenha alguma comentário adicional ..."></textarea>
                    </div>
                </div> -->
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-back float-left" @click="back">Voltar</button>
                        <button class="btn-continue float-right" :disabled="!ready" v-bind:class="{'shaking': ready}"  @click="confirm">Continuar</button>
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
            showComponent: true,
            accepted: false,
            fileReady:false,
            title : "INFORMAÇÕES DE REFERÊNCIA",
            componentInfo: {
                segment: null,
                color: null,
                hasCutAndCrease: false,
                hasLocalizedVarnish: false
            }
        }
    },
    components: {
        'content-send-art': require('./partials/upload/-SendArt.vue').default,
        'web-2-print': require('./partials/upload/-Web2Print.vue').default,
        'card-check-additional': require('./partials/additional/-Additional.vue').default,
    },
    mounted() {
        this.showComponent = true
        this.shared.step.the_road_so_far.push(9);
        window.scrollTo(0,100);
        this.load();
        //return this.$scrollTo("#step9");
    },
    computed: {
        finished() {
            return this.confirmed;
        },
        ready() {
            if (this.shared.data.chosen === 'UP' && this.shared.step.show_send_art_terms) {
                return this.accepted && this.fileReady && this.shared.data.ref_name && this.adicionalReady();
            }

            return (this.shared.data.ref_name && this.adicionalReady());
        },
        config() {
            let chosen = this.shared.data.chosen
            let res = this.data.configs.find(x => x.type == chosen)
            return res
        },
    },
    methods: {
        set_ref_name()
        {
            this.shared.data.ref_name = "Cria Fácil";
        },
        back() {
            if(this.actual_step>1)
                return this.$refs.steps.previous();
            this.$parent.$parent.back_step();
        },
        next_step()
        {
            this.confirmed = true;
            this.shared.step.position++;
        },
        confirm() {

            if (this.shared.data.chosen == 'UP' && !this.isMostruario()) {
                this.shared.data.upload.file = this.$refs.sendart.files;
            }

            if(this.actual_step<=3)
                return this.$refs.steps.next();
            this.next_step()
            this.$scrollTo("#products-breadcrumb")

        },
        load() {
            this.getSegment()
            this.getColor()
            this.getHasCutAndCrease()
            this.componentInfo.hasLocalizedVarnish = this.getHasLocalizedVarnish()
            this.showComponent = true
        },
        isMostruario() {
            let name = this.data.product.name
            return name.toUpperCase().indexOf("MOSTRU") >= 0
        },
        getColor() {
            this.componentInfo.color = this.shared.data.options.Cor
        },
        getSegment() {
            this.componentInfo.segment = this.data.product.department
        },
        getHasLocalizedVarnish() {
            let options = this.shared.data.options
            if (options["Acabamento Padrão"]) {
                if ([this.localizedVarnish, this.localizedVarnish2].includes(options["Acabamento Padrão"])) return true
            }
            return false
        },
        getHasCutAndCrease() {
            let finishes = this.shared.data.finishes
            this.componentInfo.hasCutAndCrease = false
            let aux = []
            Object.keys(finishes).forEach((key) => {
                let name = finishes[key].name.replace(/(\r\n|\n\r)/g, ' ').replace(/\s/g, '_')
                aux.push(finishes[key])
                if ((name == this.cutAndCrease) && (finishes[key].qty > 0)) this.componentInfo.hasCutAndCrease = true
            })
        },
        isMostruario() {
            let name = this.data.product.name
            return name.toUpperCase().indexOf("MOSTRU") >= 0
        },
        adicionalReady() {
            if (!this.shared.step.has_configs)
                return false
            let config = this.shared.step.configs
            let attr = config.attr
            for (let i = 0;i < attr.length;i++) {
                if (!this.shared.data.additional[config.id]) {
                    return false
                }
            }
            return true
        }
    }
}
</script>
