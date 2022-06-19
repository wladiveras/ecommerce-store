<template>
  <div class="col-md-4 px-0 col-12 mb-3">
    <div class="card detail-card h-100">
      <div class="card-header">
        <h4 class="title">Total</h4>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-end">
          <div class="col-md-9 col-sm-12">
            <table class="w-100">
              <tr>
                <td class="text-left">Subtotal</td>
                <td class="text-right">{{ subtotal.currency() }}</td>
              </tr>
              <tr v-if="taxAddCreditCard">
                <td>
                  <el-tooltip content="Referente ao Parcelamento">
                    <span>
                      Juros
                      <span class="text-danger">*</span>
                    </span>
                  </el-tooltip>
                </td>
                <td>{{ taxAddCreditCard.currency() }}</td>
              </tr>
              <template v-for="add in additional" v-if="false">
                <tr v-if="add.price>0">
                  <td class="text-left">{{add.name}}</td>
                  <td class="text-right">+{{ add.price.currency() }}</td>
                </tr>
              </template>
              <tr v-if="taxAddCreditCard">
                <td>
                  <el-tooltip content="Referente ao Parcelamento">
                    <span>
                      Juros
                      <span class="text-danger">*</span>
                    </span>
                  </el-tooltip>
                </td>
                <td>{{ taxAddCreditCard.currency() }}</td>
              </tr>
              <tr v-if="order.data.shipping.type!='payment'">
                <td class="text-left">Entrega</td>
                <td class="text-right">{{ shipping_price }}</td>
              </tr>
              <tr>
                <td class="text-left">
                  <b>Total</b>
                </td>
                <td class="text-right">
                  <b class="orange">{{ order.data.totalPrice.currency() }}</b>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["order"],
  data() {
    return {
      interest: [0,4.02,4.56,4.89,5.13,5.8],
      payment: this.order.payments[this.order.payments.length - 1]
    }
  },
  computed: {
    subtotal() {
      let value = 0
      let skus = this.order.skus
      for (let i in skus) value += skus[i].final_price
      return value
    },
    shipping_price() {
      let price = this.order.data.shipping.price ? this.order.data.shipping.price : 0
      switch (this.order.data.shipping.type) {
        case "payment":
          price = this.order.data.shipping_address.price
          return price
          break
        default:
          return price == 0 ? "Grátis" : price.currency()
          break
      }
    },
    taxAddCreditCard() {
      let payment = this.payment
      let installment = payment.data.installment
      let percent = this.interest[installment - 1]
      let amount = this.order.data.totalPrice
      let tax = ((amount / 100) * percent)
      return tax
    },
    additional() {
      let skus = this.order.skus
      let _add = []
      let groups = _.groupBy(skus,"group_ref")
      for (let i in groups) {
        let attrs = groups[i][0].data.config_info.additional.additional_attributes
        for (let y in attrs) {
          _add.push(attrs[y])
        }
      }
      return _add
    }
  }
}
</script>
<style scoped lang="scss">
.card-body {
  background-color: #fcfac9;
}
b {
  &.orange {
    color: #eb5e00 !important;
  }
}
</style>