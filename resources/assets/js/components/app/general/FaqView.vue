<template>
  <div class="">
    <div class="container">
      <h1 class="font-weight-light mt-5 text-center text-transform-uppercase">Perguntas <span class="font-weight-bold">Frequentes</span> </h1>
      <div class="col-sm-12 mt-5 d-flex justify-content-center">
        <b-form-input v-model="filter" placeholder="Qual é a sua dúvida?" class="text-center col-sm-8" />
      </div>
    </div>
    <div class="col-sm-12 mt-5">
      <b-card no-body class="mb-3 address-data" v-for="(question, i) in filteredFaq" :key="question.id">
        <b-card-header role="tab" v-b-toggle="question.id.toString()" class="section-completed">
          <span class="ml-2 f-18">{{question.topic}}</span>
        </b-card-header>
        <b-collapse :id="question.id.toString()" role="tabpanel" accordion="checkout">
          <b-card-body>
            <div class="col-sm-12">
              <span v-html="question.answer"></span>
            </div>
          </b-card-body>
        </b-collapse>
      </b-card>
    </div>
    <div class="col-sm-12 mt-5 d-flex justify-content-center">
      <h3>Não encontrou o que precisava? <a class="text-primary" href="/contato">Contate-nos</a></h3>
    </div>
  </div>
</template>
<script>
export default {
  props:{
    faq:{
      default(){
        return [
          {
            id:1,
            category:'teste',
            topic:"Arroz ou feijao?",
            answer:"Arroz."
          },{
            id:2,
            category:'teste',
            topic:"Batata ou macarrão?",
            answer:"Batata."
          },{
            id:3,
            category:'teste',
            topic:"Queijo ou presunto?",
            answer:"Queijo."
          },
        ];
      }
    }
  },
  data(){
    return {
      filter:""
    };
  },
  computed:{
    filteredFaq(){
      if(!this.filter){
        return this.faq;
      }
      let res = [];
      for(let index in this.faq){
        if(this.faq[index].topic.toLowerCase().includes(this.filter.toLowerCase())){
          res.push(this.faq[index]);
        }
      }
      return res;
    }
  }
}
</script>
