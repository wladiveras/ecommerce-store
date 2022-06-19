<template>
    <div class="d-lg-none d-block banners-carousel">
        <div
            class="row d-flex flex-column w-100 mx-0 px-0 carousel-primary"
            v-if="banners.length>0"
        >
            <el-carousel indicator-position="outside" :interval="5000">
                <template v-for="(b,i) in banners">

                    <component :is="b.route && b.route.trim ? 'a' : 'div'" :href="b.route" target="_BLANK" :key="i">
                        <el-carousel-item :key="i">
                            <div class="banner">
                                <div
                                    class="image-banner"
                                    :style="{backgroundColor: b.background_color ? b.background_color : '#fefefe' }"
                                >
                                    <div class="w-100 h-100 carousel-image"
                                         :style="{backgroundImage: `url(${b.url})`}">
                                    </div>
                                </div>
                            </div>
                        </el-carousel-item>
                    </component>

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
