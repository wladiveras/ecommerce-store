<template>
<div v-loading="loading">
	<div class="row">
		<div class="hide-mark d-sm-flex flex-wrap col-sm-12" >
			<template v-for="(card,key) in cards">
                <div class="col-sm-3 mb-3" @click="openForm('edit',card)">
                    <creditcard-div :index="key+1" :card="card"></creditcard-div>
                </div>
			</template>
            <fields-card-edit ref="frm"/>
		</div>
	</div>
</div>
</template>

<script>
export default {
  props:["cards"] ,
  data() {
    return {
			loading : false
		}
	},
	components: {
		'creditcard-div' : require("./-CreditcardDiv.vue").default,
        'fields-card-edit' : require("./-FieldsCardEdit.vue").default
	},
	computed: {
		canSubmitNewCard() {
			if(!this.frmNewCard.name) 
				return false
			if(!this.frmNewCard.number) 
				return false
			if(!this.frmNewCard.expiring_date) 
				return false
			if(!this.frmNewCard.documentNumber) 
				return false
			if(!this.frmNewCard.cvv) 
				return false
			if(!this.frmNewCard.phoneNumber) 
				return false
			if(!this.frmNewCard.address) 
				return false
			return true
		},
	},
	methods: {
        openForm(type,card) {
            this.$refs.frm.show(type,card)
        }
	}
}
</script>