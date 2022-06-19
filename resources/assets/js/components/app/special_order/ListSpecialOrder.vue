<template>
    <div class="special_orders">
        <div class="card-list">
            <h1 style="color:#8e8e8e;"><template v-if="!data.is_admin">Meus</template> Projetos Especiais</h1>
            <div class="d-flex flex-wrap mb-5">
                <div class="input-group col-12 px-0" v-loading="filtring">
                    <span class="input-group-prepend" >
                        <select class="form-control" style="border-radius: unset;" v-model="status">
                            <option value="all">Todos Status</option>
                            <option value="opened">Apenas Status Aberto</option>
                            <option value="contacting">Em Contato</option>
                            <option value="effective">Efetivado</option>
                            <option value="existing_products">Produtos Existente</option>
                        </select>
                    </span>
                    <input class="form-control py-2 h-100 border-right-0 border" type="search" placeholder="Procure por um projeto específico ..." v-model="filter">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary border-left-0 border d-flex align-items-center" @click="makeFilter" type="button">
                            <i class="material-icons">search</i>
                        </button>
                    </span>
                </div>
                <small>Digite no campo acima o conteúdo que deseja utilizar de filtro para encontrar projetos especiais</small>
            </div>
            <template v-if="items.length==0 && loaded">
                <div class="d-flex flex-wrap justify-content-center">
                    <h4>Nenhum resultado encontrado</h4>
                </div>
            </template>
            <div v-else class="d-flex flex-wrap">
                <template>
                <div class="col-md-3 col-sm-12 pl-0 mb-3" v-for="(item,i) in items" :key="i">
                    <el-tooltip content="Ver mais" placement="right-start" :key="`tooltip_${i}`">
                        <div class="card card-item mb-3 status h-100" :class="item.status_value" >
                            <div class="card-header d-flex justify-content-between align-items-center" @click="show(item)">
                                <div>#{{String(item.id).padStart(6,'0')}} - <b>{{item.title}}</b></div> 
                                <div class="d-flex justify-content-end"><i class="material-icons">more_vert</i></div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex flex-row">
                                        <b>Autor : </b><div class="ml-2"><template v-if="data.is_admin">#{{String(item.user_id).padStart(6,'0')}} - </template>{{item.author}}</div>
                                    </div>
                                    <template v-if="data.is_admin">
                                        <div class="d-flex flex-row">
                                            <b>Username : </b><div class="ml-2">{{item.author}}</div>
                                        </div>
                                        <div class="d-flex flex-row">
                                            <b>Email : </b><div class="ml-2">{{item.user_email}}</div>
                                        </div>
                                        <div class="d-flex flex-row" v-if="item.wpp_phone">
                                            <b>Telefone : </b><div class="ml-2">{{item.wpp_phone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')}}</div>
                                        </div>
                                    </template>
                                    <div class="d-flex flex-row">
                                        <b>Status : </b><div class="ml-2 d-flex flex-row align-items-center"><div :class="`status-ball ${item.status_value} mr-2`"></div>{{item.status}}</div>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <b>Data da Solicitação : </b><div class="ml-2">{{item.formated_date}}</div>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <b>Data de Entrega : </b><div class="ml-2">{{item.target_date}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-tooltip>
                </div>
                </template>
            </div>
            <div class="row" style="height:40px;" v-loading="loading"></div>
            <template v-if="show_btn_show_more">
                <div class="d-flex justify-content-center" >
                    <a class="d-flex align-items-center refresh" @click="load_items"><i class="material-icons mr-2">refresh</i>Ver Mais</a>
                </div>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    props : ['data'],
    data() {
        return {
            filtring : false,
            loaded : false,
            status : "all",
            filter : null,
            loading : true,
            items : [],
            item_page  : 0,
            show_btn_show_more : true,
            time_to_filter : null
        }
    },
    async created() {
        this.load_items()
    },
    watch : {
        filter(val) {
            if(this.time_to_filter) clearTimeout(this.time_to_filter)
            this.time_to_filter = setTimeout( _ => {
                this.makeFilter() 
                clearTimeout(this.time_to_filter)
            },500)
        },
        status(val) {
            this.makeFilter()
        }
    },
    methods : {
        show(item) {
            let loading = this.$loading()
            window.location.href=this.data.routes.show.replace("%%",item.id)
        },
        makeFilter() {
            this.items = []
            this.item_page = 0
            this.load_items()
        },
        load_items() {
            this.loading = true
            this.filtring = true
            this.$http.post(this.data.routes.get_items,{page:++this.item_page,filter : this.filter, status : this.status}).then( res => {
                res = res.data
                this.items = this.items.concat(res.data.data)
                this.show_btn_show_more = res.data.next_page_url ? true : false
                this.loading = false
                this.time_to_filter = null
                this.filtring = false
                this.loaded = true
            }).catch( er => {
                this.loading = false
                console.log(er)
            })
            
        }
    }
}
</script>