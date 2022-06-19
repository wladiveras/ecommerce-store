<template>
    <div>
        <div v-loading="loading" v-if="!shared.data.upload.file">
            <object id="iframecontent" :data="route" ref="iframe" />
        </div>
        <div v-else>
            <a href="#" class="link" @click.prevent="backToEditor">Criar a arte novamente</a>
            <div class="d-flex flex-wrap flex-row align-items-center justify-content-center">
                <template v-for="(image, i) in shared.data.upload.file">
                    <div class="col-md-3 col-sm-12 d-flex flex-column" :key="i">
                        <img
                            style="max-height: 100%;max-width: 100%;border: 1px solid lightgrey;"
                            :src="image.file.raw_url"
                        />
                        <div class="text-center">{{image.label}}</div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["shared"],
    data() {
        return {
            loading: true,
            _loading: null
        }
    },
    computed: {
        route() {
            let mode = "_art_"
            let product_id = this.shared.data.product_id
            let sku_id = this.shared.data.sku.id
            let url = `${this.shared.step.web2print_route}/admin/canvas/compact/${product_id}?mode=${mode}&sku_id=${sku_id}&api_token=${this.shared.step.w2p_token}`
            return url
        }
    },
    created() {
        window.addEventListener('message', event => {
            if (event.data.type == "art_loading") {
                this._loading = this.$loading({
                    lock: true,
                    text: event.data.message,
                    spinner: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.7)'
                })
            }
        })
        window.addEventListener('message', event => {
            if (event.data.type == "art_created") {
                let created_art = event.data.data
                this.shared.data.upload.file = created_art
                this.$parent.ready = true
                this._loading.close()
                this.$toastr.success("Arte criada e enviada com sucesso, clique em continuar ...")
                this.$scrollTo("#step7")
            }
        })
    },
    mounted() {
        this.setOnLoad()
    },
    methods: {
        backToEditor() {
            this.shared.data.upload.file = null
            this.$parent.ready = false
            this.setOnLoad()
        },
        setOnLoad() {
            setTimeout(() => {
                if (this.$refs.iframe) {
                    this.$refs.iframe.onload = () => {
                        setTimeout(() => {
                            this.loading = false
                        }, 500)
                    }
                }
            }, 500)
        }
    }
}
</script>
<style scoped>
#iframecontent {
    width: 100%;
    overflow: auto;
    white-space: nowrap;
    border: unset;
    height: 640px;
}
</style>