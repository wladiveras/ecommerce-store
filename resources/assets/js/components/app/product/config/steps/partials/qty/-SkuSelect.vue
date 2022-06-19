<style lang="scss" scoped>
.card-custom
{
    cursor:pointer;
}
.card-custom:hover
{
    border: 1px solid #1b4e01;
    transition:.8s;
    .icon {
        display:block!important;
    }
}
.card-header {
    .icon {
        display:none;
    }
}

.btn-close
{
    width: 100%;
    height: 50px;
    background-color: #1B4E01;
    color: white;
    border: none;
    font-size: 18px;
    opacity: .5;
}
.btn-close:hover
{
    transition:.5s;
    opacity: 1;
}
</style>
<template>
<div >
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h5 class="m-0">Selecione uma quantidade válida ...</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row" >
                <div class="col" v-for="(row,index) in options">
                    <div v-if="row.value" class="card card-custom shaking" @click="select_qtde( row.value )">
                        <div class="card-header d-flex align-items-center">
                            Opção {{row.label}} <i class="material-icons icon checked ml-2">check_circle</i>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <b>Quantidade</b>
                                </div>
                                <div class="col-md-6 text-right">
                                    <b>{{row.value}}</b>
                                </div>
                            </div>
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
    props: ["values"],
    data() {
        return {
            options:null
        }
    },
    mounted()
    {
        this.load_options();
    },
    methods:
    {
        load_options()
        {
            let _options = this.values.values;
            let min = this.values.min;
            let options = [];
            for(let i=0;i<_options.length;i++)
            {
                if(_options[i]<min)
                {
                    options.push({label:"Mínima",value:min});
                    break;
                }
                options.push({label:((i==0)?"Menor":"Maior"), value:_options[i]});
            }
            this.options = options;
        },
        select_qtde(amount)
        {
            this.$parent.amount = amount;
            this.$parent.modal.show=false;
            this.$parent.confirm();
        }
    }
}
</script>
