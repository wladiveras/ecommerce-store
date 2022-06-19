<style scoped lang="scss">
.filtered { 
    border: 2px solid #1B4E01;
}
</style>
<template>
<form ref="form" v-on:submit="$loading()">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header flex">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-6 col-sm-12">
                            <h4 class="d-flex align-items-center">
                                <i class="material-icons mr-2 ">error</i>Meus de Tickets
                            </h4>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <a class="ml-3 link float-right" :href="create">Cadastrar Novo Ticket</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-2 col-sm-12">
                            <label class="mb-0">Código</label>
                            <input class="form-control" v-bind:class="{'filtered':filter.code}"  v-model="filter.code" name="code" id="code">
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label class="mb-0">Assunto</label>
                            <input class="form-control" v-bind:class="{'filtered':filter.subject}"  v-model="filter.subject" name="subject" id="subject">
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <label class="mb-0">Status</label>
                            <select class="form-control" v-bind:class="{'filtered':filter.status!='all'}"  v-model="filter.status" name="status" id="status">
                                <option value="all">Todos as Status</option>
                                <option value="completed">Finalizados</option>
                                <option v-for="s in status" :value="s.id">{{s.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <label class="mb-0">Categoria</label>
                            <select class="form-control" v-bind:class="{'filtered':filter.category!='all'}"  v-model="filter.category" name="category" id="category">
                                <option value="all">Todas as Categorias</option>
                                <option v-for="category in categories" :value="category.id">{{category.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label class="mb-0">Prioridade</label>
                            <select class="form-control" v-bind:class="{'filtered':filter.priority!='all'}"  v-model="filter.priority" name="priority" id="priority">
                                <option value="all">Todas as Prioridades</option>
                                <option v-for="priority in priorities" :value="priority.id">{{priority.name}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background-color:#F7F7F7;border-bottom:1px solid rgba(0, 0, 0, 0.190);">
                    <div class="row">
                        <div class="col">
                            <div class="btn-group float-right">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
</form>
</template>

<script>
export default {
    props: ["request","categories","status","priorities","create"],
    data() {
        return {
            interval : null,
            filter : {
                code : this.request.filter.code ? this.request.filter.code : null,
                subject : this.request.filter.subject ? this.request.filter.subject : null,
                status : this.request.filter.status ? this.request.filter.status : "all",
                priority : this.request.filter.priority ? this.request.filter.priority : "all",
                category : this.request.filter.category ? this.request.filter.category : "all",
            }
        }
    },
    methods:
    {
        cleanFilter() {
            this.$loading()
            window.location.href = location.href.slice(0, - ((location.search + location.hash).length))
        }
    }
}
</script>
