<template>
    <div>
        <div class="timeline d-flex align-items-center flex-column">
            <template v-if="steps=='canceled'">
                <h1 style="color:#63636382;">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="material-icons mr-1 custom-status-icon canceled" style="font-size:40px!important;">lens</i>Pedido Cancelado
                    </div>
                </h1>
            </template>
            <template v-else>
                <div role="alert" class="alert alert-info text-white mb-2 w-60">
                    <div class="d-flex align-items-center alert-dismissible">
                        <i class="material-icons mr-2">info</i>O status de compra abaixo se refere ao conjunto de pedidos, cada pedido possui um status individual
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="events">
                    <ol>
                        <ul>
                            <li v-bind:class="{'with-content' : steps.waiting.status}">
                                <span class="title lined" :class="steps.waiting.status">Aguardando</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.waiting.status}">{{ steps.waiting.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.waiting.status}">{{ steps.waiting.time }}</p>
                                </span>
                            </li>
                            <li v-bind:class="{'with-content' : steps.processing.status}">
                                <span class="title lined" :class="steps.processing.status">Processando</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.processing.status}">{{ steps.processing.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.processing.status}">{{ steps.processing.time }}</p>
                                </span>
                            </li>
                            <li v-bind:class="{'with-content' : steps.production.status}">
                                <span class="title lined" :class="steps.production.status">Produção</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.production.status}">{{ steps.production.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.production.status}">{{ steps.production.time }}</p>
                                </span>
                            </li>
                            <li v-bind:class="{'with-content' : steps.requested_invoice.status}">
                                <span class="title lined" :class="steps.requested_invoice.status">Coleta Solicitada</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.requested_invoice.status}">{{ steps.requested_invoice.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.requested_invoice.status}">{{ steps.requested_invoice.time }}</p>
                                </span>
                            </li>
                            <li v-bind:class="{'with-content' : steps.shipping.status}">
                                <span class="title lined" :class="steps.shipping.status">A Caminho</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.shipping.status}">{{ steps.shipping.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.shipping.status}">{{ steps.shipping.time }}</p>
                                </span>
                            </li>

                            <li v-bind:class="{'with-content' : steps.delivering_error.status}" v-if="steps.delivering_error.date != 'Sem Data'">
                                <span class="title lined" :class="steps.delivering_error.status">Problemas ao Entregar</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.delivering_error.status}">{{ steps.shipping.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.delivering_error.status}">{{ steps.shipping.time }}</p>
                                </span>
                            </li>
                            <li v-bind:class="{'with-content' : steps.delivered.status}">
                                <span class="title" :class="steps.delivered.status">Entregue</span>
                                <span class="description">
                                    <p class="m-0" v-bind:class="{'text-white': !steps.delivered.status}">{{ steps.delivered.date }}</p>
                                    <p class="m-0" v-bind:class="{'text-white': !steps.shipping.status}">{{ steps.delivered.time }}</p>
                                </span>
                            </li>
                        </ul>
                    </ol>
                </div>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    props : ["order","steps"]

}

</script>
<style scoped lang="scss">
.timeline {
    width: 100%;
    .events {
        position: relative;
        // background-color: #606060;
        height: 3px;
        width: 100%;
        border-radius: 4px;
        margin: 5em 0;
        ol {
            margin: 0;
            padding: 0;
            text-align: center;
        }
        ul {
            list-style: none;
            li {
                display: inline-block;
                width: 12%;
                margin: 0;
                padding: 0;
                opacity: .3;
                &.with-content {
                    opacity: 1;
                }
                span {
                    &.description {
                        font-size:12px;
                    }
                    &.title {
                        &.lined {
                            width: 100%;
                            display: block;
                            &:before {
                                height: 4px;
                                background-color: #b3b3b3;
                                content: "";
                                width: 95%;
                                position: absolute;
                                right: -47%;
                                bottom: -18px;
                            }
                        }
                        font-size: 12px;
                        position: relative;
                        top: -32px;
                        &:after {
                            content: '';
                            position: absolute;
                            bottom: -25px;
                            left: 50%;
                            right: auto;
                            height: 20px;
                            width: 20px;
                            border-radius: 50%;
                            border: 3px solid #606060;
                            background-color: #fff;
                            transition: 0.3s ease;
                            transform: translateX(-50%);
                        }
                        &.selected:after {
                            background-color: #41bc5e;
                            border-color: #41bc5e;
                        }
                        &.actual:after {
                            background-color: #eec263;
                            border-color: #eec263;
                        }
                    }
                }
            }
        }
    }
}

.events-content {
    width: 100%;
    height: 100px;
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    justify-content: left;
    li {
        display: none;
        list-style: none;
        &.selected {
            display: initial;
        }
        h2 {
            font-family: 'Frank Ruhl Libre', serif;
            font-weight: 500;
            color: #919191;
            font-size: 2.5em;
        }
    }
}
</style>
