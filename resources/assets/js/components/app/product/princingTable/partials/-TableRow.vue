<template>
    <tr
        class="infinite-list-item table-row"
        @click="selectRow"
        v-bind:class="{'selected':isSelected}"
        :title="`${isSelected? 'Clique para selecionar sku' : ''}`"
    >
        <td class="align-middle" v-if="state.show_skuid">#{{String(sku.id).padStart(6, "0")}}</td>
        <template v-for="(variation,v) in state.product.variations">
            <td class="align-middle" v-if="$parent.showColumn(variation)" :key="v">
                <div v-html="itemFormat(sku.attributes[v],variation)"></div>
            </td>
        </template>
        <td class="align-middle">
            <div class="d-flex align-items-center justify-content-center" v-if="isSelected">
                <template>
                    <div>
                        <label class="mb-0">Quantidade</label>
                        <el-input-number
                            :disabled="!isSelected"
                            class="w-100"
                            size="medium"
                            v-model="qty"
                            controls-position="right"
                            :min="qty_min"
                        />
                    </div>
                    <div v-if="hasMeasure" class="ml-2">
                        <div class="d-flex flex-row">
                            <div>
                                <label class="mb-0">Altura (m)</label>
                                <el-input-number
                                    class="w-100"
                                    size="medium"
                                    v-model="height"
                                    controls-position="right"
                                    :min="min_height"
                                    :max="max_height"
                                />
                            </div>
                            <div>
                                <label class="mb-0">Largura (m)</label>
                                <el-input-number
                                    class="w-100"
                                    size="medium"
                                    v-model="width"
                                    controls-position="right"
                                    :min="min_width"
                                    :max="max_width"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </td>
        <td class="align-middle">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <template v-if="validQty">
                    <b class="princing_table_price mv-0">{{ price.currency() }}</b>
                    <small class="mb-2 text-center" v-if="qty!=0">
                        <template v-if="hasMeasure">
                            {{price_per_unit.currency()}}
                            por m² impresso
                        </template>
                        <template v-else>
                            {{qty_unit}} unidade
                            <template v-if="qty_unit>1">s</template>
                            , {{price_per_unit.currency()}}
                            por unidade
                        </template>
                    </small>
                    <small class="mb-2" v-else>Selecione a quantidade desejada</small>
                    <div
                        class="d-flex flex-row justify-content-between align-items-center"
                        v-if="validQty"
                    >
                        <template v-if="isSelected">
                            <div class="d-flex flex-column mr-2">
                                <template v-if="product.templates.length>0">
                                    <button
                                        class="btn btn-sm btn-secondary mr-2"
                                        @click="showing_template = true"
                                        v-if="!showing_template"
                                    >Gabaritos</button>
                                    <template v-else>
                                        <a
                                            v-for="(t,index) in product.templates"
                                            target="_BLANK"
                                            class="link"
                                            :href="t.raw_url"
                                            :key="index"
                                        >Gabarito {{t.type}}</a>
                                        <a
                                            class="link mt-3"
                                            style="color:red!important;"
                                            @click="showing_template = false"
                                        >Esconder Gabaritos</a>
                                    </template>
                                </template>
                            </div>
                            <button class="btn-sm btn btn-primary" @click="buy(sku)">Comprar</button>
                        </template>
                    </div>
                    <template v-else>
                        <h4 class="mb-0">
                            <i class="material-icons text-danger">block</i>
                        </h4>
                        <span class="text-danger">{{sku.attributes[sku.attributes.length-1]}}</span>
                    </template>
                </template>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
    props: ["sku", "product", "state", "_key", "selected_row"],
    data() {
        return {
            showing_template: false,
            min_value: 0.01,
            max_value: 999999,
            qty_unit: 0,
            qty: 0,
            qty_min: 0,
            qty_max: 0,
            width: 0,
            height: 0,
            max_width: 0,
            max_height: 0,
            min_width: 0,
            min_height: 0,
            is_pack: false
        }
    },
    mounted() {
        let rule = this.getQtyRule()
        if (Array.isArray(rule)) {
            this.qty_min = rule[0] ? rule[0] : 1
            this.qty_max = rule[1] ? rule[1] : 99999
        } else {
            this.qty_min = 1
            this.qty_max = 99999
            this.is_pack = true
        }
        this.qty = this.qty_min
        if (this.hasMeasure) {
            let attr = this.getFormatoArray(this.sku.attributes[this.product.variations.indexOf("Formato")])
            this.max_height = ((attr[0] != "-") ? Number(attr[0] / 100) : this.max_value)
            this.max_width = ((attr[1] != "-") ? Number(attr[1] / 100) : this.max_value)
            this.min_height = this.min_value
            this.height = this.min_value
            this.min_width = this.min_value
            this.width = this.min_value
        }
    },
    watch: {
        selected_row(val) {
            if (val != this._key) {
                this.qty = this.qty_min
            }
        }
    },
    computed: {
        isSelected() {
            return this.selected_row == this._key
        },
        price_per_unit() {
            let rule = this.getQtyRule()
            if (!Array.isArray(rule)) {
                let qty = Number(this.sku.attributes[this.product.variations.indexOf("Quantidade")])
                return this.sku.reseller_price / qty
            } else {
                return this.sku.reseller_price
            }
        },
        hasMeasure() {
            let index = this.product.variations.indexOf("Formato")
            if (index < 0) return false
            return (this.sku.attributes[index].indexOf("M²") >= 0)
        },
        validQty() {
            let rule = this.getQtyRule()
            if (!Array.isArray(rule)) {
                this.qty_unit = rule * this.qty
                return true
            }
            let result = false
            if (rule[1]) result = ((this.qty <= rule[1]) && (this.qty >= rule[0]))
            else result = this.qty >= rule[0]
            this.qty_unit = this.qty
            return result
        },
        price() {
            let qty = this.qty == 0 ? 1 : this.qty
            if (!this.hasMeasure) return Number(this.sku.reseller_price * qty)

            //tem medidas
            let area = this.area
            let full_area = area * qty
            let price = Number(this.sku.reseller_price)
            let multiplicator = ((full_area <= 1) ? 1 : full_area)
            return price * multiplicator
        },
        area() {
            if (!this.hasMeasure) return 0
            return (this.width * this.height)
        },
        hasSizes() {
            return Array.isArray(this.state.filter.options["Tamanho"])
        },
        selectedSize() {
            return this.state.filter.form["Tamanho"]
        },
        artType() {
            return this.state.filter.form.art_type
        }
    },
    methods: {
        selectRow() {
            this.$emit("onSelected", this._key)
        },
        itemFormat(attribute, variation) {
            switch (variation) {
                case "Formato":
                    return this.formatFormato(attribute)
                    break
                default:
                    return attribute ? attribute : "-"
                    break
            }
        },
        getFormatoArray(attribute) {
            let hasSize = (attribute.indexOf("M²") > -1)
            if (!hasSize) return attribute
            let attr = attribute.replace("M² ", "").replace("[", "").replace("]", "")
            attr = attr.split(",")
            return attr
        },
        formatFormato(attribute) {
            try {
                let attr = this.getFormatoArray(attribute)
                if (attribute.indexOf("M²") == -1) return attr
                let header = attr[0]
                let width = attr[1]
                if ((header != "-") && (width != "-")) return `Limite de ${header / 100}x${width / 100} m`
                if ((header != "-") && (width == "-")) return `Limite de ${header / 100} m de altura`
                if ((header == "-") && (width != "-")) return `Limite de ${width / 100} m de largura`
                if ((header == "-") && (width == "-")) return `Sem Limitações`
                return attribute
            } catch {
                return attribute
            }
        },
        getQtyRule() {
            let rule = this.sku.attributes[this.sku.attributes.length - 1]
            if (this.hasRule(rule)) {
                rule = rule.replace("De ", "").replace("até ", "").replace("A partir de", "").trim().split(" ")
                rule = rule.map(x => Number(x))
                return rule
            }
            return rule
        },
        hasRule(rule) {
            return isNaN(Number(rule))
        },
        buy(sku) {
            let hasSizes = this.hasSizes
            if (this.qty == 0) return this.$toastr.error("Selecione a quantidade desejada")
            if (hasSizes && !this.state.filter.other["Tamanho"]) return this.$toastr.error("Selecione o tamanho")
            if (!this.state.filter.art_type) return this.$toastr.error("Selecione o tipo de arte")
            let data = {}
            data.sku_id = this.sku.id
            data.qty = this.qty
            data.calc_qty = this.qty_unit
            data.art = this.state.filter.art_type
            if (hasSizes) data.size = this.state.filter.other["Tamanho"]
            if (this.hasMeasure) data.measures = JSON.stringify({ height: this.height, width: this.width })
            window.location.href = `${this.$root.root_url}/produtos/${this.product.slug}/compra-rapida/comprar?${new URLSearchParams(data).toString()}`
        }
    }
}
</script>
<style lang="scss" scoped>
.table-row {
    cursor: pointer;
    opacity: 0.6;
    &:hover {
        transition: 0.3s;
        opacity: 1;
    }
    &.selected {
        cursor: default;
        opacity: 1;
        outline: 3px solid green;
    }
}
</style>