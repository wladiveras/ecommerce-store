<template>
  <div class="table-responsive">
    <table class="table-striped items">
      <thead>
        <tr>
          <th>Referência</th>
          <th>Produto</th>
          <th>Qtde</th>
          <th>Subtotal</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template v-if="grouped">
          <template v-for="(items,product_id) in grouped_skus">
            <grouped-item
              :statusItemList="statusItemList"
              :items="items"
              :index="product_id"
              :key="product_id"
            />
          </template>
        </template>
        <template v-else>
          <template v-for="group in grouped_skus">
            <tr v-for="(item, index) in group" :key="`${group[0].group_ref}-${index}`">
              <td>
                <p
                  class="mb-2 d-flex align-items-center"
                  v-if="status_desc(item)=='Com problemas' && canResendart"
                >
                  <i class="material-icons text-danger error_tag mr-2">warning</i>
                  <a
                    class="link"
                    :href="$root.root_url+'/compras/'+order.hashid+'/pedido/'+item.id+'/resendart'"
                  >
                    <b>Pedido com Problemas, Clique aqui para reenviar a arte !!!</b>
                  </a>
                </p>
                <ul class="pl-4 mb-0">
                  <li v-text="item.ref_id || 'Aguardando Integração'" />
                </ul>
              </td>
              <td>
                <collapse-item-detail :item="item" :index="index" />
              </td>
              <td v-text="renderQty(item)" />
              <td v-text="final_price(item,index).currency()" />
              <td>
                <p class="m-0" v-text="status_desc(item)" />
                <p class="m-0" v-if="!can_delete(item.status_id) && item.data.cancel_reason">
                  <small class="text-danger" v-text="item.data.cancel_reason" />
                </p>
              </td>
              <td class="text-right">
                <template v-if="can_delete(item.status_id)&&item.ref_id">
                  <a href="#" @click.prevent="destroy(item,index)" class="link-delete">
                    <i class="material-icons">delete</i>
                  </a>
                </template>
              </td>
            </tr>
          </template>
        </template>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  props: ["order","statusItemList","canCancel","grouped","canResendart"],
  components: {
    "collapse-item-detail": require("./-CollapseItemDetail.vue").default,
    "grouped-item": require("./-GroupedItem.vue").default
  },
  computed: {
    grouped_skus() {
      let skus = this.order.skus
      let groupedSkus = _.groupBy(skus,'group_ref')
      return groupedSkus
    },
  },
  methods: {
    renderQty({ qty,data: { sku: { amount_rule,attributes } } }) {
      let has_rule = this.has_rule(amount_rule),amount
      if (has_rule)
        amount = parseInt(qty)
      else
        amount = Number(attributes[attributes.length - 1]) * Number(qty)

      return `${amount} Unidade${amount > 1 ? "s" : ""}`
    },
    has_rule(value) {
      return (value.match(/[^\d]/) != null);
    },
    final_price(item,index) {
      let price = Number(item.data.sku.reseller_price)
      let measures = item.data.config_info.measures
      if (measures.unit && measures.height && measures.width) price *= this.get_measure_multiplier(measures,item.qty)
      else price *= Number(item.qty)
      if (index == 0) price += this.get_additional_price(item)
      item.final_price = price
      return price
    },
    get_measure_multiplier(measures,qty) {
      let unit = measures.unit
      let finalMultiplier = (measures.height * measures.width) * qty
      if (unit == "cm") finalMultiplier /= (100 * 100)
      if (finalMultiplier > 1) return finalMultiplier
      return 1
    },
    get_additional_price(item) {
      let price = 0
      if (item.data.config_info.additional.additional_attributes) {
        let adds = item.data.config_info.additional.additional_attributes
        for (let row in adds) price += adds[row].price
      }
      if (item.data.config_info.finishes.finishes) {
        let fins = item.data.config_info.finishes.finishes
        for (let row in fins) {
          if (fins[row].qty > 0) {
            price += fins[row].price
            if (fins[row].name == "Corte e Vinco") price += 150
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
      if (!this.canCancel) return false
      return this.statusItemList[status_id].trim().toLowerCase() == "aguardando"
    },
    can_destroy() {
      let shipping_type = this.order.data.shipping.type
      let payment_type = this.order.payments[0].type
      if ((shipping_type == "payment") && (payment_type == "creditcard")) {
        this.$toastr.error("Para cancelar esse pedido entre em contato com nosso atendimento")
        return false
      }
      if ((shipping_type == "withdrawal") && (payment_type == "creditcard")) {
        this.$toastr.error("Para cancelar esse pedido entre em contato com nosso atendimento")
        return false
      }
      if ((shipping_type == "retirada_balcao") && (payment_type == "creditcard")) {
        this.$toastr.error("Para cancelar esse pedido entre em contato com nosso atendimento")
        return false
      }
      if (((shipping_type == "shipping") || (shipping_type == "client_shipping")) && (payment_type == "creditcard")) {
        this.$toastr.error("Para cancelar esse pedido entre em contato com nosso atendimento")
        return false
      }
      return true
    },
    destroy(item,index) {
      if (!this.can_destroy()) return false
      let data = {
        order_id: item.order_id,
        order_sku: item.id,
        price: Number(this.final_price(item,index))
      }
      if (data.price > 1000) return this.$toastr.error("Devido ao Valor do Pedido, é necessário entrar em contato com o Atendimento da Padrão Color para efetuar o Cancelamento")
      this.$swal.confirm("Confirmação","Deseja mesmo cancelar este pedido ?","warning",() => {
        this.$swal.input("Motivo","Digite o motivo de cancelamento ?","info",reason => {
          if (!reason) return this.$toastr.error("O motivo é obrigatório para cancelamento")
          let loading = this.$loading({
            lock: true,
            text: "Aguarde, Efetuando o Cancelamento ...",
            background: 'rgba(0, 0, 0, 0.7)'
          })
          data.reason = reason
          this.$http.post(this.$root.root_url + "/compras/cancelamento",data).then(res => {
            res = res.data
            if (!res.success) {
              this.$toastr.error(res.message)
              return loading.close()
            }
            window.location.reload()
            loading.close()
          }).catch(er => {
            console.log(er)
          })
        })
      })
    }
  }
}
</script>
<style scoped lang="scss">
.link-delete {
  color: #c3c8cc !important;
}
</style>