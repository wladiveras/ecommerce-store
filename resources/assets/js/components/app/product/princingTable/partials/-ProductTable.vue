<template>
    <div class="table-responsive infinite-list" v-infinite-scroll="load" v-if="state.filter.loaded">
        <table class="table table-striped mb-0 table-hover">
            <thead>
                <tr>
                    <th class="align-middle" style="widht:5%" v-if="state.show_skuid">SKU</th>
                    <template v-for="(variation,i) in state.product.variations">
                        <th class="align-middle" v-if="showColumn(variation)" :key="i">{{variation}}</th>
                    </template>
                    <th class="text-center align-middle" style="width:40%">Meu Pedido</th>
                    <th class="text-center align-middle">Valor</th>
                </tr>
            </thead>
            <tbody v-if="skus.data.length>0">
                <template v-for="(sku,i) in skus.data">
                    <row-table
                        :selected_row="selected_row"
                        :sku="sku"
                        :product="state.product"
                        :state="state"
                        :_key="i"
                        :key="i"
                        @onSelected="selectedRow"
                    />
                </template>
            </tbody>
        </table>
        <div v-if="skus.data.length<=0">
            <h5
                class="text-center mt-4"
                v-if="!loading"
            >Nenhum resultado encontrado para este filtro</h5>
        </div>
        <div class="mt-0 py-5" v-loading="true" v-if="!finished"></div>
    </div>
</template>
<script>
export default {
    props: ["state"],
    data() {
        return {
            selected_row: null,
            ignore_variation: ["Quantidade", "Acabamento Padrão"],
            skus: {
                next_page: 1,
                current_page: 0,
                data: [],
                last_page: 999
            },
            loading: false,
            initialized: false
        }
    },
    components: {
        "row-table": require("./-TableRow.vue").default,
    },
    watch: {
        "state.filter.form": {
            handler() {
                if (this.state.filter.loaded) {
                    this.initialized = true
                    this.initTable()
                }
            },
            deep: true
        },
    },
    computed: {
        finished() {
            return this.skus.next_page >= this.skus.last_page
        }
    },
    methods: {
        selectedRow(val) {
            this.selected_row = val
        },
        initTable() {
            this.skus.next_page = 1
            this.skus.page = 0
            this.skus.data = []
            this.skus.last_page = 999
            this.load()
        },
        showColumn(variation) {
            return !this.ignore_variation.includes(variation)
        },
        load() {
            if (!this.initialized) return
            if (this.loading) return
            if (this.finished) return
            this.loadSkus()
        },
        loadSkus() {
            this.loading = true
            let data = Object.assign(this.state.filter.form, {})
            data.page = this.skus.next_page
            data.product_id = this.state.product.id
            this.$http.post("compra-rapida/skus", data).then(res => {
                if (this.skus.data.length == 0) this.skus = res.data
                else {
                    this.skus.current_page = res.data.current_page
                    this.skus.data = this.skus.data.concat(res.data.data)
                }
                this.skus.next_page = res.data.current_page + 1
                this.loading = false
                this.state.loading = false
            }).catch(er => {
                console.log(er)
            })
        }
    }
}
</script>

<style scoped lang="scss">
.infinite-list {
    max-height: 1000px;
    overflow-y: auto;
}
</style>