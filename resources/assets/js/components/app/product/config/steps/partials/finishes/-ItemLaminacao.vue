<template>
<div class="h-100">
    <label @click.prevent="$emit('input',val)" class="w-100">
        <div class="card h-100" v-bind:class="{'selected' : selected==val}">
            <div class="card-body">
                <div class="custom_radio d-flex align-items-center">
                    <i v-if="selected==val" class="material-icons icon checked">check_circle</i>
                    <i v-else class="material-icons icon unchecked">radio_button_unchecked</i> 
                    <p class="m-0">
                        <span class="ml-2">
                            {{type.capitalize()}} <b>{{option.type_desc}}</b>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </label>
</div>
</template>
<script>
export default {
    props: ["option","val","selected","type"],
}
</script>
<style scoped lang="scss">
    .card {
        &.selected {
            border: 1px solid #1B4E01;
            box-shadow: 0px 0px 23px -16px #1b4e01;
        }
    }
</style>