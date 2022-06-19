<template>
  <tr>
    <td>
      <ul class="pl-4 mb-0">
        <li v-for="ref_id in processed_item.processed_content.ref_ids">{{ref_id}}</li>
      </ul>
    </td>
    <td>
      <collapse-item-detail :item="processed_item" :index="index" />
    </td>
    <td>{{processed_item.processed_content.qty}}</td>
    <td>{{ final_price(processed_item,index).currency() }}</td>
    <td>
      <p class="m-0">{{ status_desc(processed_item) }}</p>
      <p class="m-0" v-if="processed_item.data.cancel_reason">
        <small class="text-danger">{{processed_item.data.cancel_reason}}</small>
      </p>
    </td>
    <td></td>
  </tr>
</template>
<script>
export default {
  props: ["items","statusItemList","index"],
  components: {
    "collapse-item-detail": require("./-CollapseItemDetail.vue").default,
  },
  computed: {
    processed_item() {
      let items = this.items
      let item = items[0]
      item.processed_content = {
        order_sku_ids: [],
        ref_ids: [],
        qty: 0,
        price: 0,
        ref_names: []
      }
      for (var i in items) {
        item.processed_content.order_sku_ids.push(items[i].id)
        item.processed_content.ref_ids.push(items[i].ref_id ? items[i].ref_id : "Aguardando Código")
        item.processed_content.ref_names[0] = items[i].data.config_info.ref_name
        let has_rule = this.has_rule(items[i].data.sku.amount_rule)
        if (has_rule)
          item.processed_content.qty += parseInt(items[i].qty)
        else
          item.processed_content.qty += Number(items[i].data.sku.attributes[items[i].data.sku.attributes.length - 1]) * Number(items[i].qty)

        /*
        item.processed_content.price += Number(items[i].data.sku.reseller_price)
        let measures = items[i].data.config_info.measures
        if (measures.unit && measures.height && measures.width)
          item.processed_content.price *= this.get_measure_multiplier(measures,item.processed_content.qty)
        else
          item.processed_content.price *= Number(items[i].qty)
        */
        item.processed_content.price += items[i].final_price

        if (item.status_id < items[i])
          item.status_id = items[i]
      }

      //item.processed_content.price += this.get_add_price(items[i])

      item.processed_content.qty += Number(item.processed_content.qty) > 1 ? " Unidades" : " Unidade"
      return item
    },
  },
  methods: {
    get_add_price(item) {
      let price = 0;
      if (item.data.config_info.additional.additional_attributes) {
        let attrs = item.data.config_info.additional.additional_attributes;
        for (let row in attrs) {
          price += attrs[row].price;
        }
      }
      if (item.data.config_info.finishes.finishes) {
        let fins = item.data.config_info.finishes.finishes;
        for (let row in fins) {
          price += fins[row].price;
          if (fins[row].name == "Corte e Vinco") {
            price += 150;
          }
        }
      }
      return price;
    },
    has_rule(value) {
      return (value.match(/[^\d]/) != null);
    },
    final_price(item,index) {
      return item.processed_content.price
      let price = Number(item.data.sku.reseller_price)
      let measures = item.data.config_info.measures
      if (measures.unit && measures.height && measures.width) price *= this.get_measure_multiplier(measures,item.qty)
      else price *= Number(item.qty)
      if (index == 0) price += this.get_additional_price(item)
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
    }
  }
}
</script>