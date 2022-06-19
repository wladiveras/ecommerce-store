<template>
    <div id="section_info">
        <div class="description-row mb-3">
            <!-- <div class="row">
                <div class="col-12">
                    <h4 class="section_title pb-1">
                        <b>Informações</b> do produto
                    </h4>
                </div>
            </div> -->
            <div class="row" v-if="product.description">
                <div class="col-12 mt-5">
                    <showMoreComponent :html="product.description" backTo="#section_info" />
                </div>
            </div>
        </div>
        <div class="row">
            <div v-if="hasEmbed" class="col-md-4 col-sm-12 mb-4">
                <div class="embed-responsive embed-responsive-16by9">
                    <object :data="product.embed_video" class="embed-responsive-item" />
                </div>
            </div>
            <div v-bind:class="{'col-md-8 col-sm-12' : hasEmbed, 'col-12' : !hasEmbed}">                
                <div class="table_info mb-3 table-responsive">
                    <table class="table w-100" style="max-width:500px;">
                        <tbody>
                            <!-- <tr>
                                <td style="width:120px">
                                    <b>Departamento</b>
                                </td>
                                <td>{{table_department}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Categorias</b>
                                </td>
                                <td>{{categories}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Tipo</b>
                                </td>
                                <td>
                                    <span v-if="product.type.includes('UP')">Criação de Arte</span>
                                    |
                                    <span v-if="product.type.includes('CA')">Envio de Arte</span>
                                </td>
                            </tr> -->
                            <tr v-if="templates">
                                <td>
                                    <b>Gabaritos</b>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between" v-html="table_templates"></div>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['product', 'templates'],
    components: {
        showMoreComponent: require("./-showMore.vue").default
    },
    computed: {
        hasEmbed() {
            if (this.product.embed_video && this.product.embed_video != 'null') return true
            return false
        },
        table_department() {
            let dep = this.product.department
            if (dep == "impressao digital") return "Impressão Digital"
            if (dep == "impressao offset") return "Impressão Offset"
            if (dep == "comunicacao visual") return "Comunicação Visual"
            return dep
        },
        table_templates() {
            let files = this.templates
            let text = ""
            for (let i in files) {
                if (files[i].type == "psd") {
                    text += `<a target="_BLANK" class="link" href="${files[i].url}"><img class="icon-gabarito" src="/assets/images/icon-psd.png" /></a>`
                }
                if (files[i].type == "ai") {
                    text += `<a target="_BLANK" class="link" href="${files[i].url}"><img class="icon-gabarito" src="/assets/images/icon-ai.png" /></a>`
                }
                if (files[i].type == "pdf") {
                    text += `<a target="_BLANK" class="link" href="${files[i].url}"><img class="icon-gabarito" src="/assets/images/icon-pdf.png" /></a>`
                }                
                if (files[i].type == "cdr") {
                    text += `<a target="_BLANK" class="link" href="${files[i].url}"><img class="icon-gabarito" src="/assets/images/icon-cdr.png" /></a>`
                }
                // text += `<p class="m-0"><a target="_BLANK" class="link" href="${files[i].url}">Opção em ${files[i].type}</a></p>`
            }
            return text
        },
        categories() {
            return _.map(this.product.categoriesList, x => x.name).join(" / ")
        }
    }
}
</script>
