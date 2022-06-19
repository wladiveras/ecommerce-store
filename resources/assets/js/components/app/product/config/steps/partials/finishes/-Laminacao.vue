<template>
    <div v-if="has_laminacao">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="d-flex align-items-center">
                    Laminações
                    <template v-if="laminacao">
                        <a href="#" class="text-danger ml-2 f-12" @click.prevent="limpar" style="text-decoration: underline;">Remover Laminação</a>
                    </template>
                </h4>
            </div>
        </div>
        <div class="row">
            <template v-for="(o,i) in Object.keys(options)">
                <item-laminacao :type="o" v-model="laminacao" class="col-md-3 col-sm-12"  :key="options[o].frente.index" :val="options[o].frente.index"  v-if="options[o].frente.label"  :option="options[o].frente" :selected="laminacao"/>
                <item-laminacao :type="o" v-model="laminacao" class="col-md-3 col-sm-12"  :key="options[o].verso.index"  :val="options[o].verso.index"  v-if="options[o].verso.label"   :option="options[o].verso"  :selected="laminacao"/>
                <item-laminacao :type="o" v-model="laminacao" class="col-md-3 col-sm-12"  :key="options[o].ambos.index"  :val="options[o].ambos.index"  v-if="options[o].ambos.label"   :option="options[o].ambos"  :selected="laminacao"/>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    props:["items","product","shared"],
    components : {
        "item-laminacao" : require("./-ItemLaminacao.vue").default
    },
    watch: {
        laminacao(new_values=null,old_values=null) {
            if(!new_values) return
            if(new_values) {
                new_values = new_values.split("_")
                new_values = this.options[new_values[1]][new_values[2]]
            }
            if(old_values) {
                let values = old_values.split("_")
                let finish = this.options[values[1]][values[2]]

                if(!this.shared.data.finishes[finish.finish.id])
                    this.$set(this.shared.data.finishes, finish.finish.id,{})

                this.shared.data.finishes[finish.finish.id].qty   = 0
                this.shared.data.finishes[finish.finish.id].time  = 0
                this.shared.data.finishes[finish.finish.id].ref   = null
            }
            this.calc_finish(new_values)
        }
    },
    computed : {
        has_rules() {
           return this.shared.data.skus[0].sku.amount_rule.match(/[^\d]/)
        }
    },
    data() {
        return {
            laminacao : null,
            has_laminacao : [],
            types : {
                brilho : [],
                fosca  : [],
            },
            options : {
                brilho : {
                    frente : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                    verso : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                    ambos : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                },
                fosca  : {
                    frente : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                    verso : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                    ambos : {
                        index     : null,
                        label     : null,
                        type      : null,
                        type_desc : null,
                        finish    : null,
                    },
                },
            }
        }
    },
    mounted() {
        this.process()
    },
    methods : {
        limpar() {
            let values = this.laminacao.split("_")
            let finish = this.options[values[1]][values[2]]
            this.shared.data.finishes[finish.finish.id].qty   = 0
            this.shared.data.finishes[finish.finish.id].time  = 0
            this.shared.data.finishes[finish.finish.id].ref   = null
            this.shared.data.finishes[finish.finish.id].ref   = null
            this.laminacao = null
        },
        set_finish_price(qty,price,time,ref,finish) {
            if(!this.shared.data.finishes[finish.id]) this.shared.data.finishes[finish.id] = {qty: qty , price : 0, name : finish.name.toLowerCase() }
            this.shared.data.finishes[finish.id].price = price;
            this.shared.data.finishes[finish.id].time  = time;
            this.shared.data.finishes[finish.id].qty   = qty;
            this.shared.data.finishes[finish.id].ref   = ref;
            this.force_reative()
        },
        force_reative() {
            if (this.shared.step.position < 10) {
                this.shared.data.skus = JSON.parse(JSON.stringify(this.shared.data.skus))
            }
            this.shared.data.finishes = JSON.parse(JSON.stringify(this.shared.data.finishes))
        },
        get_qty(finish) {
            let qty = 0
            if(!this.shared.data.skus) return qty
            let skus = this.shared.data.skus
                console.log(finish)

            for(let sku of skus) {
                if(this.has_rules || finish.data.multiply == "sku") qty+=Number(sku.qty)
                else qty+=(Number(sku.attribute)*Number(sku.qty))
            }
            return qty
        },
        calc_finish(new_finish) {
            let sku  = {
                id : null,
                price : 0,
            }
            let skus = this.shared.data.skus
            let qty_sku = this.get_qty(new_finish.finish)
            for(let row in skus) {
                if(skus[row].sku.reseller_price > sku.price) {
                    sku.id = skus[row].sku.id
                    sku.price = skus[row].sku.reseller_price + (new_finish.finish.price * this.get_qty)
                }
            }

            let data = {
                finishes: this.shared.data.finishes,
                finish_ref_id : new_finish.finish.finish_ref_id,
                department    : this.product.department,
                sku_id        : sku.id,
                qty_sku       : qty_sku,
                qty_finish    : 1
            }
            Object.assign(this.shared.data.requests, { [new_finish.finish.id] : data });
            this.$http.post(this.$root.root_url + "/api/produto_config/get_calc_finishes", data ).then( res => {
                res = res.data
                if (!res.success) {
                    this.loading = false
                    return this.$toastr.error(res.message)
                }
                this.loading=false
                return this.set_finish_price(1,res.data.additionalPrice * qty_sku,res.data.additionalTime,res.data.ref ? res.data.ref : null, new_finish.finish)
            }).catch( er => {
                console.log(er)
                this.loading=false
            })
        },
        process() {
            this.getTypes()
            this.getOptions()
        },
        getTypes() {
            this.items.map( x => { if(x.name.indexOf("Fosca")>=0) return this.types.fosca.push(x)})
            this.items.map( x => { if(x.name.indexOf("Brilho")>=0) return this.types.brilho.push(x)})
        },
        getOptions() {
            for(let x in this.types.fosca)  this.makeValueDescription("fosca",this.types.fosca[x],x,  "Laminação Fosca")
            for(let x in this.types.brilho) this.makeValueDescription("brilho",this.types.brilho[x],x,"Laminação Brilho")
        },
        makeValueDescription(type,x,index,label) {
            if(x.name.indexOf("1/0")>=0) {
                let _index = index+"_"+type+"_frente"
                this.has_laminacao                   = true
                this.options[type].frente.index      = _index
                this.options[type].frente.label      = label
                this.options[type].frente.type       = "1/0"
                this.options[type].frente.type_desc  = "Frente 1/0"
                this.options[type].frente.finish     = this.types[type][index]
            }
            if(x.name.indexOf("0/1")>=0) {
                let _index = index+"_"+type+"_verso"
                this.has_laminacao                  = true
                this.options[type].verso.index      = _index
                this.options[type].verso.label      = label
                this.options[type].verso.type       = "0/1"
                this.options[type].verso.type_desc  = "Verso 0/1"
                this.options[type].verso.finish     = this.types[type][index]
            }
            if(x.name.indexOf("1/1")>=0) {
                let _index = index+"_"+type+"_ambos"
                this.has_laminacao                  = true
                this.options[type].ambos.index      = _index
                this.options[type].ambos.label      = label
                this.options[type].ambos.type       = "1/1"
                this.options[type].ambos.type_desc  = "Frente e Verso 1/1"
                this.options[type].ambos.finish     = this.types[type][index]
            }
        }
    }
}
</script>
