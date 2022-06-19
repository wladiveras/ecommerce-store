<template>
    <div class="banner-mobile d-lg-none d-block overflow-hidden">
        <div class="row d-flex justify-content-center">
            <div class="col-6 p-0" v-for="(mobile,i) in mobiles" :key="i">
                <div
                    class="card-color text-center"
                    :style="{backgroundColor:mobile.background_color}"
                >
                    <a target="_blank" v-if="mobile" :href="mobile.route" >

                        <img
                            class="mobile-item"
                            :src="mobile.url"
                            alt="banner home"
                        />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['mobiles']
}
</script>
