<style scoped>

</style>
<template>
<div>
    <div class="row">
        <card-finish-item :shared="shared" v-for="(f,i) in grouped_finishes" :item="f" :data="data" :key="i" />
    </div>
    <template v-if="laminacoes.length>0">
        <laminacao-items :items="laminacoes" :product="data.product" :shared="shared" />
    </template>
</div>
    
</template>

<script>
export default {
    props: ['shared', 'data'],
    data() {
        return {
            finishes: Object.assign({},this.shared.step.finishes),
            laminacoes : []
        }
    },
    components: {
        'card-finish-item': require('./-Item.vue').default,
        'laminacao-items': require('./-Laminacao.vue').default,
    },
    computed : {
        grouped_finishes() {
            let finishes = this.finishes
            let _fin = []
            let laminacao = []
            for(let f in finishes)
            {
                finishes[f].name = finishes[f].name.replace(/\n/ig, ' ')
                if(finishes[f].name.indexOf("Laminação")>=0) this.laminacoes.push(finishes[f])
                else _fin.push(finishes[f])
            }
            return _fin 
        }
    },
}
</script>



