<template>
    <div class="col-md-6 px-0 col-12 mb-3">
        <div class="card detail-card h-100">
            <div class="card-header">
                <h4 class="title d-flex align-items-center"><i class="material-icons icon">gps_fixed</i>Endereço de Cobrança</h4>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <table class="w-100">
                        <tr>
                            <td><b>Cep</b></td>
                            <td>{{ order.data.address.zip_code }}</td>
                            <td><b>Rua</b></td>
                            <td>{{ order.data.address.street }}</td>
                        </tr>
                        <tr>
                            <td><b>Estado</b></td>
                            <td>{{ order.data.address.state }}</td>
                            <td><b>Número</b></td>
                            <td>{{ order.data.address.number }}</td>
                        </tr>
                        <tr>
                            <td><b>Cidade</b></td>
                            <td>{{ order.data.address.city }}</td>
                            <td><b>Complemento</b></td>
                            <td>{{ order.data.address.complement }}</td>
                        </tr>
                        <tr>
                            <td><b>Bairro</b></td>
                            <td>{{ order.data.address.district }}</td>
                            <td><b>Refêrencia</b></td>
                            <td>{{ order.data.address.reference }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["order"],
    computed : {
    }
}
</script>