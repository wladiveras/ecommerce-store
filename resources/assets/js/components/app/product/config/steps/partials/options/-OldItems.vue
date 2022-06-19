<style lang="scss" scoped>
.overlay {
    height: 100%;
    width: 100%;
    background-color: #eaeaea;
    position: absolute;
    opacity: .7;
    z-index: 9999;
}

.custom_radio input[type=radio]:after {
    display:none;
    .icon {
        font-size : 30px;
        color:#1B4E01!important;
        color: #1B4E01;
        &.unchecked {
            color: #F8F9FA;
        }
        &.checked {
            color: #1B4E01;
        }
    }
}
</style>
<template>
<div  :ref="'card_option_'+variation"  style="display:none;">
    <div class="row">
        <div class="col-12" v-if="variation == 'Formato'">
            <h5 class="mb-3">
                {{variation}}
            </h5>
        </div>
        <div class="col-12" v-else>
            <h5>{{variation}}</h5>
        </div>
    </div>
    <div class="row mb-2">
        <template v-for="(attr,index) in attribute.attributes" >
            <template v-if="attr.indexOf('M²')>=0">
                <card-item-metters class="col-12" :shared="shared" v-if="index==0" :variation="variation" :attribute="attribute" :options="attribute.attributes" :step="step" />
            </template>
            <div class="col-md-4 mb-2 text-center" v-else>
                <div class="card card-option" v-bind:class="{'selected' : shared.data.options[variation]==attr}">
                    <label class="m-0 card-body">
                        <b class="m-0">
                            <div class="custom_radio d-flex align-items-center" >
                                <input type="radio" style="display:none;" :ref="'option_'+attr" :name="variation" 
                                :value="attr" @change="$parent.select_option(attr,step,variation)" v-model="shared.data.options[variation]"> 
                                <i class="material-icons icon checked"   v-if="shared.data.options[variation]==attr">check_circle</i>
                                <i class="material-icons icon unchecked" v-else>radio_button_unchecked</i>
                                <span class="ml-2">{{(attr  ?  attr : "Não Optar")}}</span>
                            </div>
                        </b>
                    </label>
                </div>
            </div>
        </template>
    </div>
</div>
</template>

<script>
export default {
    props: ['shared', 'attribute', 'variation', 'step','data'],
    data() {
        return {}
    },
    components: {
        'card-item-metters': require('./-RowMetters.vue').default
    },
    mounted() {
        let attr = this.attribute.attributes
        let attribute = attr[0]
        if(this.data.clone.options) {
            $(this.$refs['option_'+this.data.clone.options[this.variation]]).click()
            if(this.$parent.$parent.ready) {
                return this.shared.step.loading.close()
            }
        } else {
            if(attr.length==1) {
                if((attribute!='M²')&&(this.variation!="Quantidade")) {
                    $(this.$refs['option_'+attribute]).click()
                }
                if((attribute!="")&&(this.variation!="Quantidade")) {
                    return $(this.$refs['card_option_'+this.variation]).show()
                }
                return false
            }
        }
        $(this.$refs['card_option_'+this.variation]).show()
    }
}
</script>
