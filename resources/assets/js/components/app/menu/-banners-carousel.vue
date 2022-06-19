<template>
    <div class="d-none d-lg-block banners-carousel">
        <div>asdasd</div>
        <div
            class="row d-flex flex-column px-0 carousel-primary"
            v-if="banners.length>0"
            style="max-width: 1140px;margin: 0 auto 0;"
        >
            <el-carousel indicator-position="outside" :interval="5000">
                <template v-for="(b,i) in banners">
                    <a :href="b.route" target="_BLANK" :key="i" class="d-block h-100">
                        <el-carousel-item :key="i">
                            <div class="banner">
                                <div
                                    class="image-banner"
                                    :style="{backgroundColor: b.background_color ? b.background_color : '#fefefe' }"
                                >
                                    <div class="container h-100">
                                        <div class="row h-100">
                                            <div class="col-12 h-100 carousel-image"
                                                 :style="{backgroundImage: `url(${b.url})`}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </el-carousel-item>
                    </a>
                </template>
            </el-carousel>
        </div>
    </div>
</template>
<script>
export default {
    props: ['banners'],
}
</script>
