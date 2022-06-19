<template>
    <div class="col-md-6 pr-0 col-12 mb-3">
        <div class="card detail-card">
            <div class="card-header">
                <h4 class="title d-flex align-items-center"><i class="material-icons icon">local_shipping</i>Entrega</h4>
            </div>
            <div class="table-responsive">
                <table class="table-striped items">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ shipping_type }}</td>
                            <td>{{ shipping_price }}</td>
                        </tr>
                        <tr class="text-center" v-if="(shipping_type == 'Envio para o Cliente' || shipping_type == 'Frete') && shipping_url != null">
                            <td colspan="2">
                                <div class="d-flex align-items-center justify-content-center my-3">
                                    <!-- <i class="material-icons icon mr-3">local_shipping</i> -->
                                    <i _ngcontent-hrg-c19 class="material-icons icon-image-preview mr-3">track_changes</i>
                                    Acompanhe sua Compra, 
                                    <a :href='shipping_url' target="_blank" class="ml-1 link">Clique aqui</a>
                                </div>
                            </td>
                        </tr>    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["order", "shipping"],
    
    computed : {
        address() {
            return `${this.order.data.shipping_address.street}, N° ${this.order.data.shipping_address.number}, ${this.order.data.shipping_address.district}, ${this.order.data.shipping_address.city} - ${this.order.data.shipping_address.state}, ${this.order.data.shipping_address.complement}`
        },
        shipping_type() {
            let shipping = this.order.data.shipping
            switch(shipping.type) {
                case "payment":
                    return "Rota"
                break
                case "withdrawal":
                    return "Retirar em uma loja Cria Fácil"
                break
                 case "retirada_balcao":
                    return "Retirar em um balcão de entrega"
                break
                case "client_shipping":
                    return "Envio para o Cliente"
                break
                case "shipping":
                    return "Frete"
                break
                default :
                    return shipping.type
                break
            }
        },
        shipping_price() {
            let price = this.order.data.shipping.price ? this.order.data.shipping.price : 0
            switch(this.order.data.shipping.type) {
                case "payment":
                    price = this.order.data.shipping_address.price ? this.order.data.shipping_address.price.currency() : "preço da rota"
                    return price  
                break
                default :
                    return price==0 ? "Grátis" : price.currency()
                break
            }
        }, 
        shipping_url(){
            if(this.shipping)
            {
                return "http://www.jadlog.com.br/siteDpd/tracking.jad?cte=" + this.shipping.data.shippingCode
            }else{ 
                return null
            }
        }
    }
}
</script>