<style lang="scss" scoped>
.card-select-config {
    .card-footer {
        background: transparent;
        border: none;
    }
}
.card_body_custom {
    height: 117px;
    padding-top: 38px;
}

.footer_custom {
    height: 84px;
    padding-top: 40px;
}
.custom_position_radio {
    position: absolute;
    left: 43%;
    top: 81px;
}
.card-custom .radio_custom .icon.unchecked, .card-custom .radio_custom .icon.checked{
    display: none;
}
.card-custom {
    .card.card-option {
        border: none;
    }
    .card.card-option.selected {
        background: linear-gradient(20deg, rgb(255, 202, 33) 0%, rgb(236, 65, 0) 100%);
    }
    .card-body {
        padding: 5px;
    }
    .card.card-option .card-footer {
        background: #f6fafd;
    }
}
.config-cards {
    .card {
        background: transparent;
    }
}
</style>
<template>
    <div class="card card-select-config" style="display:none" ref="content">
        <div class="finished" v-if="finished" style="border-radius: 10px;border: 1px solid #ccc !important;margin-bottom: 20px !important;background: #eceff1;">
            <!-- <i class="mi mi-check"></i> -->
            <!-- <b>ESCOLHA A FORMA DE ENVIAR O PRODUTO</b> -->
            <h5 class="f-15">Forma de criar seu produto:</h5>
            <div class="d-flex align-items-center">
                <img class="col-md-2" v-if="label=='Enviar minha arte final'" src="/assets/images/ilust-config-envio-arte.jpg" alt="" style="max-width: 100%;">
                <h5 class="col-md">{{label}}</h5>
                <span class="col-md-4" v-if="label=='Enviar minha arte final'">Faça deve fazer upload de seu arquivo personalizado.</span>
            </div>
        </div>
        <div class="card-header" style="background:transparent; padding:0" v-else>
            <!-- <b style="color:#d32821">ESCOLHA A FORMA DE ENVIAR O PRODUTO</b> -->
        </div>
        <transition name="fade">
            <div v-if="(shared.step.position==1)">
                <div class="card-body card-custom" id="step1">
                    <div class="row select_send_type justify-content-center">
                        <div :class="cols" v-for="(config,i) in data.configs" :key="i">
                            <label class="radio_custom">
                                <div
                                    :class="`card card-option ${config.type} ${shared.data.chosen==config.type ? 'selected' : ''}`"
                                >
                                    <div class="m-0 card-body">
                                        <h5
                                            class="m-0 d-flex align-items-center justify-content-center text-left"
                                        >
                                            <input
                                                type="radio"
                                                :value="config.type"
                                                v-model="shared.data.chosen"
                                            />
                                            <i
                                                class="material-icons icon big checked mr-2"
                                                v-if="shared.data.chosen==config.type"
                                            >check_circle</i>
                                            <i
                                                class="material-icons icon big unchecked mr-2"
                                                v-else
                                            >radio_button_unchecked</i>
                                            <template
                                                v-if="config.type=='UP'"
                                            >
                                                <img src="/assets/images/ilust-config-envio-arte.jpg" alt="" style="max-width: 100%;">
                                            </template>
                                            <template
                                                v-if="config.type=='W2P'"
                                            >Criar minha arte final</template>
                                            <template
                                                v-if="config.type=='CA'"
                                            >
                                                <img src="/assets/images/ilust-config-designer.jpg" alt="" style="max-width: 100%;">
                                            </template>
                                        </h5>
                                    </div>
                                    <div
                                        class="card-footer d-flex align-items-center justify-content-center"
                                        style="height: 100px;"
                                    >
                                        <small>
                                            <template
                                                v-if="config.type=='UP'"
                                            ><h5>Enviar arte final</h5>Envie o seu arquivo finalizado para impressão conforme sua configuração selecionada e nossas regras de fechamento de arquivo.</template>
                                            <template
                                                v-if="config.type=='W2P'"
                                            >Cria sua Arte e envie para a impressão.</template>
                                            <template
                                                v-if="config.type=='CA'"
                                            ><h5>Contratar Criação de Arte</h5> suas informações e imagens nossos designers vão criar uma arte personalizada para você. Custo adicional de {{(getCaPrice).currency()}}</template>
                                        </small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button
                                class="btn-continue"
                                :disabled="(this.shared.data.chosen==null)"
                                v-bind:class="{'shaking':!(this.shared.data.chosen==null)}"
                                @click="confirm"
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
            actual_step: 1,
            confirmed: false,
        }
    },
    computed: {
        label() {
            switch (this.shared.data.chosen) {
                case "UP":
                    return "Enviar minha arte final"
                    break
                case "W2P":
                    return "Criar minha arte final"
                    break
                case "CA":
                    return "Contratar Criação de Arte"
                    break
            }
        },
        finished() {
            return ((this.ready) && (this.confirmed))
        },
        ready() {
            return this.shared.data.chosen != null
        },
        getCaPrice() {
            let config = this.data.configs.find(x => x.type == "CA")
            let price = !config.price ? (config.attr[0].price ? config.attr[0].price : 29.90) : config.price
            if (this.data.product.custom_extra_info) {
                if (this.data.product.custom_extra_info.additional_custom_price) {
                    if (this.data.product.custom_extra_info.additional_custom_price.CA) price = this.data.product.custom_extra_info.additional_custom_price.CA
                }
            }
            config.attr[0].price = price
            return price
        },
        cols() {
            if (this.data.configs.length > 2)
                return "col-md-4 text-center"
            else
                return "col-md-4 text-center"
        }

    },
    mounted() {
        console.log('vvvvv', this.data);
        console.log('vvvvv', this.shared);
        if (this.data.product.customizable == '1') {
            return this.confirm()
        }
        this.shared.step.the_road_so_far.push(1)
        if (this.shared.data.chosen != null) {
            return this.confirm()
        }
        if (this.isMostruario()) {
            this.shared.data.chosen == "UP"
            return this.confirm()
        }
        if (this.withoutArt()) {
            this.shared.data.chosen == "UP"
            return this.confirm()
        }
        $(this.$refs.content).show()
    },
    methods: {
        isMostruario: function isMostruario() {
            var name = this.data.product.name
            return name.toUpperCase().indexOf("MOSTRU") >= 0
        },
        withoutArt: function withoutArt() {
            var name = this.data.product.name
            return name.toUpperCase().indexOf("SEM PERSONALIZA") >= 0
        },
        confirm() {
            this.confirmed = true
            this.shared.step.position++
        }

    }
}
</script>
<style lang="scss" scoped>
@media (max-width: 768px) {
    .card {
        &.card-option {
            &.W2P {
                //display: none;
            }
        }
    }
}
</style>
