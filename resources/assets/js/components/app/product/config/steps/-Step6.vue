<template>
<div class="card tab_deactive" style="display:none;" ref="content"  id="step6">
    <div class="card-header finished" style="display:none; margin-bottom:0 !important" v-if="finished">
        <i class="mi mi-check"></i>
        <b>ESCOLHA OS ACABAMENTOS</b>
    </div>
    <div class="card-header" v-else>
        <b>ESCOLHA OS ACABAMENTOS</b>
    </div>
    <transition name="fade">
        <div v-show="(shared.step.position==6)">
            <div class="card-body">
                <template v-if="has_finishes">
                    <card-finishes-list :shared="shared" :data="data" />
                </template>
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
            ready : true
        }
    },
    components: {
        'card-finishes-list': require('./partials/finishes/-FinishesList.vue').default,
    },
    mounted() {
        if (this.has_finishes)  {
            $(this.$refs.content).show();
            this.shared.step.the_road_so_far.push(6);    
            return this.$scrollTo("#step6");        
        }
        else
            this.confirm();
    },
    computed: {
        finished() {
            return (this.ready && this.confirmed);
        },
        has_finishes() {
            return this.shared.step.has_finishes;
        }
    },
    methods: {
        back() {
            this.shared.data.requests = {};
            this.shared.data.finishes = {};
            this.$parent.$parent.back_step();
        },
        confirm() {
            this.confirmed = true;
            this.shared.step.position++;
        }
    }
}
</script>
