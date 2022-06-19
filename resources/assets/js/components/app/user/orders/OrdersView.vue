<style scoped>
.header-compras
{
  font-size: 20px; padding: 20px; background-color: rgb(242, 242, 242);
}

.card-header-compras
{
  padding-top: 0px;
  padding-left: 9px;
  padding-right: 9px;
}

.card-compras-title
{
    font-weight:600;
}
.tab-content-custom
{
    padding:24px;
}
.card-footer
{
    background-color:white;
}
.pagination
{
    border: 1px solid #1B4E01;
}
.page-link{
    color: #1B4E01!important;
}
</style>
<template>
<div v-loading="shared.loading" ref="content">
    <span class="display-4 title d-flex align-items-center mb-0 header-compras">
        <i class="material-icons mr-2">store_mall_directory</i>
        <span class="card-compras-title">Minhas Compras</span>
    </span> 
    <div id="__BVID__1" class="custom tabs">
       <!---->
       <div class="card-header card-header-compras">
            <component-header :shared="shared"></component-header>
       </div>
       <div id="__BVID__1__BV_tab_container_" class="tab-content">
            <div role="tabpanel" class="tab-content-custom tab-pane card-body show fade active">
                
                <template v-if="(shared.orders.length==0)">
                    <div class="row">
                        <div class="col-md-12 text-center pt-4 mb-3">
                            <h4>Você não possui compras com este status</h4>
                        </div>
                    </div>
                </template>  
                <template v-else v-for="order in shared.orders">
                    <component-orderBox :id="order.id" :color="colorBox(order)" :shared="shared" :order="order"></component-orderBox>
                </template> 
                
            </div>
            <footer class="card-footer" >

                <div class="row">
                    <div class="col-md-12 text-right d-flex justify-content-end" style="top: 10px;">
                        <slot name="pagination"></slot>
                    </div>
                </div>

            </footer>
          <!---->
       </div>
    </div>
</div>
</template>

<script>
export default {
    props: ["status","orders","count","default_route","results_perpage","filter","route_detail","status_list"],
    data() {
        return {
            shared :
            {
                status_list : this.status_list,
                route_detail : this.route_detail,
                filter : this.filter,
                results_perpage : this.results_perpage,
                default_route : this.default_route,
                loading : false,
                count : this.count,
                orders : this.orders.data,
                status : this.status,
                boxOpened : null
            }
        }
    },
    mounted() {
        this.group();
    },
    components: {
        // 'component-header': require('./-Header.vue').default ,     
        'component-orderBox': require('./-OrderBox.vue').default ,     
    },
    methods: {
        group() {
             let orders = this.shared.orders;
            for(let i in orders) {
                let skus = orders[i].skus;
                let aux  = _.groupBy(skus,(x) => x.data.cart_id  );
                this.shared.orders[i].skus = aux;
            }
        },
        colorBox(order)
        {
            let status_id = order.status_id;
            
            let array = this.shared.status_list;
            if(array==undefined)
            {
                return null;
            }
            let status = array.find(x=>x.id==status_id);
            if(['pending'].includes(status.value))
            {
                return "orange";
            }
            if(['forwarded'].includes(status.value))
            {
                return "green";
            }
            if(['delivered'].includes(status.value))
            {
                return "purple";
            }
            if(['canceled'].includes(status.value))
            {
                return "red";
            }
            if(['approved'].includes(status.value))
            {
                return "green";
            }
            if(['production'].includes(status.value))
            {
                return "blue";
            }
        }
    }
}
</script>