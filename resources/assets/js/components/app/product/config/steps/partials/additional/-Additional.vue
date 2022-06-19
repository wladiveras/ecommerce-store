<template>
    <div>
        <card-items :shared="shared" :config="config" />
    </div>
</template>

<script>
export default {
    props: ["shared", "data", "config"],
    data() {
        return {}
    },
    components: {
        "card-items": require("./-Items.vue").default,
    }
};
</script>
