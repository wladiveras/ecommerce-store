<style scoped>
.label_slider {
    position: absolute;
    top: 7px;
    left: 22px;
}
.input_slider {
    padding-left: 40px;
    border:none;
}
</style>
<template>
<div>
    <label class="m-0"><b class="mr-2 label_slider">{{row.label}} : </b> <input type="number" class="form-control input_slider" v-model="qty"  :min="0" :max="1000" /></label>
    <el-slider v-if="sliderQty!=undefined" v-model="sliderQty" :min="0" :max="1000"  style="width:100%"></el-slider>
</div>
</template>

<script>
export default {
    props: ['row'],
    data() {
        return {
            qty : 0,
            sliderQty : row.qty
        }
    },
    mounted()
    {
        
    },
    methods: {
    }
}
</script>
