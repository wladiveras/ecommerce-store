<template>
    <div class="faq" v-loading="loading">
        <div class="row" v-if="product.faqs">
            <div class="col-12">
                <h4 class="section_title"><b>Perguntas</b> frequentes</h4>
            </div>
        </div>
        <template v-if="user">
            <div class="row mb-4" v-if="user.id==3" v-loading="frm_loading">
                <template v-if="!asked" >
                    <div class="col-md-9 col-sm-12 pt-3">
                        <textarea class="form-control" rows="5" placeholder="Qual sua dúvida ?" v-model="new_question"></textarea>
                        <small>No mínimo 12 caracteres</small>
                    </div>
                    <div class="col-md-3 col-sm-12 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-primary config" @click="submitQuestion">ENVIAR PERGUNTA</button>
                    </div>
                </template>
                <template v-else>
                    <div class="col-12 text-center py-5">
                        <a href="#" class="new_question" @click.prevent="makeNewQuestion">Deseja Fazer Nova Pergunta ? Clique aqui</a>
                    </div>
                </template>
            </div>
        </template>
        <template v-if="product.faqs.length>0" >
            <template v-for="faq in last_faqs">
                <div class="real_faqs">
                    <div class="col-12 d-flex flex-column px-0">
                        <div>
                            <img src="/assets/images/question.png">
                            <b class="question">{{faq.ask}}</b>
                        </div>
                        <div class="from">Por : {{faq.author}}  em {{format_date(faq.created_at)}}</div>
                        <div class="resp">
                            <img src="/assets/images/resp.png">
                            <span >{{faq.answer}}</span>
                        </div>
                    </div>
                </div>
            </template>
            <div class="row my-5" v-if="can_show_more">
                <div class="col-12 d-flex justify-content-center">
                    <button type="button" @click="show_more" class="btn btn-primary config" v-loading="loading_button">VER <template v-if="showing_more">MENOS</template><template v-else>MAIS</template> PERGUNTAS</button>
                </div>
            </div>
        </template>
        <template v-else>
            <h4 class="no-asks">nenhuma pergunta</h4>
        </template>
    </div>
</template>
<script>
export default {
    props : ['product','user'],
    data() {
        return {
            frm_loading : false,
            asked : false,
            loading : false,
            loading_button : false,
            showing_more : false,
            new_question : null
        }
    },
    computed : {
        can_show_more() {
            let faqs = this.product.faqs
            return (faqs.length>3)
        },
        last_faqs() {
            let faqs = this.product.faqs
            if(faqs.length==0) return null
            return this.showing_more ? faqs : faqs.slice(0,3)
        },
    },
    methods : {
        makeNewQuestion() {
            this.frm_loading = true
            setTimeout(()=> {
                this.asked = false
                this.frm_loading = false
            },500)
        },
        show_more() {
            this.loading_button = true
            setTimeout(() => {
                this.showing_more = !this.showing_more
                this.loading_button = false
            },500)
        },
        submitQuestion() {
            if(this.new_question.length<12) return this.$toastr.error("A pergunta deve ter no mínimo 12 caracteres")
            this.loading = true
            this.$http.post(`${window.location.href}/create_faq`,{question : this.new_question}).then( res => {
                this.loading = false
                this.new_question = null
                this.$toastr.success("Pergunta criada com sucesso e aguardando resposta")
                this.asked = true
            }).catch( er => {
                this.loading = false
                console.log(er)
            })
        },
        format_date(date) {
            let day = date.substring(8,10)
            let month = date.substring(5,7)
            let year = date.substring(0,4)
            return `${day}/${month}/${year}`
        },
    }
}
</script>
<style lang="scss">
    .faq {
        .new_question {
            font-size: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
            font-weight: 800;
            color: #1b4e01;
            text-decoration: underline;
        }
        .no-asks {
            font-size: 30px;
            letter-spacing: -2px;
            font-weight: bolder;
            color: #d97d2a;
            margin-bottom: 0;
        }
        margin-bottom : 100px;
        .resp {
            padding-top:10px;
            padding-left :40px;
            padding-bottom :20px;
            span {
                color: #1b4e01;
            }
        }
        .real_faqs {
            img {
                width : 30px;
                margin-right : 10px;
            }
            .from {
                padding-left : 43px;
                font-size: 12px;
                color: #929292;
            }
        }
    }
</style>