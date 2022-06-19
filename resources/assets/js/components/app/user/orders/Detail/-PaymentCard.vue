<template>
    <div class="col-md-6 pr-0 col-12 mb-3">
        <div class="card detail-card">
            <div class="card-header">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="col-md-8 col-12 pl-0">
                        <h4 class="title d-flex align-items-center">
                            <i class="material-icons icon mr-2">attach_money</i>
                            Pagamentos
                        </h4>
                    </div>
                    <div v-if="(order.has_bankslip)" class="col-md-4 col-12 text-right pr-0">
                        <a :href="order.payments[0].data.payment.bankslip.pdf" target="blank" class="link white" v-if="(order.has_bankslip)">Reimprimir Boleto</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table-striped items">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Data Aprovação</th>
                            <th>Método</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="payment in order.payments">
                            <tr>
                                <td>{{ status_name(payment) }}</td>
                                <td>{{ get_payment_date(payment) }}</td>
                                <td>{{ type_name(payment) }}</td>
                                <td>{{ (payment.data.amount*0.01).currency() }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["order","statusPaymentList"],
    methods : {
        type_name(payment) {
            switch(payment.type) {
                case "paylater":
                    return "Pagar na retirada"
                break
                case "creditcard":
                    return "Cartão de Crédito " + "(em "+payment.data.installment+"x)"
                break
                case "bankslip":
                    return "Boleto Bancário"
                break
                default :
                    return payment.type
                break
            }
        },
        get_payment_date(payment) {
            let payment_date = payment.payment_date
            if((payment.data.payment.confirmed)&&(!payment_date)) return ""
            if(!payment_date) {
                if(this.type=="withdral") return "Pagamento na Retirada"
                else if(payment.data.response.status=="PAID") return this.formatPaymentDate(payment.data.response.payment_date) 
                return "Aguardando Pagamento"
            }
            else return this.format_date(payment_date)
        },
        format_date(date) {
            if(!date) return ""
            return date.substr(8,2)+"/"+date.substr(5,2)+"/"+date.substr(0,4)
        },
        status_name(payment) {
            payment = this.statusPaymentList.find(x=>x.id==payment.status_id);
            switch(payment.value) {
                case "pending":
                    return "Aguardando pagamento"
                break
                case "approved":
                    return "Pagamento aprovado"
                break
                case "canceled":
                    return "Pagamento cancelado"
                break
                case "reversed":
                    return "Estornado"
                default :
                    return payment.value
                break
            }
        }
    }
}
</script>