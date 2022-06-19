<style scoped>
.card-header-compras
{
  padding-top: 0px;
  padding-left: 9px;
  padding-right: 9px;
}


</style>
<template>
<div>
    <ul role="tablist" tabindex="0" id="__BVID__1__BV_tab_controls_"    class="nav nav-tabs card-header-tabs">
        <component-filter class="col-md-12 p-0" :shared="shared"></component-filter>    

        <component-tab class="col-md-2 p-0" title="Compras Pendentes"  :shared="shared" url="pendentes" :count="qtdePending" color="orange" :active="(this.shared.status[1]=='pending')"></component-tab>
        <component-tab class="col-md-2 p-0" title="Compras Confirmadas":shared="shared" url="confirmadas" :count="qtdeForwarded" color="green" :active="(this.shared.status[1]=='confirmed')"></component-tab>
        <component-tab class="col-md-2 p-0" title="Compras Concluidas" :shared="shared" url="concluidas" :count="qtdeFinished" color="purple" :active="(this.shared.status[1]=='finished')"></component-tab>
        <component-tab class="col-md-2 p-0" title="Compras Canceladas" :shared="shared" url="canceladas" :count="qtdeCanceled" color="red" :active="(this.shared.status[1]=='canceled')"></component-tab>    
        <component-tab class="col-md-2 p-0" title="Compras em Produção" :shared="shared" url="producao" :count="qtdeProduction" color="blue" :active="(this.shared.status[1]=='production')"></component-tab>    
        <component-tab class="col-md-2 p-0" title="Todas as Compras"   :shared="shared" url="todas" :count="qtdeAll" :active="(this.shared.status[1]=='all')"></component-tab>    
    </ul>
</div>
</template>

<script>
export default {
    props: ["shared"],
    data() {
        return {}
    },
    computed:
    {
        qtdeAll()
        {
            return this.shared.count.all;
        },
        qtdeCanceled()
        {
            return this.shared.count.canceled;
        },
        qtdeFinished()
        {
            return this.shared.count.finished;
        },
        qtdePending()
        {
            return this.shared.count.pending;
        },
        qtdeForwarded()
        {
            return this.shared.count.forwarded;
        },
        qtdeProduction() {
            return this.shared.count.production;
        }
    },
    components: {
        'component-tab': require('./-Tabs.vue').default, 
        'component-filter': require('./-Filter.vue').default, 
    },
    mounted() {
    //    console.log(this.shared.status);
    }
}
</script>
