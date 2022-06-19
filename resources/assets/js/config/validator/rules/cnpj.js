import * as util from "async-validator/es/util"

function calc_digitos_posicoes(digitos, posicoes = 10, soma_digitos = 0) {
    digitos = digitos.toString()
    for (var i = 0; i < digitos.length; i++) {
        soma_digitos = soma_digitos + (digitos[i] * posicoes)
        posicoes--
        if (posicoes < 2) {
            posicoes = 9
        }
    }
    soma_digitos = soma_digitos % 11
    if (soma_digitos < 2) {
        soma_digitos = 0
    } else {
        soma_digitos = 11 - soma_digitos
    }
    var cpf = digitos + soma_digitos
    return cpf
}

function valida_cnpj(valor) {
    valor = valor.toString()
    valor = valor.replace(/[^0-9]/g, '')
    var cnpj_original = valor
    var primeiros_numeros_cnpj = valor.substr(0, 12)
    var primeiro_calculo = calc_digitos_posicoes(primeiros_numeros_cnpj, 5)
    var segundo_calculo = calc_digitos_posicoes(primeiro_calculo, 6)
    var cnpj = segundo_calculo
    if (cnpj === cnpj_original) {
      return true
    }
    return false

  }

export default function (rule, value, source, errors, options) {
    if (!valida_cnpj(value)) {
        errors.push("CNPJ inválido");
    }
}