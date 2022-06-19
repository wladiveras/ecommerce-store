<style scoped>
.menu-default .tab-pane .item:hover {
    background-color: #d6ef63;
}

.menu-default .tab-pane .row .item {
    height: 138px;
}

.menu-default .tab-pane .row-1 .item:hover {
    -webkit-box-shadow: inset 0px 10px 16px -12px rgba(0, 0, 0, 0.75);
    -moz-box-shadow: inset 0px 10px 16px -12px rgba(0, 0, 0, 0.75);
    box-shadow: inset 0px 10px 16px -12px rgba(0, 0, 0, 0.75);
}

.menu-default .tab-pane .item .icon {
    width: 30px;
}

.menu-default .tab-pane .item .title {
    font-size: 16px;
    font-weight: 600;
}
</style>
<template>
    <div>
        <template v-for="(row,index) in rows">
            <div class="d-flex flex-row" v-bind:class="{ 'row-1': (index==0) }" :key="index">
                <template v-for="(item,i) in row">
                    <div class="p-4 col-3 item" @click="go(item.url)" :key="i">
                        <div class="row">
                            <div class="col text-left icon">
                                <img class="mb-2" :src="item.icon" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span class="title">{{item.text}}</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: ["items"],
    data() {
        return {
            items_per_row: 4
        }
    },
    computed:
    {
        rows() {
            let array = []
            let count = 0
            let index = 0
            if (!this.items)
                return []
            for (let row in this.items) {
                if (index >= this.items_per_row) {
                    count++
                    index = 0
                }
                if (!array[count]) {
                    array[count] = []
                }
                array[count].push(this.items[row])
                index++
            }
            return array
        }
    },
    methods:
    {
        go(url) {
            window.location.href = this.$root.root_url + "/" + url
        }
    }
}
</script>
