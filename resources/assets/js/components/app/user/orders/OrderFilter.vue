<template>
<form ref="form" v-on:submit="$loading()">
    <div class="row detail-card mb-3">
        <div class="col-md-12">
            <div class="card">
                
                <div class="card-header my-0">
                    <div class="row  d-flex justify-content-end align-items-center">
                        <div class="col-md-6 col-sm-12 text-left">
                            <b><h4 class="d-flex align-items-center title"><i class="material-icons icon mr-2">store_mall_directory</i>Filtro</h4></b>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <el-select size="mini" v-model="filter.showing_day" @change="setShowingDay()" placeholder="Selecione o status" class="w-100 mb-2" filterable>
                                <el-option label="Últimos 7 dias"  value="7d">Últimos 7 dias</el-option>
                                <el-option label="Últimos 15 dias" value="15d">Últimos 15 dias</el-option>
                                <el-option label="Últimos 30 dias" value="30d">Últimos 30 dias</el-option>
                                <el-option label="Últimos 45 dias" value="45d">Últimos 45 dias</el-option>
                                <el-option label="Últimos 90 dias" value="90d">Últimos 90 dias</el-option>
                                <el-option label="Todos os dias"   value="all">Todos os dias</el-option>
                            </el-select>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <el-select size="mini" v-model="filter.status" @change="setStatus()" placeholder="Selecione o status" class="w-100 mb-2" filterable>
                                <el-option label="Todos Status" value="todos">
                                        Todos Status <span class="badge badge-secondary">{{counter.all}}</span>
                                </el-option>
                                <el-option label="Status Pendente" value="pendente">
                                        Pendente  <span class="badge badge-secondary">{{counter.pending}}</span>
                                </el-option>
                                <el-option label="Status Processando"  value="aprovado">
                                        Processando  <span class="badge badge-secondary">{{counter.approved}}</span>
                                </el-option>
                                <el-option label="Status Produção"  value="producao">
                                        Produção  <span class="badge badge-secondary">{{counter.production}}</span>
                                </el-option>
                                <el-option label="Status Finalizando"  value="finalizando">
                                        Finalizando  <span class="badge badge-secondary">{{counter.finishing}}</span>
                                </el-option>
                                <el-option label="Status Encaminhado"  value="encaminhado">
                                        Encaminhado  <span class="badge badge-secondary">{{counter.forwarded}}</span>
                                </el-option>
                                <el-option label="Status Concluido"  value="concluido">
                                        Concluido  <span class="badge badge-secondary">{{counter.finished}}</span>
                                </el-option>
                                <el-option label="Status Cancelado"  value="cancelado">
                                        Cancelado  <span class="badge badge-secondary">{{counter.canceled}}</span>
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-2 col-sm-12 mb-2">
                            <label class="mb-0">Código da Compra</label>
                            <input class="form-control input-sm mt-2" v-bind:class="{'filtered':filter.code}"  v-model="filter.code" name="code" id="code">
                        </div>
                        <div class="col-md-3 col-sm-12 mb-2">
                            <label class="mb-0">Nome da Arte</label>
                            <input class="form-control input-sm mt-2" v-bind:class="{'filtered':filter.art_name}"  v-model="filter.art_name" name="art_name" id="art_name">
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label  class="mb-0">Método de Envio</label>
                            <select class="form-control input-sm" v-bind:class="{'filtered':filter.shipping_method && filter.shipping_method!='all'}"  name="shipping_method" v-model="filter.shipping_method">
                                <option value="all">Todos</option>
                                <option value="shipping">Frete</option>
                                <option value="client_shipping">Envio para o Cliente</option>
                                <option value="withdrawal">Retirada (Lojas Padrão Color)</option>
                                <option value="retirada_balcao">Retirada (Balcão Jadlog)</option>
                                <option value="payment">Rota</option>
                            </select>
                         </div>
                        <div class="col-md-4 col-sm-12">
                            <label  class="mb-0">
                                Forma de Pagamento
                                <label class="ml-3 mb-0"><input type="checkbox" v-model="filter.pagto_approved" name="pagto_approved"><span class="ml-2" v-bind:class="{'filtered_color':filter.pagto_approved}">Somente pagtos Aprovados</span></label>
                            </label>
                            <select class="form-control input-sm" v-bind:class="{'filtered':filter.payment_method && filter.payment_method!='all'}"  name="payment_method" v-model="filter.payment_method">
                                <option value="all">Todos</option>
                                <option value="paylater">Pagar na Retirada</option>
                                <option value="creditcard">Cartão de Crédito</option>
                                <option value="bankslip">Boleto Bancário</option>
                            </select>
                         </div>
                    </div>
                    <div class="row d-flex align-items-center mt-3">
                        <div class="col-md-2 col-sm-12">
                            <label class="mb-0">Data da Compra</label>
                            <input type="date" v-bind:class="{'filtered':filter.date_start}" class="form-control input-sm mb-2" name="date_start"  v-model="filter.date_start">
                            <input type="date" v-bind:class="{'filtered':filter.date_end}" class="form-control input-sm" name="date_end" v-model="filter.date_end" >
                        </div>
                        <div class="col-md-2 col-sm-12 mt-2">
                            <label class="mb-0">Hora da Compra</label>
                            <input type="time" v-bind:class="{'filtered':filter.time_start}" class="form-control input-sm mb-2" name="time_start"  v-model="filter.time_start">
                            <input type="time" v-bind:class="{'filtered':filter.time_end}" class="form-control input-sm" name="time_end" v-model="filter.time_end" >
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <div class="btn-group btn-block d-lg-none">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split ml-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" @click.prevent="cleanFilter">Limpar Filtro</a>
                                </div>
                            </div>
                            <input style="display:none" v-model="filter.showing_day" name="showing_day" />
                            <input style="display:none" v-model="filter.status" name="status" />
                            <div class="btn-group d-none d-lg-block float-right ">
                                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" @click.prevent="cleanFilter">Limpar Filtro</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-warning text-white alert-dismissible" role="alert" style="display:none;" ref="warning_alert_payment_pending">
        <div class="d-flex align-items-center" style="color:black;"><i class='material-icons mr-2'>restore</i>Existe um ou mais boletos(s) aguardando pagamento</div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true" style="color:black;">&times;</span>
        </button>
    </div>
    <div class="alert alert-danger text-white alert-dismissible" role="alert" style="display:none;" ref="error_alert">
        <div class="d-flex align-items-center"><i class='material-icons mr-2'>warning</i><b class="mx-1">Atenção {{user}},</b> existe um ou mais pedido(s) que requer atenção</div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</form>
</template>

<script>
export default {
    props: ["request","counter","user"],
    data() {
        return {
            interval : null,
            interval2 : null,
            filter : {
                code : this.request.filter.code ? this.request.filter.code : null,
                art_name : this.request.filter.art_name ? this.request.filter.art_name : null,
                status : this.request.filter.status ? this.request.filter.status : "todos",
                showing_day : this.request.filter.showing_day ? this.request.filter.showing_day : "15d",
                payment_method : this.request.filter.payment_method ? this.request.filter.payment_method : 'all',
                shipping_method : this.request.filter.shipping_method ? this.request.filter.shipping_method : 'all',
                date_start : this.request.filter.date_start ? this.request.filter.date_start : null,
                date_end : this.request.filter.date_end ? this.request.filter.date_end : null,
                time_start : this.request.filter.time_start ? this.request.filter.time_start : null,
                time_end : this.request.filter.time_end ? this.request.filter.time_end : null,
                pagto_approved : this.request.filter.pagto_approved ? this.request.filter.pagto_approved : false,
            }
        }
    },
    mounted() {
        this.interval = setInterval( () => {
            if($(".error_tag").length>0)
            {
                $(this.$refs.error_alert).show(250)
                clearInterval(this.interval)
            }
        },200)

        this.interval2 = setInterval( () => {
            if($(".bank_slip_pending").length>0)
            {
                $(this.$refs.warning_alert_payment_pending).show(250)
                clearInterval(this.interval)
            }
        },200)

        
        setTimeout( () => clearInterval(this.interval),5000)
        setTimeout( () => clearInterval(this.interval2),5000)
    },
    methods:
    {
        cleanFilter() {
            this.$loading()
            window.location.href = location.href.slice(0, - ((location.search + location.hash).length))
        },
        setShowingDay() {
            this.$loading()
            setTimeout( () => {
                $(this.$refs.form).submit()
            },100)
        },
        setStatus() {
            this.$loading()
            setTimeout( () => {
                console.log(this.filter)
                $(this.$refs.form).submit()
            },100)
        }
    }
}
</script>
<style scoped lang="scss">
.filtered { 
    border: 2px solid #1B4E01;
}
.filtered_color {
    color: #1B4E01;
}
.badge {
    background-color:white;
    border:1px solid black;
    color:black;
    margin-left:10px;
}
</style>