<template>
<div>
    {{item.data.sku.product.name}}
    <span class="badge badge-secondary mr-2" style="background-color:black;font-size:12px;">{{item.data.config_info.ref_name}}</span>
    <div v-b-toggle="`${index}`">
        <a  href="#" @click.prevent="" class="when-opened link float-right">Ver menos</a>
        <a  href="#" @click.prevent="" class="when-closed link float-right">Ver mais</a>
    </div>
    <b-collapse :id="index.toString()" class="float-right">
        <template v-for="(variation, i) in item.data.sku.product.variations">
            <p class="f-12 mb-0" v-if="variation != 'Quantidade' && item.data.sku.attributes[i]"><b>{{variation}} : </b>{{item.data.sku.attributes[i]}}</p>
        </template>
        <template v-if="item.data.extra">
            <div class="mt-2" />
            <template v-if="item.data.config_info.extra.cover">
                <p class="f-12 mb-1"><b>Capa : </b>{{item.data.config_info.extra.cover}} g/m²</p>
            </template>
        </template>
        <template v-if="item.data.config_info.measures">
            <div class="mt-2" />
            <p class="f-12 m-0" v-if="(item.data.config_info.measures.width&&item.data.config_info.measures.height)"><b>Medidas : </b>{{((item.data.config_info.measures.width)*(item.data.config_info.measures.height)).toFixed(2)}}{{item.data.config_info.measures.unit}}² ({{(item.data.config_info.measures.height).toFixed(2)}}{{item.data.config_info.measures.unit}} x {{(item.data.config_info.measures.width).toFixed(2)}}{{item.data.config_info.measures.unit}})</p>
        </template>
        <template v-if="item.data.config_info.sizes">
            <div class="mt-2" />
            <template v-for="size in item.data.config_info.sizes">
                <p class="f-12 m-0"><b>Tamanho {{size.label}} : </b> {{size.qty}} unidade<span v-if="size.qty > 1">s</span></p>
            </template>
        </template>
        <div v-if="item.data.config_info.additional.additional_attributes">
            <div class="mt-2" />
            <template v-for="add in item.data.config_info.additional.additional_attributes">
                <p class="f-12 m-0"><b>{{add.name}} : </b> <span v-if="add.price>0">{{add.price.currency()}}</span><span v-else>Grátis</span></p>
            </template>
        </div>
        <div v-if="item.data.config_info.upload.timing == 'now'">
            <div class="mt-2" />
            <p class="f-12 m-0" v-for="file in item.data.config_info.upload.file">
                <b>{{file.label}} </b> <span v-if="item.data.config_info.upload.send_date"> : ({{format_date_time(item.data.config_info.upload.send_date)}})</span> <a target="_blank" class="link" :href="file.file.raw_url">  Baixar o arquivo enviado</a>
            </p>
        </div>
        <div v-if="item.data.config_info.finishes.finishes">
            <div class="mt-2" />
            <template v-for="fin in item.data.config_info.finishes.finishes">
                <p class="f-12 m-0"><b>{{fin.name}} : </b> <span v-if="fin.price>0">{{fin.price.currency()}}</span><span v-else>Grátis</span></p>
                <template v-if="fin.name == 'Corte e Vinco' && fin.qty>0">
                    <p class="f-12 m-0"><b>Faca : </b> R$ 150,00 ( 1 unidade )</p>
                </template>
            </template>
        </div>
    </b-collapse>
</div>
</template>
<script>
export default {
    props : ["item","index"],
    methods : {
        format_date_time(d) {
            let year  = d.substring(0,4)
            let month = d.substring(5,7)
            let day   = d.substring(8,10)
            let time  = d.substring(11,16)
            return `${day}/${month}/${year} ${time}`
        },
    }
    
}
</script>