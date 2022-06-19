<template>
    <tr>
        <td>
            <ul class='pl-4 mb-0'>
                <li>{{ item.ref_id ? item.ref_id : "Aguardando Integração"}}</li>
            </ul>
        </td>
        <td><collapse-item-detail :item="item" :index="index" /></td>
        <td>
            <template v-if="has_rule(item.data.sku.amount_rule)">{{parseInt(item.qty)}} Unidade<template v-if="item.qty>1">s</template></template>
            <template v-else>{{parseInt(item.data.sku.attributes[item.data.sku.attributes.length-1])*Number(item.qty)}} Unidade<template v-if="item.data.sku.attributes[item.data.sku.attributes.length-1]>1">s</template></template>
        </td>
        <td>{{ final_price(item,index).currency() }}</td>
        <td> 
            <p class="m-0">{{ status_desc(item) }}</p>
            <p  class="m-0" v-if="!can_delete(item.status_id) && item.data.cancel_reason">
                <small class="text-danger">{{item.data.cancel_reason}}</small>
            </p>
        </td>
        <td>
            <template v-if="can_delete(item.status_id)&&item.ref_id">
                <a  href="#" @click="destroy(item,index)" class="link">Cancelar</a>
            </template>
        </td>
    </tr>
</template>
<script>
export default {
    props : ["item","statusItemList","canCancel","index"],
    components : {
        "collapse-item-detail" : require("./-CollapseItemDetail.vue").default
    },
    methods : {
        has_rule(value) {
            return (value.match(/[^\d]/) != null);
        },
        final_price(item,index) {
            let price = Number(item.data.sku.reseller_price)
            let measures = item.data.config_info.measures
            if(measures.unit && measures.height && measures.width) price *= this.get_measure_multiplier(measures,item.qty)
            else price *= Number(item.qty)
            if(index == 0) price += this.get_additional_price(item)
            return price
        },
        get_measure_multiplier(measures, qty) {
            let unit = measures.unit
            let finalMultiplier = (measures.height * measures.width) * qty
            if(unit=="cm") finalMultiplier /= (100*100)
            if(finalMultiplier > 1) return finalMultiplier
            return 1
        },
        get_additional_price(item)
        {
            let price = 0
            if(item.data.config_info.additional.additional_attributes) {
                let adds = item.data.config_info.additional.additional_attributes
                for(let row in adds) price += adds[row].price
            }
            if(item.data.config_info.finishes.finishes) {
                let fins = item.data.config_info.finishes.finishes
                for(let row in fins) {
                    if(fins[row].qty > 0) {
                        price += fins[row].price
                        if(fins[row].name == "Corte e Vinco") price += 150   
                    }
                }
            }
            return price
        },
        status_desc(item) {
            if (!item.data.file.file) return "Nenhuma arte enviada";
            return this.statusItemList[item.status_id];
        },
        can_delete(status_id) {
            if(!this.canCancel) return false
            if(this.statusItemList[status_id]!="Aguardando") return false
            return true
        },
        destroy(item,index) {
            return console.log("teste")
            if(!this.can_destroy())
                return false
            let data = {
                order_id : item.order_id,
                order_sku : item.id,
                price : Number(this.finalPrice(item,index))
            }
            if(data.price>1000) return this.$toastr.error("Devido ao Valor do Pedido, é necessário entrar em contato com o Atendimento da Padrão Color para efetuar o Cancelamento")
            this.$swal.confirm("Confirmação","Deseja mesmo cancelar este pedido ?","warning",() => {
                this.$swal.input("Motivo","Digite o motivo de cancelamento ?","info", reason =>
                {
                    if(!reason) return this.$toastr.error("O motivo é obrigatório para cancelamento");
                    
                    let loading = this.$loading({
                        lock: true,
                        text: "Aguarde, Efetuando o Cancelamento ...",
                        background: 'rgba(0, 0, 0, 0.7)'
                    })
                    data.reason = reason
                    this.$http.post(this.$root.root_url+"/compras/cancelamento",data).then( res => {
                        res = res.data
                        if(!res.success)
                        {
                            this.$toastr.error(res.message)
                            return loading.close()
                        }
                        window.location.reload()
                        loading.close()
                    }).catch( er => {
                        console.log(er)
                    })
                })
            })
        }
    }
}
</script>