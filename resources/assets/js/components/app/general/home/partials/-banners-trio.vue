<template>
    <div class="banner-trio d-none d-lg-block">
        <div class="container">
            <div class="d-flex flex-row flex-wrap d-flex justify-content-center">
                <div
                    class="col card-color text-center mx-2"
                    :style="{backgroundColor:trio[0].background_color}"
                >
                    <a target="_blank" v-if="trio[0]" :href="trio[0].route">
                        <img
                            class="trio-item"
                            :src="trio[0].url"
                            width="auto"
                            alt="banner home"
                            height="100%"
                        />
                    </a>
                </div>
                <div
                    class="col card-color text-center mx-2"
                    :style="{backgroundColor:trio[1].background_color}"
                >
                    <a v-if="trio[1]" target="_blank" :href="trio[1].route">
                        <img
                            class="trio-item"
                            :src="trio[1].url"
                            alt="banner home"
                            width="auto"
                            height="100%"
                        />
                    </a>
                </div>
                <div
                    class="col card-color text-center mx-2"
                    :style="{backgroundColor:trio[2].background_color}"
                >
                    <a v-if="trio[2]" target="_blank" :href="trio[2].route">
                        <img
                            class="trio-item"
                            :src="trio[2].url"
                            width="auto"
                            alt="banner home"
                            height="100%"
                        />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['trio']
}
</script>