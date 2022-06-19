
<style>
.card .card-header.finished {
    background-color: #f5a660;
}
.config-cards .card.mb-3 {
    border: none !important;
    margin-bottom: 0 !important;
}
textarea {
    resize: none;
}
.steps {
  padding: 0 25px 0 0;
  overflow: hidden;
  display: flex;
}
.steps a {
  color: white;
  text-decoration: none;
}
.steps em {
  display: block;
  font-size: 1.1em;
  font-weight: bold;
}
.steps li {
  margin-left: 0;
  width: auto; /* 100 / number of steps */
  height: 40px; /* total height */
  list-style-type: none;
  padding: 8px 5px 5px 30px; /* padding around text, last should include arrow width */
  border-right: 4px solid white; /* width: gap between arrows, color: background of document */
  position: relative;
}
/* remove extra padding on the first object since it doesn't have an arrow to the left */
.steps li:first-child {
  padding-left: 5px;
}
/* white arrow to the left to "erase" background (starting from the 2nd object) */
.steps li:nth-child(n+2)::before {
  position: absolute;
  top:0;
  left:0;
  display: block;
  border-left: 25px solid white; /* width: arrow width, color: background of document */
  border-top: 20px solid transparent; /* width: half height */
  border-bottom: 20px solid transparent; /* width: half height */
  width: 0;
  height: 0;
  content: " ";
}
/* colored arrow to the right */
.steps li::after {
  z-index: 1; /* need to bring this above the next item */
  position: absolute;
  top: 0;
  right: -25px; /* arrow width (negated) */
  display: block;
  border-left: 25px solid #7c8437; /* width: arrow width */
  border-top: 20px solid transparent; /* width: half height */
  border-bottom: 20px solid transparent; /* width: half height */
  width:0;
  height:0;
  content: " ";
}

/* Setup colors (both the background and the arrow) */

/* Completed */
.steps li { background-color: #EBEBEB; }
.steps li::after { border-left-color: #EBEBEB; }

/* Current */
.steps li.current { background-color: #f5a660; }
.steps li.current::after { border-left-color: #f5a660; }

/* Following */
/* .steps li.current ~ li { background-color: #EBEBEB; }
.steps li.current ~ li::after {	border-left-color: #EBEBEB; } */

/* Hover for completed and current */
/* .steps li:hover {background-color: #696}
.steps li:hover::after {border-left-color: #696} */


</style>
<template>
    <div class="config-cards">
        <!-- <div v-if="shared.data.chosen=='UP'" > -->
        <!-- <ul class="steps steps-5" id="config-breadcrumb">
            <li v-if="shared.step.position>=1" class="current" style="display">Inicio</li>
            <li v-else>Inicio</li>
            <li v-if="shared.step.position>=2" class="current">Opções</li>
            <li v-else>Opções</li>
            <li v-if="shared.step.position>=3" class="current">Quantidade</li>
            <li v-else>Quantidade</li> -->
            <!--<li v-if="shared.step.position>=5" class="current">Adicionais</li>
            <li v-else>Adicionais</li>-->
            <!-- <li v-if="shared.step.position>=6" class="current">Acabamento</li>
            <li v-else>Acabamento</li>
            <li v-if="shared.step.position>=7 || shared.step.position>=8" class="current">Arte</li>
            <li v-else>Arte</li>
            <li v-if="shared.step.position>=9" class="current">Finalizar</li>
            <li v-else>Finalizar</li>
        </ul> -->
        <!-- <div class="row teste">
          <div class="col">
            <button class="btn-continue btn-block btn-finish-primary mb-3" id="btn_confirm" @click="submit" v-bind:class="{ 'shaking': can_continue&& !is_showing_modal }" v-if="can_continue">CONTINUAR</button>
          </div>
        </div> -->
        <!-- </div> -->
        <step1-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=1"
            :data="data"
            ref="step1"
        />
        <!-- FORMA DE PEDIDO -->
        <step2-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=2"
            :data="data"
            ref="step2"
        />
        <!-- OPÇÕES-->
        <step3-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=3"
            :data="data"
            ref="step3"
        />
        <!-- QUANTIDADE -->
        <step4-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=4"
            :data="data"
            ref="step4"
        />
        <!-- CAPAS -->
        <step5-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=5"
            :data="data"
            ref="step5"
        />
        <!-- OPÇÕES ADICIONAIS -->
        <step6-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=6"
            :data="data"
            ref="step6"
        />
        <!-- ACABAMENTOS -->
        <step7-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=7"
            :data="data"
            ref="step7"
        />
        <!-- ENVIO DE ARTE -->
        <step8-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=8"
            :data="data"
            ref="step8"
        />
        <!-- TERMOS DE CRIAÇÃO DE ARTE -->
        <step9-card
            class="mb-3"
            :shared="shared"
            v-if="shared.step.position>=9"
            :data="data"
            ref="step9"
        />

    </div>
</template>

<script>

export default {
    props: ['shared', 'data'],
    components: {
        'step1-card': require('./-Step1.vue').default,
        'step2-card': require('./-Step2.vue').default,
        'step3-card': require('./-Step3.vue').default,
        'step4-card': require('./-Step4.vue').default,
        'step5-card': require('./-Step5.vue').default,
        'step6-card': require('./-Step6.vue').default,
        'step7-card': require('./-Step7.vue').default,
        'step8-card': require('./-Step8.vue').default,
        'step9-card': require('./-Step9.vue').default,
    },
    data: () => {
        return {
        }
    },
    mounted(){
      console.log('vvvvvllll', this.shared, this.data);
    },
    methods: {

    }

}
</script>
