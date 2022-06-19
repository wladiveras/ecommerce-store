<style scoped>
.orange {
    background-color: #FBB254;
}

.green {
    background-color: #99D468;
}

.purple {
    background-color: #B377D9;
}

.red {
    background-color: #ED5564;
}
.blue {
    background-color: #a0a0ff;
}
.order {
    border-right: 1px solid black;
}


.col-md-1.orderNumber {
    padding-left: 36px;
}

.custom-body {
    padding: 24px;
}

.dateCreated {
    font-size: 14px;
    letter-spacing: 2px;
}

.info-title {
    font-weight: 900;
}

.details {
    font-size: 14px;
    text-decoration: underline;
}
</style>
<template>
<b-card no-body class="payment-method mb-3">
    <b-card-header role="tab" @click="toggle" v-bind:class="color">
        <div class="row d-flex align-items-center">
            <div class="col-md-6 pl-2">
                <div class="d-flex align-items-center">
                    <i class="material-icons" v-if="isOpen">arrow_drop_up</i>
                    <i class="material-icons" v-else>arrow_drop_down</i>
                    <span class="order pr-3">Compras</span>
                    <span class="pl-3 orderNumber"><b>#{{order.code}}</b> -
                      <template v-for="groupItem in order.skus">
                        <span v-if="groupItem[0]" class="badge badge-secondary mr-2" style="background-color:black;font-size:14px;">{{groupItem[0].data.config_info.ref_name}}</span>
                      </template>
                    </span>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <b>{{ order.data.totalPrice.currency() }}</b>
            </div>
            
        </div>
    </b-card-header>
    <b-collapse role="tabpanel" :id="ref" accordion="checkout" :ref="ref">
        <b-card-body class="custom-body">
            <div class="row">
                <div class="col-sm-2 text-left">
                    <h4 class="mb-1 info-title">Data de compra</h4>
                    <span class="mt-0 dateCreated">{{ order.created_at }}</span>
                </div>
                <div class="col-sm-2 text-left">
                    <h4 class="mb-1 info-title">Qnt. Pedidos</h4>
                    <span class="mt-0 dateCreated">{{ Object.keys(order.skus).length }}</span>
                </div>
                <div class="col-sm-2 text-left">
                    <h4 class="mb-1 info-title">Status</h4>
                    <span class="mt-0 dateCreated">{{ orderStatus }}</span>
                </div>
                <div class="col-sm-2 text-left">
                    <h4 class="mb-1 info-title">Frete</h4>
                    <span class="mt-0 dateCreated"><b class="info-title">{{ (order.data.shipping.price ? order.data.shipping.price : 0).currency() }}</b></span>
                </div>
                <div class="col-sm-2 text-left">
                    <h4 class="mb-1 info-title">Total</h4>
                    <span class="mt-0 dateCreated"><b class="info-title">{{ order.data.totalPrice.currency() }}</b></span>
                </div>
                <div class="col-sm-1 text-center" style="top: 15px;">
                    <a class="details" :href="urlDetail">Ver pedido(s)</a>
                </div>
                <div class="col-sm-1 text-right" style="top: 15px;">
                    <el-tooltip class="item" effect="dark" content="Detalhes" placement="top-start">
                        <a :href="urlDetail"><i class="material-icons">more_vert</i></a>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="Informações" placement="top-start">
                        <a :href="urlDetail"><i class="material-icons" v-if="(this.order.status=='pending')">info</i></a>
                    </el-tooltip>
                </div>
            </div>
        </b-card-body>
    </b-collapse>
</b-card>
</template>

<script>
export default {
    props: ["id", "color", "shared", "order"],
    data() {
        return {
            ref: this.id.toString()
        }
    },
    computed: {
        urlDetail() {
            let url = this.shared.route_detail.replace("change_here", this.order.hashid);
            // console.log(url);
            return url;
        },
        orderStatus() {
            let id = this.order.status_id;
            let status = this.shared.status_list.find(x=>x.id==id);
            switch (status.value) {
                case "forwarded":
                    return "Encaminhado";
                    break;
                case "pending":
                    return "Pendente";
                    break;
                case "delivered":
                    return "Entregue";
                    break;
                case "canceled":
                    return "Cancelado";
                break;
                case "approved":
                    return "Confirmado";
                    break;
                case "production":
                    return "Em Produção";
                    break;
                default:
                    return this.order.status_id;
                    break;
            }
        },
        isOpen() {
            return (this.shared.boxOpened == this.id);
        }
    },
    mounted() {
        //    console.log(this.color);
    },
    methods: {
        changeIcon() {
            if (this.isOpen) {
                this.shared.boxOpened = null;
            } else {
                this.shared.boxOpened = this.id;
            }
        },
        toggle() {
            this.changeIcon();
            return this.$refs[this.ref].toggle();
        }
    }
}
</script>
