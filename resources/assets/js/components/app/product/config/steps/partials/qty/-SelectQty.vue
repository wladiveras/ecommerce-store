<template>
<div v-loading="loading">
    <div class="row">
        <div class="col-md">
            <h5>Selecione a quantidade desejada</h5>
            <div class="d-flex flex-wrap qty-pool">
                <template v-for="(val, i) in qty_options">
                    <div class="col-md-4 mb-2">
                        <button class="qty-btn" @click="addAmount(val)">{{val}}</button>
                    </div>
                </template>
            </div>
        </div>
        <div class="col-md-1">
            OU
        </div>
        <div class="col-md">
            <h5>Digite a quantidade</h5>
            <input type="number" @keyup="change" class="pl-5 form-control count-input" :step="min" :min="min" :max="max" :maxlength="max.length" v-model="amount" placeholder="Digite a quantidade aqui..." autofocus>
        </div>
        <transition name="fade">
            <template v-if="modal.show && amount > 0">
                <sku-select class="mt-4" :values="modal" />
            </template>
        </transition>
    </div>
</div>
</template>

<script>
export default {
    props: ['shared', 'data'],
    data() {
        return {
            default_amount: null,
            timewaiting: 2,
            change_interval: null,
            amount: null,
            loading: false,
            min: 1,
            max: 999999999,
            qty: null,
            sorted_qty: null,
            qty_options: null,
            priceSum: 0,
            multiples: null,
            modal: {
                show: false,
                values: null
            }
        }
    },
    mounted() {
        if(this.shared.isPreset){
            this.amount = this.shared.presetQty || null
            this.load_qty()
        }else{
            this.load_qty()
        }
    },
    components: {
        "sku-select": require("./-SkuSelect.vue").default
    },
    methods: {
        presetInitialAmount(res = 0){
            if(this.shared.isPreset && this.shared.presetQty){
                res = this.shared.presetQty
            }
            return res
        },
        process() {
            this.sort_qty()
            this.set_qty_options()
            this.min = this.amount
            this.max = this.qty_options[this.qty_options.length - 1] * 1000
            this.confirm()
        },
        set_qty_options() {
            if (!this.has_rule())
                return this.set_qty_options_if_dont_has_rules()
            else
                return this.set_qty_options_if_has_rules()
        },
        set_qty_options_if_has_rules() {
            let multiplyTypes = [5, 10, 20, 50, 100, 200]
            let data = []
            let aux = this.sorted_qty[0].rule
            if (aux.indexOf(">") != -1) {
                this.amount = this.presetInitialAmount(Number(this.clean(aux)))
                this.default_amount = null
            } else if (aux.indexOf("<") != -1) {
                let aux2 = 0
                while (!eval(aux2 + aux)) {
                    aux2++
                }
                this.amount = this.presetInitialAmount(Number(aux2))
                this.default_amount = null
            }
            this.qty_options = multiplyTypes
        },
        set_qty_options_if_dont_has_rules() {
            let multiplyTypes = [2, 5, 10, 50, 100, 200]
            let data = []
            this.amount = this.presetInitialAmount(null)
            this.default_amount = null
            data.push(Number(this.sorted_qty[0].amount))
            for (let i in multiplyTypes) {
                let calc = multiplyTypes[i] * data[0]
                data.push(calc)
            }
            this.qty_options = data
        },
        sort_qty() {
            let list = this.qty
            list = list.sort((a, b) => {
                return this.clean(a.rule) - this.clean(b.rule)
            })
            list = this.process_list(list)
            this.sorted_qty = list
        },
        clean(val) {
            return val.replace(/\D/g, '')
        },
        process_list(list) {
            for (var item in list) {
                let rule = list[item].rule;
                list[item] = Object.assign(list[item], {
                    fvalue: this.clean(list[item].rule)
                });
            }
            return list
        },
        getSelectedOptions(){
            let options = Object.assign([], this.$parent.$parent.$refs.step2.$refs.options.selected_options)
            if(this.shared.isPreset && this.shared.presetSkuQty && this.shared.presetQty)
                options.push(this.shared.presetSkuQty)

            return options
        },
        load_qty() {
            this.$http.post(this.$root.root_url + "/produtos/get_qty", {
                product_id: this.data.product.id,
                selected_options: this.getSelectedOptions()
            }).then((res) => {
                res = res.data
                if (!res.success) return this.$toastr.error(res.message)
                this.qty = res.data[0]
                this.process()
            });
        },



        // POS SELEÇÂO//
        change() {
            clearInterval(this.change_interval)
            this.$parent.ready = false
            let time = 0
            this.change_interval = setInterval( () => {
                if (time++ == (this.timewaiting - 1)) {
                    clearInterval(this.change_interval)
                    if (this.amount < this.min) return this.$toastr.error("A quantidade mímina deve ser " + this.min)
                    return this.confirm()
                }
            }, 1000)
        },
        confirm() {
            this.modal.show = false
            this.$parent.ready = false
            if (this.amount > this.max) return this.$toastr.error("Quantidade máxima excedida")
            if (!this.qty) return this.open_modal_select_sku([])
            if (((!this.amount) || (this.amount == 0))) return this.open_modal_select_sku([])
            if (!this.has_rule()) {
                let result = this.calc_qty(this.qty, this.amount, (result) => {
                    if (result.other_options) return this.open_modal_select_sku(result.other_options)
                    return this.find_sku(result)
                })
            } else {
                return this.simple_find(this.get_qty_with_rule())
            }
        },
        get_qty_with_rule() {
            let list = this.sorted_qty.sort((a, b) => b.fvalue - a.fvalue)
            let aux = list.find( (x) => Number(x.amount)==Number(this.amount)  )
            if(!aux) {
                for (var i in list) {
                    let amount = Number(this.amount),
                        ruleAmount = Number(list[i].fvalue)
                    let next = Number(i) + 1
                    list[i].qty = this.amount
                    switch (list[i].rule[0]) {
                        case '>':
                            if (amount >= ruleAmount || this.shared.isPreset) return list[i]
                            break
                        case '<':
                            if ((amount <= ruleAmount && (list[next] == undefined || amount > Number(list[next].fvalue))) || this.shared.isPreset) return list[i]
                    }
                }
            }
            aux.qty = this.amount
            return aux
        },
        clean_calc_array(array) {
            var options = array.options
            var _options = []
            for (let i = 0; i < options.length; i++) {
                if (options[i].qty > 0) {
                    _options.push(options[i]);
                }
            }
            array.options = _options
            return array
        },
        simple_find(value) {
            this.shared.data.skus = [value]
            this.shared.step.has_cover = (this.shared.data.skus[0].sku.data.cover ? (this.shared.data.skus[0].sku.data.cover.length > 0) : false)
            this.shared.step.has_finishes = (this.shared.data.skus[0].sku.finishes ? (this.shared.data.skus[0].sku.finishes.length > 0) : false)
            this.shared.step.finishes = this.shared.data.skus[0].sku.finishes
            this.$parent.ready = true
            this.loading = false
        },
        open_modal_select_sku(options) {
            setTimeout(() => {
                this.modal.values = options
                this.modal.min = this.min
                this.modal.show = true
                this.shared.data.skus = null
                this.loading = false
            }, 100)
        },
        calc_qty(options, default_amount, callback) {
            this.loading = true
            options = options.sort((a, b) => b.fvalue - a.fvalue)
            this.$http.post(this.$root.root_url + "/produtos/calc_qty", {
                options: options,
                default_amount: default_amount
            }).then( res => {
                res = res.data
                if (!res.success) return this.$toastr.error(res.message)
                let result = res.data
                return callback(result)
            })
        },
        addAmount(val) {
            if (val + this.amount > this.max) return this.$toastr.error("Quantidade máxima excedida")
            let amount = Number(this.amount)
            amount = Number(val)
            this.amount = amount
            this.confirm()
        },
        reset() {
            this.amount = this.default_amount
            this.shared.data.skus = null
            this.$parent.ready = false
            this.modal.show = false
        },
        has_rule() {
            let list = this.qty
            for (let item in list) {
                let value = list[item].amount.trim()
                if (this.is_not_number(value)) {
                    this.shared.step.has_pricing_role = true
                    this.$parent.has_rules = true
                    return true
                }
            }
            this.shared.step.has_pricing_role = false
            this.$parent.has_rules = false
            return false
        },
        is_not_number(value) {
            return value.match(/[^\d]/);
        },
        find_sku(combinations) {
            this.shared.data.skus = combinations.combination.skus
            this.shared.step.has_cover = this.shared.data.skus[0].sku.data ? (this.shared.data.skus[0].sku.data.cover ? (this.shared.data.skus[0].sku.data.cover.length > 0) : false) : false
            this.shared.step.has_finishes = (this.shared.data.skus[0].sku.finishes ? (this.shared.data.skus[0].sku.finishes.length > 0) : false)
            this.shared.step.finishes = this.shared.data.skus[0].sku.finishes
            this.$parent.ready = true
            this.loading = false
        },

    }
}
</script>
<style lang="scss" scoped>
.qty-pool {
    .qty-btn {
        width: 100%;
        border: 1px solid #7d1756;
        border-radius: 5px;
        background: #fff;
        padding: 10px;
        color: #7d1756;
        font-weight: 500;
    }
}
.count-wrapper {
    background-color: #F8F9FA;
    padding: 10px;
}

.count-wrapper .count-input {
    border: none;
    font-size: 18px;
    border-bottom: 1px solid #dcdfe6;
    text-align: left;
    background: transparent;
    margin-right: 30px;
    &::placeholder {
        color:#D45F51;
    }
}

.count-wrapper .count-input:focus {
    border-color: none;
    -webkit-box-shadow: none;
    box-shadow: none;
}

.count-wrapper .title {
    position: absolute;
    top: 13px;
    left: 24px;
}

.count-wrapper .custom-btn {
    position: absolute;
    right: 17px;
    top: -5px;
    border: none;
    background-color: #343a40;
    color: white;
    padding-right: 15px;
    border-radius: 5px;
    justify-self: end;
    font-size: 14px;
    padding: 12px 45px;
}

@media (max-width: 475px) {
    .count-wrapper .custom-btn {
        padding: 12px 15px;
    }
}

.count-wrapper .custom-btn.btn-confirm {
    right: 187px;
    background-color: #D6EF63;
    color: #1B4E01;
}

.count-wrapper .custom-btn.btn-confirm:hover {
    background-color: #1B4E01;
    color: #D6EF63;
    transition: .5s;
}

.count-wrapper .custom-btn:hover {
    background-color: #23272B;
    transition: .5s;
}

.qty-row .qty-btn {
    padding: 20px 50px;
    background-color: white;
    border: 1px solid #adb5bd;
}

.qty-row .qty-btn:hover {
    transition: .5s;
    background-color: #D6EF63;
}
</style>
