<template>
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-12 col-sm-12">
            <el-carousel indicator-position="outside" arrow="always" :interval="5000">
                <el-carousel-item v-for="(file,i) in business.files" :key="i">
                    
                    <div v-for="(value, name) in file">
                        <img :src="value.raw_url" alt="Padrão Color" style="max-width:100%;">
                    </div>                    
                    
                </el-carousel-item>
            </el-carousel>
        </div>
        
    </div>
</template>
<script>
export default {
    props: ['business']
}
</script>
