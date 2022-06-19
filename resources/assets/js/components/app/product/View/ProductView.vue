<template>
    <div class="product-view">
        <principal-section :product="product" :user="user" :product_buy="product.customizable">
            <slot name="title" slot="title" />            
            <slot name="categories" slot="categories" />
            <slot name="short_description" slot="short_description" />
        </principal-section>
        <info-section :product="product" :templates="templates" />
        <!-- <avaliations-section :product="product" :user="user" />
        <faq-section :product="product" :user="user" /> -->
    </div>
</template>
<script>
export default {
    props: ["product", "templates", "user"],
    components: {
        "faq-section": require("./partials/-faq.vue").default,
        "principal-section": require("./partials/-principal.vue").default,
        "info-section": require("./partials/-info.vue").default,
        "avaliations-section": require("./partials/-avaliations.vue").default
    }
}
</script>

<style lang="scss">
.table_info {
    td {
        border-top: none;
    }    
    .icon-gabarito {
        width:50px;
    }
    @media screen and (max-width: 767px) {
        td {
            padding: 0.5rem;
        }
        .icon-gabarito {
            width:30px;
        }
    }

}
.product-view {
    .categories {
        font-size: 12px;
        color: #4c4f4b;
        margin-bottom: 0;
        text-transform: uppercase;
    }
    .product_name {
        color: #313030;
        font-size: 45px;
        font-weight: 900;
        margin-bottom: 0px;
    }
    .short_description {
        font-size: 15px;
        margin-top: 20px;
        p {
            margin-bottom: 0px !important;
        }
    }
    .el-carousel__container {
        border: 1px solid #e0e0e0;
    }
    .title_row {
        position: relative;
        .categories {
            top: -5px;
            font-weight: 700;
        }
    }
    .btn {
        &.config {
            padding: 10px;
            &.advanced {
                background-color: transparent;
                border: 1px solid #1b4e01;
                color: #1b4e01;
            }
        }
    }
    .price_row {
        position: relative;
        .rule {
            font-size: 16px;
            letter-spacing: 0px;
            color: gray;
        }
        .from_for {
            color: #949494;
            top: -27px;
            font-weight: 500;
            text-decoration-line: line-through;
            position: absolute;
            color: red;
        }
        .beginning {
            color: #d88316;
            top: -13px;
            font-weight: 500;
            margin-top: 0;
        }
        .price {
            color: #313030;
            font-size: 35px;
            font-weight: 900;
            letter-spacing: -1px;
            &.sm {
                font-size: 30px;
                letter-spacing: -2px;
            }
        }
    }
    .section_title {
        margin-top: 50px;
        font-size: 30px;
        color: #ff963a;
        letter-spacing: -1px;
        border-bottom: 1px solid #dedede;
        b {
            font-weight: bolder;
        }
    }
}

</style>