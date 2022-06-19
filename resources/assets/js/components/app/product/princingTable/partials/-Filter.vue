<template>
    <div class="d-flex flex-column pb-4">
        <div class="d-flex flex-row justify-content-between align-items-end">
            <div class="d-flex flex-row flex-wrap" style="display:none!important;" ref="content">
                <template
                    v-for="(filter,index) in state.filter.options"
                    v-if="!['art_type','Tamanho'].includes(index)"
                >
                    <div class="d-flex flex-column col-md-3 col-sm-12 pl-0">
                        <b>{{index}}</b>
                        <el-select class="mr-2" v-model="state.filter.form[index]">
                            <el-option
                                label=" "
                                :value="false"
                                v-if="!['Quantidade','Acabamento Padrão'].includes(index)"
                            />
                            <el-option
                                v-for="(op,i) in state.filter.options[index]"
                                :label="op"
                                :value="op"
                                :key="i"
                            />
                        </el-select>
                    </div>
                </template>
                <template
                    v-for="(filter,index) in state.filter.options"
                    v-if="['Tamanho'].includes(index)"
                >
                    <div class="d-flex flex-column col-md-3 col-sm-12 pl-0" :key="index">
                        <b>{{index}}</b>
                        <el-select class="mr-2" v-model="state.filter.other[index]">
                            <el-option label=" " :value="false" />
                            <el-option
                                v-for="(op,i) in state.filter.options[index]"
                                :label="op"
                                :value="op"
                                :key="i"
                            />
                        </el-select>
                    </div>
                </template>
                <div class="d-flex flex-column col-md-3 col-sm-12 pl-0">
                    <b>Tipo Arte</b>
                    <el-select class="mr-2" v-model="state.filter.art_type">
                        <el-option
                            v-for="(op,i) in state.filter.options.art_type"
                            :label="op.label"
                            :value="op.value"
                            :key="i"
                        />
                    </el-select>
                </div>
            </div>
            <span class="text-secondary">Ordenado pelo menor preço</span>
        </div>
    </div>
</template>
<script>
export default {
    props: ["state"],
    data() {
        return {
            attempts: 0
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            this.attempts++
            this.$http.post("compra-rapida/fields", { product_id: this.state.product.id }).then(res => {
                let fields = res.data.fields
                let product = res.data.product
                this.$set(this.state, "product", product)
                for (let index in fields) {
                    if (index != "Tamanho") {
                        this.$set(this.state.filter.options, index, fields[index])
                        this.$set(this.state.filter.form, index, (['Quantidade', 'Acabamento Padrão'].includes(index)) ? fields[index][0] : false)
                    } else {
                        this.$set(this.state.filter.options, index, fields[index])
                        this.$set(this.state.filter.other, index, false)
                    }
                    $(this.$refs.content).show()
                }
                this.state.filter.loaded = true
            }).catch(er => {
                console.log(er)
                if (this.attempts > 3) return console.log("timeout")
                this.init()
            })
        }
    }
}
</script>