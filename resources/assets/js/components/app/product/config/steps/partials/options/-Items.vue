<template>
<div  :ref="'card_option_'+variation"  style="display:none;" :class="{'d-none':this.shared.isPreset && this.shared.hasMeasures && !this.shared.data.options[variation].includes('M²')}">
    <div class="row">
        <div class="col-12">
            <h5 class="mt-4 px-3">{{variation}}</h5>
        </div>
    </div>
    <div class="d-flex flex-wrap mb-2 h-100 p-3" style="background: #cccccca6;">
        <template v-for="(attr,index) in attribute.attributes" >
            <template v-if="attr.includes('M²')">
                <card-item-metters class="set-measures col-12" :shared="shared" v-if="index==0" :variation="variation" :attribute="attribute" :options="attribute.attributes" :step="step" />
            </template>
            <div class="variation-option mb-2 text-center" v-else>
                <div class="card mr-3 card-option card-item-option h-100" v-bind:class="{'selected' : shared.data.options[variation]==attr}">
                    <button v-if="details[index].image || details[index].description" @click.prevent="detail_attr(attr,details[index])" type="button" class="btn-edit px-2  d-none d-lg-block">+ Ver Mais</button>
                    <label>
                        <div class="card-body attr-item">
                            <img @click="addItem" v-if="details[index].image" :src="`${details[index].image}?width=133&height=133&gravity=center`" />
                            <div v-else class="fake-img"/>
                        </div>
                        <div class="m-0 p-1">
                            <b class="m-0">
                                <div class="custom_radio d-flex align-items-center" >
                                    <input type="radio" style="display:none;" :ref="'option_'+attr" :name="variation" 
                                    :value="attr" @change="$parent.select_option(attr,step,variation)" v-model="shared.data.options[variation]"> 
                                    <i class="material-icons icon checked"   v-if="shared.data.options[variation]==attr">check_circle</i>
                                    <i class="material-icons icon unchecked" v-else>radio_button_unchecked</i>
                                    <span class="ml-2">{{(attr  ?  attr : "Não Optar")}}</span>
                                </div>
                            </b>
                        </div>
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
        return {
            details : this.attribute.details
        }
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
        
    },
    methods : {
        detail_attr(attribute,detail) {
            this.$parent.show_modal_info(attribute,detail)
        },
        addItem: function() {
            return this.$scrollTo("#card-options-anchor");
            console.log("teste")
        }
        
    }
}
</script>
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

.card {
    &.card-option {
        width: 145px!important;
        min-height: 180px!important;
        .attr-item {
            cursor : pointer;
            padding :5px 5px 0px 5px;
            img {
                width : 100%;
            }
            .fake-img {
                width : 100%;
                min-width : 100%;
                height : 100%;
                min-height : 100%;
                background-color: #d4d4d4;
            }
            position : relative;
        }
        .card-body {
            height : 140px;
            min-height :140px;
            max-height :140px;
        }
        .card-footer { 
            padding: 5px;
            background-color: white!important;
            border-top: unset;
        }
        .btn-edit {
            border: unset;
            font-size: 11px;
            font-weight: 600;
            background-color: #1B4E01;
            color: white;
            border-radius: 10px;
            position: absolute;
            top: 16px;
            right: 4px;
            z-index: 900;
        }
        &:hover {
			// transition: 0.5s;
			// -webkit-transform: scale(1.08);
			// transform: scale(1.08);
			border: 1px solid #1B4E01;
		}
    }
}
</style>