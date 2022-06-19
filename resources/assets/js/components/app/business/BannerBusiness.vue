<template>
    <div class="product-view">
        <principal-section :business="business" >

        </principal-section>
        
    </div>
</template>
<script>
export default {
    props: ["business"],
    components: {
        
        "principal-section": require("./partials/-principal.vue").default
        
    }
}
</script>

<style lang="scss">
.product-view {
    .el-carousel {
        .el-carousel__container {
        border: none;
        height: 350px;
        }
        .img-wrapper {
            max-width: 100% !important;
        }
    }    
}
</style>