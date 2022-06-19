<template>
<div class="card tab_deactive" style="display:none;" ref="content"  id="step4" >
    <div class="card-header finished" v-if="finished">
        <i class="mi mi-check"></i>
        <b>SELECIONE A CAPA</b>
    </div>
    <div class="card-header" v-else>
        <b>SELECIONE A CAPA</b>
    </div>
    <transition name="fade">
        <div v-show="(shared.step.position==4)">
            <div class="card-body">
                <card-select-covers :shared="shared" :data="data" ref="covers"/>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-back float-left" @click="back">Voltar</button>
                        <button class="btn-continue float-right" @click="confirm" :disabled="!ready" v-bind:class="{'shaking':ready}">Continuar</button>
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
        }
    },
    components: {
        'card-select-covers': require('./partials/cover/-Covers.vue').default,
    },
    mounted()
    {
        if (!this.shared.step.has_cover)
            this.confirm();
        else
        {
            $(this.$refs.content).show();
            this.shared.step.the_road_so_far.push(4);
            return this.$scrollTo("#products-breadcrumb");
        }
    },
    computed: {
        finished() {
            return (this.ready && this.confirmed);
        },
        ready() {
            return this.shared.data.extra.cover;
        }
    },
    methods: {
        back() {
            this.shared.step.position--;
            this.shared.data.extra.cover=null;
            this.$parent.$parent.back_step();
        },
        confirm()
        {
            this.confirmed = true;
            this.shared.step.position++;
        },
    }
}
</script>
