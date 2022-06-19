<style scoped>
.card-custom {
    opacity: 0.5;
}

.custom_radio input[type="radio"]:after {
    display: none;
}
</style>
<template>
    <div class="row mb-2">
        <!-- <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="subsection-title">{{config.name}}</h5>
                </div>
            </div>
        </div> -->
        <div class="col-12">
            
            <template v-for="(attr,index) in config.attr">
                <div :key="index">
                    <label class="w-100">
                        <div
                            class="select-pro"
                            v-bind:class="{ 'selected' : shared.data.additional[config.id] == attr.id  }"                            
                        >
                            <div>
                                <div class="custom_radio d-flex align-items-center select-pro-content">
                                    <input
                                        type="radio"
                                        style="display:none;"
                                        :name="config.id"
                                        :value="attr.id"
                                        v-model="value"
                                    />
                                    <i
                                        class="material-icons icon checked"
                                        v-if="shared.data.additional[config.id] == attr.id "
                                    >check_circle</i>
                                    <i
                                        class="material-icons icon unchecked"
                                        v-else
                                    >radio_button_unchecked</i>
                                    <div class="m-0 d-flex align-items-center">
                                        <span>
                                            <div class="d-flex align-items-center" v-if="attr.name == 'Tratamento Profissional'">
                                                <img src="/assets/images/icon-get-pro.jpg" alt="" style="max-width: 100%;margin-right: 9px;">
                                                <p style="width: 130px;">Sim, quero ajuda profissional</p>
                                            </div>
                                            <p v-else>Obrigado, NÂO quero ajuda profissional </p>
                                            <!-- <b>{{attr.name}}</b> -->
                                        </span>
                                        <div v-if="attr.price<=0">
                                                                                        
                                        </div>
                                        <div v-else>
                                           ( {{attr.price.currency()}} )
                                        </div>
                                    </div>
                                </div>
                                <!-- <div>
                                    <p class="m-0">
                                        <small>{{attr.description}}</small>
                                    </p>
                                </div> -->
                            </div>
                            <!-- <div class="card-footer">
                                {{attr.price.currency()}}
                                <span v-if="attr.price<=0">(Grátis)</span>
                            </div> -->
                        </div>
                    </label>
                </div>
            </template>
            
        </div>
    </div>
</template>

<script>
export default {
    props: ['shared', "config"],
    data() {
        return {
            value: null,
            backup: null
        }
    },
    mounted() {
        let attr = this.config.attr
        for (let row in attr) {
            if (attr[row]._default) {
                this.value = attr[row].id
                break
            }
        }
    },
    watch: {
        value(val) {
            let attr = this.config.attr.find(x => x.id == val)
            this.shared.step.show_send_art_terms = attr.show_confirmation_terms
            this.$set(this.shared.data.additional, this.config.id, val)
        }
    },
}
</script>
