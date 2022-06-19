<template>
    <div class="card tab_deactive" ref="content" style="display:none;" id="step7">
        <div class="card-header finished" style="display:none; margin-bottom:0 !important" v-if="finished">
            <i class="mi mi-check"></i>
            <b>{{title}}</b>
            <small class="ml-3">{{label}}</small>
        </div>
        <div class="card-header" v-else>
            <b>{{title}}</b>
        </div>
        <transition name="fade">
            <div v-show="(shared.step.position===7)">
                <div class="card-body" v-loading="!showComponent">
                    <web-2-print v-if="shared.data.chosen == 'W2P'" :shared="shared" />
                    <template v-else>
                        <content-send-art
                            ref="sendart"
                            v-if="showComponent"
                            :shared="shared"
                            :componentInfo="componentInfo"
                        ></content-send-art>
                        <div class="row" v-if="ready && shared.step.show_send_art_terms">
                            <div class="col-12">
                                <label class="float-right">
                                    <input type="checkbox" v-model="accepted" class="mr-2" />
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
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn-back float-left" @click="back">Voltar</button>
                            <button
                                class="btn-continue float-right"
                                @click="confirm"
                                :disabled="!readyToContinue"
                                v-bind:class="{'shaking':readyToContinue}"
                            >Continuar</button>
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
            showComponent: false,
            confirmed: false,
            ready: false,
            accepted: false,
            cutAndCrease: "corte__e__vinco",
            localizedVarnish: "Encartes e Folhetos c/ Verniz Localizado",
            localizedVarnish2: "Laminação Fosca e UV Localizado 1/1",
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
        "web-2-print": require("./partials/upload/-Web2Print.vue").default
    },
    mounted() {
        if (["W2P"].includes(this.shared.data.chosen) && !this.isMostruario()) {
            this.shared.step.the_road_so_far.push(7)
            this.load()
            //this.$scrollTo("#products-breadcrumb")
            if (this.shared.data.chosen == "W2P") {
                if (this.shared.data.upload.file) this.ready = true
            }
            return $(this.$refs.content).show()
        } else
            return this.confirm()
    },
    computed: {
        readyToContinue() {
            if (this.shared.data.chosen == "W2P") {
                return this.ready
            }
        },
        title() {
            return "ENVIO DA ARTE"
        },
        label() {
            if (this.ready) {
                return "Arte enviada"
            }
            return null
        },
        finished() {
            return (this.ready && this.confirmed)
        },
    },
    methods: {
        isMostruario() {
            let name = this.data.product.name
            return name.toUpperCase().indexOf("MOSTRU") >= 0
        },
        load() {
            this.getSegment()
            this.getColor()
            this.getHasCutAndCrease()
            this.componentInfo.hasLocalizedVarnish = this.getHasLocalizedVarnish()
            this.showComponent = true
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
        getColor() {
            this.componentInfo.color = this.shared.data.options.Cor
        },
        getSegment() {
            this.componentInfo.segment = this.data.product.department
        },
        back() {
            if (this.shared.data.chosen != 'W2P') this.shared.data.upload.file = null
            this.shared.data.upload.timing = "now"
            this.$parent.$parent.back_step()
        },
        confirm() {
            this.shared.step.position++
            if (!this.isMostruario()) {
                if (this.shared.data.chosen == 'UP') {
                    if(this.$refs.sendart){
                        this.shared.data.upload.file = this.$refs.sendart.files
                    }
                } else if (this.shared.data.chosen != 'W2P') {
                    this.shared.data.upload.file = null
                }
            }
            this.confirmed = true
            window.scrollTo(0,100)
        },
    }
}
</script>
