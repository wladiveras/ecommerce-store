<template>
    <div class="card tab_deactive" ref="content" style="display:none;" id="step5">

    </div>
</template>

<script>
//ESTE STEP FOI REMOVIDO DA VISÃO
export default {
    props: ['shared', 'data'],
    data() {
        return {
            confirmed: true,
        }
    },
    components: {
        'card-check-additional': require('./partials/additional/-Additional.vue').default,
    },
    computed: {
        config() {
            let chosen = this.shared.data.chosen
            let res = this.data.configs.find(x => x.type == chosen)
            return res
        },
        finished() {
            return (this.ready && this.confirmed)
        },
        ready() {
            if (!this.shared.step.has_configs)
                return false
            let config = this.shared.step.configs
            let attr = config.attr
            for (let i = 0;i < attr.length;i++) {
                if (!this.shared.data.additional[config.id]) {
                    return false
                }
            }
            return true
        }
    },
    mounted() {
        this.confirm()
        /*if (!this.shared.step.has_configs || this.config.attr.length <= 0) {
            this.confirm()
        } else {
            $(this.$refs.content).show()
            this.shared.step.the_road_so_far.push(5)
            return this.$scrollTo("#products-breadcrumb")
        }*/
    },
    methods: {
        back() {
            this.shared.data.additional = []
            this.$parent.$parent.back_step()
        },
        confirm() {
            this.confirmed = true
            this.shared.step.position++
        },
    }
}
</script>
