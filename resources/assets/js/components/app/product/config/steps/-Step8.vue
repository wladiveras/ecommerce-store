<template>
<div class="card tab_deactive" ref="content" style="display:none;"  id="step8">
    <div class="card-header finished" v-if="finished">
        <i class="mi mi-check"></i>
        <b>{{title}}</b>
    </div>
    <div class="card-header" v-else>
        <b>{{title}}</b>
    </div>
    <transition name="fade">
        <div v-show="(shared.step.position==8)">
            <div class="card-body">
                <component-step-art-creation :shared="shared" ref="steps"/>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-back float-left" @click="back">Voltar</button>
                        <button class="btn-continue shaking float-right" @click="confirm">{{btn_text}}</button>
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
            btn_text : null,
            title : "TERMOS DE CRIAÇÃO DE ARTE"
        }
    },
    components: {
        'component-step-art-creation': require('./partials/artCreation/-StepArtCreation.vue').default,
    },
    mounted() {
        this.$parent.$parent.btn_text = "Continuar";
        if (this.shared.data.chosen === 'CA') {
            this.shared.step.the_road_so_far.push(8);
            $(this.$refs.content).show();
            return this.$scrollTo("#products-breadcrumb");
        } else
            return this.next_step();
    },
    computed: {
        actual_step()
        {
            if(!this.$refs.steps)
                return 1;
            return this.$refs.steps.position;
        },
        finished() {
            return this.confirmed;
        }
    },
    methods: {
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
            if(this.actual_step<=3)
                return this.$refs.steps.next();
                alert(this.$refs.steps);
            this.next_step();
            window.scrollTo(0,100);
        },
    }
}
</script>
