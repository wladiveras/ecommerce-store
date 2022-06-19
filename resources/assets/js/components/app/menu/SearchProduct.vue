<template>
    <form
        class="col align-items-center h-100 pt-0 d-flex"
        @submit.prevent="findProduct"
        @keydown="changed"
    >
        
        <el-input            
            suffix-icon="el-icon-search text-gray-search"
            placeholder="O que você procura?"
            :autofocus="false"
            v-model="lookingFor"
        />
    </form>
</template>
<script>
export default {
    data() {
        return {
            lookingFor: "",
            timeout: null,
            loading: false,
        }
    },
    created() {
        let param = window.location.search.replace("?", "").split("=")
        if (param.length == 2) {
            if (param[1]) {
                this.lookingFor = decodeURIComponent(param[1])
            }
        }
    },
    mounted() {
        this.loaded = true
    },
    methods: {
        changed() {
            clearTimeout(this.timeout)
            this.timeout = setTimeout(() => { this.findProduct() }, 1000)
        },
        findProduct() {
            this.loading = true
            window.location.href = `${window.location.origin}/produtos?procurando=${this.lookingFor}`
        }
    }
}
</script>
<style lang="scss">
.el-input__inner {
        background: transparent;
        border-color: #fff;
        color: #fff;
    }
.radius-b {
    .el-input__inner {
        border-radius: 14px;
    }
    .text-gray-search {
        color: #c7c7c7;
        font-size: 20px;
    }
}
.text-yellow {
    padding-left: 20px;
    font-size: 10px;
    font-weight: 400;
    strong {
        color: yellow;
    }
}
</style>