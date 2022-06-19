<template>
<div v-loading="loading" ref="content" style="display:none;">
    <span class="display-4 title d-flex mb-0 header-compras align-items-center p-2 pl-3 pr-3">
        <i class="material-icons mr-2">store_mall_directory</i>
        <span class="card-compras-title" >Compra</span>
        <span class="card-compras-code">#{{order.code}}</span>
       
    </span>
    <div class="custom tabs">
        <b-card-body class="custom-body">
            <div class="row" v-if="reason">
                <div class="col-12" >
                    <b class="ml-3">Motivo do Problema : </b> {{reason}}
                </div>
            </div>
            <send-art class="mt-3 mx-3" :order="order" :sku="sku" :componentInfo="componentInfo" v-model="files"/>
        </b-card-body>
        <b-card-footer class="custom-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 text-right">
                    <button class="btn-continue btn-sm-block float-right d-none d-lg-block" :disabled="!canConfirm" v-bind:class="{'shaking':canConfirm}" @click="confirm">Confirmar Reenvio de Arte</button>
                    <button class="btn-continue btn-block d-lg-none" :disabled="!canConfirm" v-bind:class="{'shaking':canConfirm}" @click="confirm">Confirmar Reenvio de Arte</button>
                </div>
            </div>
        </b-card-footer>
    </div>
</div>
</template>

<script>
export default {
    props: ["order","sku"],
    data() {
        return {
                loading: false,
                componentInfo : {
                    segment: null,
                    color: null,
                    hasCutAndCrease : false,
                    hasLocalizedVarnish : false
                },
                files : []
        }
    },
    mounted() {
        $( this.$refs.content ).slideToggle("fast");
    },
    computed: {
        reason() {
            return this.sku.data.status_reason
        },
        detailUrl() {
            return window.location.href = this.$root.root_url+"/compras/"+this.order.hashid+"/detail"
        },
        canConfirm() {
            return this.files.length > 0
        }
    },
    components: {
        'send-art': require('./-SendArt.vue').default
    },
    methods: {
        back() {
            return window.location.href = this.detailUrl
        },
        confirm() {
            this.$swal.confirm("Confirmação","Confirma envio da nova arte ?<br>A data de previsão de entrega será recalculada a partir da data de hoje.","warning",() => {
                let loading = this.$loading({
                    lock: true,
                    text: "Aguarde, Reenviando nova(s) arte(s) ...",
                    background: 'rgba(0, 0, 0, 0.7)'
                })            
                this.$http.post("resendart/submit",this.files).then( res => {
                    res = res.data
                    return window.location.href = res.data.route
                }).catch( er => {
                    loading.close()
                    console.log(er)
                })
            })
        }
    }
}
</script>


<style>
.custom-body {
    border: 1px solid #eceaea;
}
.header-compras {
    font-size: 20px;
    padding: 20px;
    background-color: rgb(242, 242, 242);
    height:60px;
}

.card-header-compras {
    padding-top: 0px;
    padding-left: 9px;
    padding-right: 9px;
    font-size:18px;
}

.card-compras-title {
    font-weight: 600;
    border-right: 1px solid black;
    padding-right: 20px;
    font-size:18px;
}
.card-compras-shipping
{
    font-weight: 600;
    padding-right: 20px;
    font-size:18px;
}
.card-compras-code
{
    font-weight: 600;
    padding-left: 20px;
}
.tab-content-custom {
    padding: 24px;
}

.card-footer {
    background-color: white;
}

.pagination {
    border: 1px solid #1B4E01;
}

.page-link {
    color: #1B4E01 !important;
}
.btn.btn-print
{
    background-color : #007FC4;
    color:white;
}
.custom-status-icon.pending
{
    color:#FBB254;
    font-size:19px;
}
.custom-status-icon.forwarded
{
    color:#99D468;
}
.custom-status-icon.delivered
{
    color:#99D468;
}
.custom-status-icon.canceled
{
    color:#ED5564;
}
.custom-status-icon.approved
{
    color:#99D468;
}
.custom-status-icon.production
{
    color:blue;
}
.custom-status-icon.finished
{
    color:#0fe7ff;
}
</style>