<style scoped>
.template-custom {
    opacity: 0.6;
}

.template-custom:hover {
    opacity: 1;
    transition: .5s;
}
</style>
<template>
<div class="row" id="gabarito-manual">
    <div class="col-md-12">
        <div class="card" v-if="!files.length">
            <div class="card-header"><b>GABARITO</b></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center">Este produto não possui gabarito</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-header"><b>GABARITO</b></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 text-left">
                        <p class="big mb-1"><b>Baixe o Gabarito</b></p>
                        <p class="small mb-1"><b>Escolha o Software</b></p>
                        <div class="pt-2 d-flex flex-row flex-wrap mb-0">
                            <template v-for="file in files">
                                <a :href="file.url" v-if="file.type == `pdf`" target="_blank">
                                    <img src="/svg/upload/pdf.svg" alt="Pdf" class="mr-2 mt-2 template-custom">
                                </a>
                                <a :href="file.url" v-if="file.type == `ai`" target="_blank">
                                    <img src="/svg/upload/illustrator.svg" alt="Adobe Illustrator" class="mr-2 mt-2 template-custom">
                                </a>
                                <a :href="file.url" v-if="file.type == `psd`" target="_blank">
                                    <img src="/svg/upload/photoshop.svg" alt="Adobe Photoshop" class="mr-2 mt-2 template-custom">
                                </a>
                                <a :href="file.url" v-if="file.type == `cdr`" target="_blank">
                                    <img src="/svg/upload/cdr.svg" alt="Arquivo CDR" class="mr-2 mt-2 template-custom">
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12" style="margin-top:20px">        
        <div class="card">
            <div class="card-header"><b>MANUAL DE ENVIO</b></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 text-left">
                        <p class="big mb-1"><b>Baixe o manual de envio em PDF</b></p>
                        <p class="small mb-1"><b>Escolha o Software</b></p>
                        <div class="pt-2 d-flex flex-row flex-wrap mb-0 manual-envio-icons">
                            <template v-for="file in files">
                                <a href="https://padraocolor.com.br/assets/images/manualdeenvio.pdf" target="_blank">
                                    <img src="/svg/upload/pdf.svg" alt="Pdf" class="mr-2 mt-2 template-custom">
                                </a>                                
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</template>

<script>
export default {
    props: ['files'],
    data() {
        return {}
    },
    methods: {

    }
}
</script>
