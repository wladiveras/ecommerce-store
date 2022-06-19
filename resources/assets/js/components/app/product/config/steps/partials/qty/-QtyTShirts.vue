<template>
<div v-loading="loading">
    <div class="row d-flex align-items-center">
        <div class="col-md-4">
           <ul class="size-list">
                <a href="#" v-for="(row,index) in values" @click.prevent="plus(row.index)">
                    <el-tooltip class="item" effect="dark" :content="'Adicionar a '+row.label" placement="top-start">
                        <li class="shirt mb-1"><span class="text-center" >{{row.label}}</span></li>
                    </el-tooltip>
                </a>
           </ul>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-12 text-center mb-2">
                    <h4>Total : <b>{{qty}}</b> Unidade<span v-if="qty>1">s</span> </h4>
                </div>
                <div class="col-md-3" v-for="(row,index) in values">
                    <label class="m-0"><b class="mr-2">{{row.label}} : </b></label>
                    <el-input-number class="mb-3" v-model="row.qty"  show-input :min="0" :max="999" @change="change" style="width:100%;" controls-position="right"></el-input-number>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ['shared'],
    data() {
        return {
            loading : false,
            values : {},
            qty : 0,
            change_interval : null,
            timewaiting : 1,
        }
    },
    watch : {
        loading(val) {
            this.$parent.ready = false;
            if(this.shared.data.skus != null) {
                this.$parent.ready = true
            }
        }
    },
    mounted()
    {
        this.loading = true
        this.load()
        this.change()
        this.loading = false
    },
    methods: {
        submitQty() {
            let data = [];
            for(let i in this.values) {
                if(this.values[i].qty > 0) {
                    data.push(this.values[i]);
                }
            }
            this.setSkus(data);
        },
        setSkus(data) {
            this.shared.data.sizes = data;
        },
        change() {
            let qty = 0;
            for(let i in this.values) {
                if(this.values[i].qty > 0) {
                    qty += this.values[i].qty;
                }
            }
            this.qty = qty;
            clearInterval(this.change_interval);
            let time = 0;
            this.change_interval = setInterval(function() {
                if(time++==(this.timewaiting-1)) {
                    clearInterval(this.change_interval);
                    this.confirm();
                }
            }.bind(this),1000);
        },
        confirm() {
            this.loading = true;
            this.shared.data.skus = null;
            this.$http.post(this.$root.root_url + "/api/produto_config/calc_qty_tshirts", {
                ids: this.shared.data.sku_ids,
                qty : this.qty
            }).then((response) => {
                response = response.data;
                if (!response.success) {
                    this.loading = false;
                    return this.$toastr.error(response.message);
                }
                this.shared.data.skus = response.data;
                this.submitQty();
                this.loading = false;
            });
        },
        reset() {
            this.shared.data.skus = null;
            this.shared.data.sizes = null;
        },
        load() {
            this.loading = true
            let sizes = this.shared.step.product.sizes,
            index = 0
            for(let i in sizes) {
                let qty = 0
                if(this.shared.isPreset && this.shared.presetSizes){
                    let sharedItem = this.shared.presetSizes[index]
                    
                    if(sharedItem && sizes[i].size == sharedItem.index){
                        qty = sharedItem.qty
                        index++
                    }
                }
                this.$set(this.values, i ,{index: sizes[i].size ,  qty, label : sizes[i].desc, ref:"table" } )
            }
            this.loading = false
        },
        plus(index) {
            let aux = this.values[index];
            aux.qty ++;
            this.$set(this.values,index,aux);
            this.change();
        }
    }
}
</script>


<style lang="scss" scoped>
.size-list {
    list-style: none;
    li {
        float : left;
        position: relative;
        margin-right: 5px;
    }
    .shirt {
        cursor : pointer;
        padding: 0 7px;
        text-align: center;
        font-size: 20px;
        line-height: 26px;
        -webkit-border-radius: 20px;
        border-radius: 20px;
        text-decoration: none;
        background-color : #d6ef63;
        color: #1B4E01;
        border: 1px solid #ccc;
        outline: 1px solid transparent;
        width: 80px;
        height: 80px;
        font-size: 10px;
        margin: 20px auto;
        padding: 12px;
        -webkit-clip-path: polygon(68% 5%, 32% 5%, 0 24%, 9% 44%, 25% 36%, 25% 90%, 75% 90%, 75% 36%, 91% 44%, 100% 24%);
        clip-path: polygon(68% 5%, 32% 5%, 0 24%, 9% 44%, 25% 36%, 25% 90%, 75% 90%, 75% 36%, 91% 44%, 100% 24%);
    }
    .shirt:hover {
        background-color : #1B4E01;
        border: 1px solid #1B4E01;
        color : #d6ef63;
        transition : .5s;
    }
}
.btn_delete {
    background-color: red;
    color: white;
    border-radius: 100%;
    padding-right: 5px;
    padding-left: 5px;
    position: absolute;
    right: 16px;
    opacity: .5;
}
.btn_delete:hover {
    opacity: 1;
    transition : .5s;
}
</style>