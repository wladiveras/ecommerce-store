<style lang="scss" scoped>
.resource-card {
    .card {
        &.card-content {
            min-height: 200px;
        }
    }
    .dropdown {
        &.filter {
        }
    }
    .search-field {
        border:1px solid #bbbbbb;
        input {
            border:none;
        }
        .input-group-text {
            border:none;
            background-color:transparent;
        }
        .fa-search {
            color:#bbbbbb;
        }
    }
}
</style>		
<template>
    <div class="resource-card" >
        <div class="row">
            <div class="col-12">
                <div class="card-content" v-loading="loading">
                    <div class="p-0" ref="content" >
                        <div class="row">
                            <div class="col-12">
                                <slot name="table"></slot>
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
    props: ["filter","route","perpage"],
    data() {
        return {
            datafilter : this.filter ? this.filter : {},
            table : null,
            loading : true,
            search_value : this.search ? this.search : null,
            config : null
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        show() {
            setTimeout( () => {
                this.loading = false
                // $(this.$refs.content).show(300)
            },300)
            
        },
        init() {
            this.config =  {
                "rowReorder": {
                    "selector": 'td:nth-child(2)'
                },
                "responsive": true,
                "pageLength": this.perpage ? this.perpage : 10,
                "destroy" : true,
                "aaSorting": [[0, 'desc']],
                "searching": false,
                "drawCallback": ( ) => this.show(),
                "serverSide": true,
                "ajax": {
                    "url": this.route,
                    "type":"POST",
                    "data": this.datafilter,
                    "headers": {
                        'X-CSRF-TOKEN': this.$root.csrf_token
                    }
                },
                "lengthChange": false,
                "columnDefs": [
                    { targets: 'no-sort', orderable: false }
                ],
                "oLanguage": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "<b>Mostrando :</b> de _START_ a _END_ registros<br><b>Registros Filtrados : </b>_TOTAL_",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "<br><b>Total de registros em banco : </b> _MAX_",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ Resultados",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": false,
                    "sSearch": "Encontrar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                },
                // "initComplete": function(){ 
                //     window.dispatchEvent(new Event('resize'))
                // }
            }
            this.table = $("#table").DataTable(this.config)
        }
    }
}
</script>
