<template>
    <div class="col-12 px-0 mb-3">
        <div class="card detail-card h-100">
            <div class="card-header">
                <h4 class="title d-flex align-items-center"><i class="material-icons icon">store_mall_directory</i>Compra</h4>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center pb-lg-3">
                    <div class="col-lg-4 col-12 pl-0">
                        <table class="w-100">
                            <tr>
                                <td><b>Código</b></td>
                                <td>#{{ order.code }}</td>
                            </tr>
                            <tr>
                                <td><b>Data da Compra</b></td>
                                <td>{{ order.format_date }}</td>
                            </tr>
                            <tr>
                                <td><b>Hora da Compra</b></td>
                                <td>{{ order.format_time }}</td>
                            </tr>
                            <tr>
                                <td><b>Previsão de Entrega</b></td>
                                <td>{{ estimated_shipping }}</td>
                            </tr>
                            <tr>
                                <td><b>Qtde de pedidos</b></td>
                                <td>{{ order.skus.length }} Pedido<template v-if="order.skus.length>1">s</template></td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td>{{ order.data.totalPrice.currency() }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-8 col-12 d-none d-md-block pl-0 py-3 py-lg-0">
                        <timeline-card :steps="steps" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["order","statusList","steps"],
    components : {
        "timeline-card": require("./-TimelineCard.vue").default
    },
    mounted() {
        // console.log(this.shipping)
    },
    computed : {
        estimated_shipping() {
            let time = this.order.data.estimated_time ? this.order.data.estimated_time : 0
            if((this.order.data.shipping.type=="withdrawal") || (this.order.data.shipping.type=='payment'))
                return this.order.estimated_shipping_date.formated
            else
                return `${time} Dias úteis após a aprovação do pagto`
        },
        status_value() {
            let status_id = this.order.status_id
            let array = this.statusList
            if(!array) return "pending"
            let status = array.find(x=>x.id==status_id)
            return status.value
        },
        order_status() {
            let status_id = this.order.status_id
            let array = this.statusList
            if(array==undefined) return "Pendente"
            let status = array.find(x=>x.id==status_id)
            switch (status.value) {
                case "forwarded":
                    return "Encaminhado"
                    break
                case "pending":
                    return "Pendente"
                    break
                case "delivered":
                    return "Entregue"
                    break
                case "canceled":
                    return "Cancelado"
                    break
                case "approved":
                    return "Confirmado"
                    break
                case "production":
                    return "Em Produção"
                    break
                case "finished":
                    return "Concluido"
                    break
                default:
                    return this.order.status.name
                    break
            }
        },
    }
}
</script>
