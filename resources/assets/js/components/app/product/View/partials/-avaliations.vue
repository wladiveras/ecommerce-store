<template>
    <div class="testimonials_row mb-3">
        <div class="row">
            <div class="col-12">
                <h4 class="section_title"><b>Avaliações</b> dos clientes</h4>
            </div>
        </div>
        <div class="row d-flex mb-5">
            <div class="col-md-12 col-sm-12 text-left justify-content-between d-flex align-items-center pr-0">
                <div class="rate_value d-flex flex-column text-center" > 
                    <h1 v-if="product.avaliations.length>0">{{product.rate.toFixed(1)}}</h1>
                    <h4 v-else>Nenhuma Avaliação</h4>
                    <div class="stars">
                        <component-rate v-if="product.avaliations.length>0" :val="product.rate" readonly />
                        <div class="counter" v-if="product.avaliations.length>0">{{counter}}</div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12 real_tests py-3 pr-0">
                    <div class="d-flex align-items-center flex-column" v-for="(rate,i) in averagePercent">
                        <div class="mb-2 w-100 d-flex flex-row">
                            <div class="col-md-4 col-sm-12">
                                <div>{{i}}</div>
                            </div>
                            <div class="col-md-7 col-sm-12 pt-2">
                                <el-progress class="mb-2" :percentage="(rate*100)/5" :show-text="false" /> 
                            </div>
                            <div class="col-md-1 col-sm-12">
                                <div>{{rate.toFixed(1)}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="user">
                    <button type="button" class="col-md-3 ml-5 btn btn-primary config  mt-3 mr-3" v-if="user.id==3" @click="showFormCreate">AVALIAR ESTE PRODUTO</button>
                </template>
            </div>
            <template v-if="user">
                <modal name="create_avaliations" trasition="fade" height="auto" width="1000" :adaptive="true" :scrollable="true" :clickToClose="false" >
                    <div class="row">
                        <div class="col-12">
                            <div class="card" v-loading="frm_new_avaliation.loading">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <component-rate class="frm" :val="frm_average" readonly />
                                        <a href="#" @click.prevent="$modal.hide('create_avaliations')">X</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-8 col-sm-12">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label><b>Autor :</b></label>
                                                    <input class="form-control" v-model="frm_new_avaliation.author" :disabled="user.id!=3">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12 col-sm-12">
                                                    <label><b>Titulo :</b></label>
                                                    <input class="form-control" v-model="frm_new_avaliation.title">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <label><b>Descrição :</b></label>
                                                    <textarea class="form-control" rows="5" v-model="frm_new_avaliation.description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label class="mb-0"><b>Qualidade Geral :</b> {{frm_new_avaliation.rates['Qualidade Geral']}}</label>
                                                    <el-slider :min="0" :max="5" v-model="frm_new_avaliation.rates['Qualidade Geral']"></el-slider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label class="mb-0"><b>Acabamento :</b> {{frm_new_avaliation.rates['Acabamento']}}</label>
                                                    <el-slider :min="0" :max="5" v-model="frm_new_avaliation.rates['Acabamento']"></el-slider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label class="mb-0"><b>Prazo :</b> {{frm_new_avaliation.rates['Prazo']}}</label>
                                                    <el-slider :min="0" :max="5" v-model="frm_new_avaliation.rates['Prazo']"></el-slider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label class="mb-0"><b>Cor :</b> {{frm_new_avaliation.rates['Cor']}}</label>
                                                    <el-slider :min="0" :max="5" v-model="frm_new_avaliation.rates['Cor']"></el-slider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <label  class="mb-0"><b>Custo-benefício :</b> {{frm_new_avaliation.rates['Custo-benefício']}}</label>
                                                    <el-slider :min="0" :max="5" v-model="frm_new_avaliation.rates['Custo-benefício']"></el-slider>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button class="btn btn-primary px-5" @click="submitAvaliation">ENVIAR AVALIAÇÃO</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </modal>
            </template>
        </div>
        <template v-if="last_avaliations" v-for="a in last_avaliations">
            <hr>
            <div class="real_tests py-3">
                <div class="row d-flex align-items-center">
                    <div class="col-md-7 col-sm-12 d-flex flex-column">
                        <component-rate :val="a.average" readonly > <span style="padding-top: 2px;"><b>{{Number(a.average).toFixed(1)}}</b></span></component-rate>
                        <div><h5 class="title">{{a.title}}</h5></div>
                        <div class="from">Por : {{a.author}}  em {{format_date(a.created_at)}}</div>
                        <div class="test">{{a.description}}</div>
                    </div>
                    <div class="col-md-5 col-sm-12">
                        <template v-for="(rate,i) in a.rates">
                            <div class="row mb-2" >
                                <div class="col-md-4 col-sm-12">
                                    <div>{{i}}</div>
                                </div>
                                <div class="col-md-7 col-sm-12 pt-2">
                                    <el-progress class="mb-2" :percentage="(rate*100)/5" :show-text="false" /> 
                                </div>
                                <div class="col-md-1 col-sm-12">
                                    <div>{{rate.toFixed(1)}}</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <div class="row my-5" v-if="can_show_more">
            <div class="col-12 d-flex justify-content-center">
                <button type="button" @click="show_more" class="btn btn-primary config" v-loading="loading_button">VER <template v-if="showing_more">MENOS</template><template v-else>MAIS</template> AVALIAÇÕES</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ['product','user'],
    data() {
        return {
            loading_button : false,
            showing_more : false,
            initial_rates : {
                "Prazo" : 5,
                "Qualidade Geral" : 5,
                "Acabamento" : 5,
                "Cor" : 5,
                "Custo-benefício" : 5
            },
            frm_new_avaliation : {
                loading : false,
                author : null,
                average : 5,
                rates : {
                    "Prazo" : 5,
                    "Qualidade Geral" : 5,
                    "Acabamento" : 5,
                    "Cor" : 5,
                    "Custo-benefício" : 5
                },
                title : null,
                description : null
            }
        }
    },
    computed : {
        averagePercent() {
            if(this.product.avaliations<=0) return {}
            let keys = Object.keys(this.product.avaliations[0].rates)
            let values = {}
            for(let i in keys) values[keys[i]] = this.getAverage(keys[i])
            return values
        },
        frm_average() {
            let val = this.frm_new_avaliation.rates
            let keys = Object.keys(val)
            let sum = 0
            for(let i in keys) {
                sum += val[keys[i]]
            }
            let qty = keys.length
            let average = sum/qty
            this.frm_new_avaliation.average = average
            return average
        },
        counter() {
            let avaliations = this.product.avaliations
            if(avaliations.length==0) return "0 avaliação"
            let qty = avaliations.length
            return `${qty} ${qty>1 ?'avaliações' : 'avaliação'}`
        },
        last_avaliations() {
            let avaliations = this.product.avaliations
            if(avaliations.length==0) return null
            return this.showing_more ? avaliations : avaliations.slice(0,3)
        },
        rate_percent() {
            let avaliations = this.product.avaliations
            if(avaliations.length==0)  return "0%"
            return "100%"
        },
        can_show_more() {
            let avaliations = this.product.avaliations
            return (avaliations.length>3)
        }
    },
    methods : {
        getAverage(index)
        {
            let sum = 0
            let avaliations = this.product.avaliations
            for(let i in avaliations)
            {
                sum+=avaliations[i].rates[index]
            }
            return sum/(avaliations.length)
        },
        show_more() {
            this.loading_button = true
            setTimeout(() => {
                this.showing_more = !this.showing_more
                this.loading_button = false
            },500)
        },
        format_date(date) {
            let day = date.substring(8,10)
            let month = date.substring(5,7)
            let year = date.substring(0,4)
            return `${day}/${month}/${year}`
        },
        showFormCreate() {
            this.frm_new_avaliation.loading = false
            this.frm_new_avaliation.author = this.user.name
            this.frm_new_avaliation.average = 5
            this.frm_new_avaliation.rates = Object.assign({},this.initial_rates)
            this.frm_new_avaliation.title = null
            this.frm_new_avaliation.description = null
            return this.$modal.show('create_avaliations')
        },
        submitAvaliation() {
            if(!this.frm_new_avaliation.author) return this.$toastr.error("Autor é campo obrigatório")
            if(!this.frm_new_avaliation.title) return this.$toastr.error("Título é campo obrigatório")
            if(!this.frm_new_avaliation.description) return this.$toastr.error("Descrição é campo obrigatório")
            this.frm_new_avaliation.loading = true
            this.$http.post(`${window.location.href}/create_avaliation`,this.frm_new_avaliation).then( res => {
                this.frm_new_avaliation.loading = false
                this.$modal.hide('create_avaliations')
                this.$toastr.success("Avaliação criada com sucesso e aguardando aprovação")
            }).catch( er => {
                this.frm_new_avaliation.loading = false
                this.$modal.hide('create_avaliations')
                console.log(er)
            })
        }
    }
}
</script>
<style lang="scss"> 
    .frm {
        .el-rate__icon {
            font-size : 30px;
        }
    }
    .real_tests {
        .title {
            color:black;
            font-weight :700;
            margin-bottom: 0;
        }
        .from {
            font-size: 12px;
            color: #929292;
        }
    }
    .percent {
       h1 {
            font-size: 85px;
            letter-spacing: -5px;
            font-weight: bolder;
            color: #1B4E01;  
            margin-bottom: 0;
        } 
        .sub_text {
            font-size: 12px;
            color: #8c8c8c;
        }
    }
    .rate_value{
        h1 {
            font-size: 140px;
            letter-spacing: -5px;
            font-weight: bolder;
            color: #ff963a;  
            margin-bottom: 0;
        }
        h4 {
            font-size: 30px;
            letter-spacing: -2px;
            font-weight: bolder;
            color: #1B4E01;  
            margin-bottom: 0;
        }
        .stars {
            .counter {
                font-size: 15px;
                color: #8c8c8c;
            }
            .el-rate__icon {
                font-size:50px;
                top:-30px;
            }
        }
    }
</style>