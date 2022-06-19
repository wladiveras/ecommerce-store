<template>
    <div class="d-flex flex-row align-items-center">
        <span style="display:none">{{value}}</span>
        <input :name="name" style="display:none" :id="name" v-model="value">
        <el-rate
            :class="size"
            :disabled="disabled"
            v-model="value"
            :colors="['#99A9BF', '#F7BA2A', '#FF9900']">
        </el-rate>
        <slot></slot>
    </div>

</template>

<script>
export default {
    props: ["val","readonly","name","center","size"],
    data() {
        return {
            value: Number(this.val),
        }
    },
    watch : {
        value(val) {
            return this.$emit('input',val)
        },
        val(val) {
            this.value = val
            return this.$emit('input',val)
        }
    },
    computed:
    {
        disabled()
        {
            return (this.readonly!=undefined);
        }
    },
    methods: {
    }
}
</script>
<style lang="scss">
    .el-rate {
        &.medium {
            .el-rate__item {
                .el-rate__icon {
                    font-size : 30px;
                }
            }
        }
        &.large {
            .el-rate__item {
                .el-rate__icon {
                    font-size : 60px;
                }
            }
        }
        &.small {
            .el-rate__item {
                .el-rate__icon {
                    font-size : 10px;
                }
            }
        }
    }
</style>