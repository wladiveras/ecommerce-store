<template>
    <div class="shipping-item h-100 item rounded p-4 border creditcard"  :class="cardClass">
	    <div class="flex-wrap d-flex justify-content-between align-items-center">
            <img src="/assets/images/chip.png" class="chip"/>
	    </div>
        <div class="flex-wrap d-flex justify-content-start">
            <div class="f-12 f-space card-number" v-if="card.last_four_digits">
                {{  card.brand  ?  card.brand  : "Cartão"  }} {{card.last_four_digits ? card.last_four_digits : card.number.substring(card.number.length-4,card.number.length)}}
            </div>
        </div>
        <div class="flex-wrap d-flex justify-content-end align-items-center">
             <img :src="icon" class="icon"/>
        </div>
    </div>  
</template>

<script>
export default {
  props:['card','selectedCard','index'],
  computed : {
      icon() {
          let icon = "/assets/images/creditcard.png"
          if(this.card.brand) {
                if(this.card.brand=='mastercard')
                    return "/assets/images/mastercard.png"
                if(this.card.brand=='visa')
                    return "/assets/images/visa.png"
                if(["visa","mastercard"].includes(this.card.brand))
                    return icon
          }
          return icon
      },
      cardClass() {
        let icon = "othercard"
        if(this.card.brand=='mastercard')
            icon = "mastercard"
        if(this.card.brand=='visa')
            icon = "visa"
        icon += ` ${this.selectedCard==this.index ? 'selected' : ''}`
        return icon
      }
  }
}
</script>
<style lang="scss" scoped>
	.creditcard {
        cursor:pointer;
		border-radius: 8px!important;
		-webkit-box-shadow: 10px 10px 35px -16px rgba(0,0,0,0.75);
		-moz-box-shadow: 10px 10px 35px -16px rgba(0,0,0,0.75);
		box-shadow: 10px 10px 35px -16px rgba(0,0,0,0.75);
		height : 180px!important;
        .chip {
            width : 50px;
        }
        .icon {
            width : 40px;
        }
        .card-number { 
            font-size: 21px!important;
            font-family: monospace!important;
            letter-spacing: -1px;
        }
        &.mastercard {
            background-image: linear-gradient(to right top, #ffffff, #d6c2d9, #bf83a0, #a44356, #750000);
        }
        &.othercard {
            background-image: linear-gradient(to right top, #ffffff, #cfc5e3, #a78bc3, #85519e, #670075);
        }
        &.visa {
            background-image: linear-gradient(to right top, #ffffff, #d7d0ff, #aca3ff, #7778ff, #004eff);
        }
	}
</style>