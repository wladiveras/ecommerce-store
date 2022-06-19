<style scoped>
.overlay {
  height:100%;
  width:100%;
  background-color:#eaeaea;
  position:absolute;
  opacity: .7;
  z-index: 9999;
}
</style>
<template>
    <div>
        <a href="#" id="reset-options" @click.prevent="reset" class="float-right d-flex align-items-center">
            <i class="material-icons mr-2" >refresh</i> Redefinir opções
        </a>
        <div class="row mb-2">
            <div class="col-md-12">
                <h5>Capa</h5>
            </div>
        </div>
        <div class="row mb-2">
            <template v-for="(cover,index) in covers">
            <div class="col-md-4 mb-2 text-center" :key="index"> 
                <div class="card">
                    <template  v-if="(shared.data.extra.cover)">
                        <div class="overlay" v-if="(shared.data.extra.cover!=cover.gramatura)"></div>
                    </template>
                    <label class="m-0 card-body">
                        <b class="m-0">
                            <input :key="index" type="radio" class="radio_big mr-3" :name="cover.gramatura" :value="cover.gramatura" v-model="shared.data.extra.cover"> 
                            Gramatura {{cover.gramatura}}
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
    props: ['shared'],
    data() {
        return {
            covers:null
        }
    },
    mounted() {
       this.load_covers(); 
    },
    methods: {
        reset()
        {
            this.shared.data.extra.cover = null;
        },
        load_covers()
        {
            if(!this.shared.data.skus)return
            this.shared.step.covers = this.shared.data.skus[0].sku.data ? this.shared.data.skus[0].sku.data.cover : null;
            this.covers = this.shared.step.covers;
        }
    }
}
</script>
