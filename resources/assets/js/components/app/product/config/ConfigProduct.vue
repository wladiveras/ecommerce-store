<template>

    <div :id="data.is_preset ? 'is-preset' :null">
        <div class="row">
            <div class="col-md-12" v-show="data.product.customizable == '0'">
                <h3 id="config-title">Crie seu produto: {{data.product.name}}</h3>
            </div>
            <div class="col-md-12" v-show="data.product.customizable == '1'">
                <h3 id="config-title">Comprando o produto: {{data.product.name}}</h3>
            </div>
        </div>
        <div class="row">
            <div v-if="(shared.step.position==1)" class="col-lg-12">
                <steps-card :shared="shared" :data="data" ref="steps" />
            </div>
            <div v-else class="col-lg-9">
                <steps-card :shared="shared" :data="data" ref="steps" />
            </div>
            <div v-if="shared.step.position!=1" class="col-lg-3">
                <sumary-card :shared="shared" :data="data" ref="sumary" />
            </div>
        </div>

        <modal
            name="modal_confirm"
            class="modal_confirm"
            :clickToClose="false"
            trasition="fade"
            height="auto"
            :adaptive="true"
        >
            <div class="row">
                <div class="col-12">
                    <div class="card w-100 h-100">
                        <div class="card-header text-center">
                            <h4 class="m-0">Conclusão de pedido</h4>
                            <small>Como você deseja prosseguir ?</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="d-flex align-items-center mb-2">
                                        <input
                                            type="radio"
                                            name="f_type"
                                            style="display:none!important;"
                                            value="FINISH_AND_GO_TO_CART"
                                            v-model="shared.step.f_type"
                                        />
                                        <i
                                            class="material-icons icon f-50 checked mr-2"
                                            v-if="shared.step.f_type=='FINISH_AND_GO_TO_CART'"
                                        >check_circle</i>
                                        <i
                                            class="material-icons icon f-50 unchecked mr-2"
                                            v-else
                                        >radio_button_unchecked</i>
                                        <b>Finalizar este pedido e ir para o carrinho</b>
                                    </label>
                                    <label
                                        class="d-flex align-items-center mb-2"
                                        v-if="data.product.customizable == '0'"
                                        @click="shared.step.f_type='FINISH_AND_CREATE_A_CLONE'"
                                    >
                                        <input
                                            type="radio"
                                            name="f_type"
                                            style="display:none!important;"
                                            value="FINISH_AND_CREATE_A_CLONE"
                                            v-model="shared.step.f_type"
                                        />
                                        <i
                                            class="material-icons icon f-50 checked mr-2"
                                            v-if="shared.step.f_type=='FINISH_AND_CREATE_A_CLONE'"
                                        >check_circle</i>
                                        <i
                                            class="material-icons icon f-50 unchecked mr-2"
                                            v-else
                                        >radio_button_unchecked</i>
                                        <b>Finalizar este pedido e criar um novo com as mesmas configurações</b>
                                    </label>
                                    <label
                                        class="d-flex align-items-center mb-2"
                                        @click="shared.step.f_type='FINISH_AND_CONTINUE_BUYING'"
                                    >
                                        <input
                                            type="radio"
                                            name="f_type"
                                            style="display:none!important;"
                                            value="FINISH_AND_CONTINUE_BUYING"
                                            v-model="shared.step.f_type"
                                        />
                                        <i
                                            class="material-icons icon f-50 checked mr-2"
                                            v-if="shared.step.f_type=='FINISH_AND_CONTINUE_BUYING'"
                                        >check_circle</i>
                                        <i
                                            class="material-icons icon f-50 unchecked mr-2"
                                            v-else
                                        >radio_button_unchecked</i>
                                        <b>Finalizar este pedido e continuar comprando</b>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button
                                        v-if="data.product.customizable == '0'"
                                        @click="closeModalConfirm"
                                        class="btn btn-back float-left d-flex align-items-center opacity-1"
                                        style="padding: 10px 30px 10px 30px;"
                                    >
                                        <i class="material-icons mr-2">edit</i> Editar
                                    </button>
                                    <button
                                        v-if="data.product.customizable == '1'"
                                        @click="closeModalConfirmCancel"
                                        class="btn btn-back float-left d-flex align-items-center opacity-1"
                                        style="padding: 10px 30px 10px 30px;"
                                    >
                                        <i class="material-icons mr-2">close</i> Cancelar
                                    </button>
                                    <button
                                        @click="showModalConfirm(true)"
                                        v-if="shared.step.f_type"
                                        class="btn btn-continue float-right d-flex align-items-center shaking opacity-1"
                                        style="heigth:46px;"
                                    >
                                        <i class="material-icons mr-2">check_circle</i> Confirmar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </modal>

    </div>
</template>

<script>
export default {
    props: ['data', 'route_upload', "show_card_attributes", "route_web2print", "w2p_token"],
    components: {
        'steps-card': require('./steps/-Steps.vue').default,
        'sumary-card': require('./sumary/-Sumary.vue').default,
    },
    mounted() {
        console.log('xxxxx', this.data, this.shared);
        if (this.data.is_preset) {
            this.$set(this.shared.data, 'options', this.data.options)
            if (this.data.measures)
                this.$set(this.shared.data, 'measures', this.data.measures)
        }
        if (JSON.parse(localStorage.getItem('shared')) && this.shared.step.position === 9) {
            this.shared = JSON.parse(localStorage.getItem('shared'))
        }
    },
    watch: {
        shared: {
            handler(newValue, oldValue) {
                console.log('yyyyy', this.shared, this.data)
                if (this.shared.step.position === 14 || this.shared.step.position === 15) {
                    this.shared.step.position = 9
                }
                else if (!JSON.parse(localStorage.getItem('user')) && (newValue.step.position === 9)) {
                    localStorage.setItem('route', JSON.stringify(window.location.href))
                    localStorage.setItem('step', JSON.stringify(true))
                    localStorage.setItem('shared', JSON.stringify(this.shared))
                    window.location.href = `${window.location.origin}/login`
                } else if (this.shared.step.position > 9) {
                    localStorage.removeItem('route');
                    localStorage.removeItem('step');
                }

            },
            deep: true
        }
    },
    data() {
        return {

            shared: JSON.parse(localStorage.getItem('shared')) || {
                //measureIndex: this.getMeasureIndex(),
                hasMeasures: this.data.needs_measures,
                isPreset: this.data.is_preset || this.data.product.customizable == '1' || null,
                presetSizes: this.data.sizes || null,
                presetSkuQty: this.data.sku_amount,
                presetQty: Number(this.data.qty),
                step: {
                    selected_options: Object.values(this.data.options || []),
                    route_upload: this.route_upload,
                    web2print_route: this.route_web2print,
                    w2p_token: this.w2p_token,
                    f_type: "FINISH_AND_GO_TO_CART",
                    position: JSON.parse(localStorage.getItem('step')) ? 9 : 1,
                    the_road_so_far: [],
                    has_cover: false,
                    show_send_art_terms: false,
                    has_pricing_role: false,
                    has_finishes: false,
                    finishes: null,
                    covers: null,
                    configs: null,
                    loading: false,
                    has_configs: null,
                    product: this.data.product,
                    show_card_attributes: this.show_card_attributes
                },
                data: {
                    requests: {},
                    chosenFinish: "",
                    ref_name: null,
                    comment: null,
                    art_creation_info: {
                        accepted_termos: false,
                        personal_info: {},
                        company_info: {}
                    },
                    department: this.data.product.department,
                    product_id: this.data.product.id,
                    additional: {},
                    chosen: (this.data.clone.chosen ? this.data.clone.chosen : this.data.art_type ? this.data.art_type : null),
                    skus: null,
                    sku_ids: null,
                    sizes: null,
                    sku: null,
                    amount: null,
                    finishes: {},
                    extra: {
                        cover: null
                    },
                    upload: {
                        file: null,
                        timing: "now"
                    },
                    options: {},
                    measures: {
                        height: null,
                        width: null,
                        unit: null,
                    }
                },
            }
        }
    },
    methods: {
        getMeasureIndex() {
            if (!this.isPreset && !this.data.needs_measures) return
            let field = this.data.options["Formato"]
            if (field && field.includes("M²")) {

            }
        },
        back_step(step = null) {
            if (!step) {
                this.shared.step.the_road_so_far.splice(this.shared.step.the_road_so_far.length - 1, this.shared.step.the_road_so_far.length)
                this.shared.step.position = this.shared.step.the_road_so_far[this.shared.step.the_road_so_far.length - 1]
            } else {
                this.shared.step.the_road_so_far.splice(step - 1, this.shared.step.the_road_so_far.length)
                this.shared.step.position = step
            }
            return this.$scrollTo(`#step${this.shared.step.position}`)
        },
        showModalConfirm(confirmed = false) {
            if (!confirmed) {
                return this.$modal.show('modal_confirm')
            } else {
                this.shared = JSON.parse(localStorage.getItem('shared'))
                this.$modal.hide('modal_confirm')
                this.$refs.sumary.submit(false)
            }
        },
        closeModalConfirmCancel(){
            this.$modal.hide('modal_confirm');
            localStorage.removeItem('shared')
            window.location = (window.location.href).split("configuracao_produto")[0];
        },
        closeModalConfirm() {
            this.$modal.hide('modal_confirm')
            this.$refs.sumary.is_showing_modal = false
            this.$refs.sumary.edit()
            this.shared.step.f_type = "FINISH_AND_GO_TO_CART"
        }
    }
}
</script>

<style lang="scss">
.btn-back {
    border: none;
    border-radius: 5px;
    color: #000;
    background-color: #cccccc;
    padding: 10px 20px 10px 20px;
    font-size: 14px;
    font-weight: 500;
}

.btn-back:enabled:hover {
    color: #000;
    transition: 0.5s;
    background-color: #9c9c9c;
}

.btn-back[disabled] {
    opacity: 0.5;
}

.btn-continue {
    background-color: #1798a7;
    color: #fff;
    border-radius: 5px;
    border: none;
    padding: 10px 20px 10px 20px;
    font-size: 14px;
}

.btn-continue:enabled:hover {
    background-color: #1b4e01;
    color: white;
    transition: 0.5s;
}

.btn-continue[disabled] {
    opacity: 0.5;
}

.radio_big {
    transform: scale(2);
}
.icon {
    &.f-50 {
        font-size: 50px !important;
    }
}
#is-preset {
    #step2,
    #step3,
    #step4,
    #step5,
    #step6 {
        .btn-back {
            display: none;
        }
    }
}
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
</style>