<template>
    <div class="img-wrapper">
        <div class="ratio-fill">
            <div class="img-container d-flex justify-content-center" ref="media">
                <canvas
                    ref="canvas"
                    class="comp-img-canvas"
                    style="width:100%"
                    :width="model.width"
                    :height="model.height"
                ></canvas>
                <img crossorigin="Anonymous" ref="image" :class="imgclass" :alt="alt" />
                <img
                    crossorigin="Anonymous"
                    :alt="alt"
                    @load="loadThumb"
                    ref="thumb"
                    class="img-thumb"
                    :src="thumbUrl"
                />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        imgclass: {
            type: String, default: "real-image"
        },
        url: {
            default: "/assets/images/placeholder-thumbnail.png"
        },
        thumb: Boolean,
        height: {
            default: 140,
        },
        width: {
            default: 140
        },
        gravity: {
            default: 'center'
        },
        alt: {
            type: String, default: "image"
        },
        model: { default: () => ({}) },
        mode: { default: 'normal' }
    },
    data() {
        return {
            component: '',
            image: '',
            canvas: '',
            thumbnail: '',
            context: '',
            //config
            blurDelay: 1000,
            blurRadius: 10,
            wid: this.width,
            hei: this.height,
            triggered: false
        }
    },
    created() {
        if (this.model && this.mode == 'full') {
            this.hei = this.model.height
            this.wid = this.model.width
        }
    },
    mounted() {
        this.setVars()
        this.prepareIntersect()
    },
    methods: {
        prepareIntersect() {
            let obs = new IntersectionObserver((e) => {
                if (e[0].intersectionRatio > 0) {
                    obs.unobserve(this.component)
                    this.loadRealImage()
                }
            })
            obs.observe(this.component)
        },
        setVars() {
            this.component = this.$refs.media
            this.image = this.$refs.image
            this.canvas = this.$refs.canvas
            this.thumbnail = this.$refs.thumb
            this.context = this.canvas.getContext('2d')
        },
        loadThumb() {
            this.drawThumbnail(this.blurRadius)
        },
        loadRealImage() {
            this.image.src = this.realUrl
            this.image.onload = e => this.component.classList.add('img-loaded')
        },
        drawThumbnail(blur) {
            let
                thmb = this.thumbnail, thmbW = thmb.width, thmbH = thmb.height,
                cnv = this.canvas, cnvW = cnv.width, cnvH = cnv.height

            this.context.drawImage(thmb, 0, 0, thmbW, thmbH, 0, 0, cnvW, cnvH)
        },
        getAspectRatio(w, h, expected) {
            let res = {
                width: expected,
                height: expected
            }
            if (w < h) {
                res.width = (w / h * expected)
            } else if (h < w) {
                res.height = (h / w * expected)
            }

            res.width = res.width.toFixed(0)
            res.height = res.height.toFixed(0)
            return res
        }
    },
    computed: {
        src() {
            return this.url
        },
        thumbUrl() {
            let ratio = { width: 42, height: 42 }
            if (!this.thumb) {
                ratio = this.getAspectRatio(Number(this.model.width), Number(this.model.height), 42)
            }
            return `${this.src}?width=${ratio.width}&height=${ratio.height}&gravity=${this.gravity}&blur=1`
        },
        realUrl() {
            let additional = `?height=${this.hei}&width=${this.wid}&gravity=center`
            return this.src + additional
        }
    }
}
</script>
<style lang="scss" scoped>
.img-wrapper {
    margin: 0 auto;
    width: 100%;
    height: 100%;
    &:not(.banner) {
        max-width: 900px;
    }
    * {
        width: auto;
        height: 100%;
    }
    &.img-rounded {
        img,
        canvas {
            border-radius: 100%;
        }
    }
}
.banner {
    &.secondary-banner {
        .img-container {
            max-height: 330px;
        }
    }
    .img-container {
        max-height: 650px;
        &.img-loaded {
        }
        overflow: hidden;
        height: auto;
        width: auto;
        position: relative;
    }
    .real-image {
        position: relative;
    }
}

.ratio-fill {
    position: relative;
    display: block;
}

.img-container {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    overflow: hidden;
    &.img-loaded {
        & > .real-image {
            opacity: 1;
            width: 100%;
            height: auto !important;
        }
        & > .img-thumb {
        }
    }
}

.img-thumb {
    display: none;
}

.real-image,
.comp-img-canvas {
    border-radius: 3px;
    display: block;
    position: absolute;
    top: 0;
    left: 0;
}

.real-image {
    opacity: 0;
    transition: opacity 400ms 0ms;
    z-index: 1;
}

.img-container.img-loaded .comp-img-canvas {
    visibility: hidden;
    opacity: 0;
    transition: visibility 0s linear 0.5s, opacity 0.1s 0.4s;
}
.fit {
    .img-container {
        display: flex;
        justify-content: center;
        align-items: center;
        .real-image {
            position: relative;
            width: unset;
        }
    }
}
</style>
