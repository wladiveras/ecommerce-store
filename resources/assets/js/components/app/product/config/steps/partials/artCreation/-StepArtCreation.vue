<style scoped>
.container {
    width: 100%;
}
.progressbar {
    counter-reset: step;
}
@media (max-width: 400px) {
    .progressbar {
        padding-left: 0;
        font-size: 13px;
    }
}
.progressbar li {
    list-style: none;
    display: inline-block;
    width: 30.33%;
    position: relative;
    text-align: center;
    cursor: pointer;
}
.progressbar li:before {
    content: counter(step);
    counter-increment: step;
    width: 30px;
    height: 30px;
    line-height : 30px;
    border: 1px solid #ddd;
    border-radius: 100%;
    display: block;
    text-align: center;
    margin: 0 auto 10px auto;
    background-color: #e5e5e5;
}
.progressbar li:after {
    content: "";
    position: absolute;
    width: 91%;
    height: 4px;
    background-color: #ddd;
    top: 13px;
    left: -46%;
}
.progressbar li:first-child:after {
  content: none;
}
.progressbar li {
    color: black;
    font-weight:bolder;
}
.progressbar li.active:before {
    background-image: url(/svg/checked.svg);
    background-size: cover;
    content: "";
}
.progressbar li.active + li:after {
    background-color: #D6EF63;
}
.complete-svg {
    width: 118px;
    height: 118px;
}
</style>
<template>
<div>
    <div class="container mb-4">
        <ul class="progressbar">
            <li v-bind:class="{'active': (position>1)}">TERMOS</li>
            <li v-bind:class="{'active': (position>2)}">PESSOAL</li>
            <li v-bind:class="{'active': (position>3)}">EMPRESA</li>
        </ul>
    </div>
    <component-step1 :shared="shared" ref="step_1" v-if="position==1"/>
    <component-step2 :shared="shared" ref="step_2" v-if="position==2"/>
    <component-step3 :shared="shared" ref="step_3" v-if="position==3"/>
    <div class="row" ref="step_4" v-if="position==4">
        <div class="col-md-12 text-center">
            <img src="/svg/checkout/Complete.svg" class="svg complete-svg mb-3">
            <p class="f-32 text-primary text-center">CONCLUÍDO</p>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ["shared"],
    data() {
        return {
            position : 1
        }
    },
    components:
    {
        'component-step1': require('./-Step1.vue').default,
        'component-step2': require('./-Step2.vue').default,
        'component-step3': require('./-Step3.vue').default,
    },
    methods: {
        previous()
        {
            if(this.position>1)
                this.position--;
        },
        next()
        {
            if(this.position<=3)
            {
                this.$refs["step_"+this.position].submit();
            }
        }
    },
}
</script>