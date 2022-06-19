<template>
    <div>
        <div class="row" style="background: url(/assets/images/bg-descr-product.png) no-repeat;background-position: top right;">
            <div v-html="this._html" ref="content" id="content" class="col-md-7" />
        </div>
        <div class="mt-4 mb-5 d-flex align-items-center justify-content-center" v-if="showBtnMore">
            <a href="#" @click.prevent="show()" class="d-flex flex-column align-items-center">
                <h2 v-bind:class="{'el-icon-arrow-down' : !clicked, 'el-icon-arrow-up' :clicked}" />
                <span>Ver {{ clicked ? "Menos" : "Mais"}}</span>
            </a>
        </div>
    </div>
</template>
<script>
export default {
    props: ["html", "backTo"],
    data() {
        return {
            contentH: 0,
            clicked: false,
            limit: 2000
        }
    },
    computed: {
        showBtnMore() {
            return this.html.length > this.limit
        },
        _html() {
            if (this.showBtnMore) {
                if (!this.clicked) {
                    const text = this.html
                    return (text.substring(0, (text + ' ').lastIndexOf(' ', this.limit)))
                }
            }
            return this.html
        }
    },
    methods: {
        async show() {
            this.clicked = !this.clicked
            if (this.backTo) this.$scrollTo(this.backTo)
        }
    }
}
</script>