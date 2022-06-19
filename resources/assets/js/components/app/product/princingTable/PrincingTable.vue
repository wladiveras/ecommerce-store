<template>
    <div
        class="row"
        v-loading="state.loading"
        element-loading-text="Carregando opções e variações..."
    >
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column">
                    <filter-row :state="state" />
                    <product-table :state="state" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["productid"],
    data() {
        return {
            state: {
                product: {
                    id: this.productid ? this.productid : null,
                },
                show_skuid: true,
                loading: true,
                filter: {
                    art_type: "UP",
                    other: {},
                    form: {},
                    options: {
                        art_type: [
                            { value: "UP", label: "Envio de Arte" },
                            { value: "CA", label: "Criação de Arte" },
                        ]
                    },
                    loaded: false
                }
            }
        }
    },
    components: {
        "filter-row": require("./partials/-Filter.vue").default,
        "product-table": require("./partials/-ProductTable.vue").default,
    }
}
</script>