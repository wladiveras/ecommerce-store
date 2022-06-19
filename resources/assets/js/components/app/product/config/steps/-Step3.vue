<style lang="scss">

.card.qty-data {
    background: transparent;
    border: none;
    .card-footer {
        background: transparent;
        .btn.btn-continue {
            background-color: #1798a7;
            color: #fff;
            border-radius: 5px;
        }
    }
}
</style>
<template>
<div class="card qty-data tab_deactive" style="display:none;" ref="content" id="step3">
    <!-- <div class="card-header finished" style="display:none; margin-bottom:0 !important" v-if="finished">
        <i class="mi mi-check"></i>
        <b>{{title}}</b>
    </div>
    <div class="card-header" v-else>
        <b>{{title}}</b>
    </div> -->
    <transition name="fade">
        <div v-show="(shared.step.position==3)">
            <div class="card-body">
                <template v-if="data.has_qty || shared.isPreset">
                    <qty-tshirts v-if="shared.step.product.sizes.length>0" ref="qty" :shared="shared"/>
                    <card-select-qty v-else :shared="shared" :data="data" ref="qty" />
                </template>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-back float-left" @click="back">Voltar</button>
                        <button class="btn btn-continue float-right" @click="confirm" :disabled="!ready" v-bind:class="{'shaking':ready}">Continuar</button>
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
            has_rules:false,
            ready : false
        }
    },
    components: {
        'card-select-qty': require('./partials/qty/-SelectQty.vue').default,
        'qty-tshirts': require('./partials/qty/-QtyTShirts.vue').default
    },
    mounted() {
        console.log('xxxxxxx has_qty', this.data.has_qty);
        if (this.data.product.customizable == '1') {
            this.confirm();
        }
        if (this.data.has_qty) {
            this.shared.step.the_road_so_far.push(3);
            this.$scrollTo("#products-breadcrumb");
            if(this.shared.isPreset){
                this.confirm();
                this.$scrollTo("#products-breadcrumb");
            }else{
                $(this.$refs.content).show();
                this.$scrollTo("#products-breadcrumb");
            }
            if(this.data.clone.options) {
                this.shared.step.loading.close();
                this.$scrollTo("#products-breadcrumb");
            }
        }
    },
    computed: {
        label()
        {
            let qty = 0;
            if(!this.shared.data.skus)
                return qty;
            let skus = this.shared.data.skus;
            for(let i=0; i<skus.length;i++)
            {
                if(this.has_rules)
                    qty+=Number(skus[i].qty);
                else
                    qty+=(Number(skus[i].attribute)*Number(skus[i].qty));
            }
            return qty;
        },
        has_cover()
        {
            if(!this.shared.step)
                return false;
            return this.shared.step.has_cover;
        },
        title() {
            if (this.data.has_qty) {
                return "ESCOLHA A QUANTIDADE";
            }
        },
        finished() {
            return (this.ready && this.confirmed);
        },
    },
    methods: {
        back() {
            this.$refs.qty.reset();
            this.$parent.$parent.back_step();
        },
        confirm() {
            this.confirmed = true;this.shared.step.position++;
        }
    }
}
</script>
