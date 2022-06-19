export default {
    'default': 'Erro de validação no campo %s',
    required: '%s é obrigatório',
    'enum': '%s deve ser um destes: %s',
    whitespace: '%s não pode ficar em branco',
    date: {
      format: 'A data de %s (%s) é inválida para o formato %s',
      parse: 'A data de %s não pôde ser tratada, %s é inválido',
      invalid: 'A data de %s (%s) é inválida'
    },
    types: {
      string: '%s não é %s',
      method: '%s não é %s (function)',
      number: '%s deve ser um número',
      date: '%s não é %s',
      boolean: '%s não é %s',
      float: '%s não é %s',
      array: '%s não é uma %s',
      object: '%s não é um %s',
      integer: '%s não é uma %s',
      regexp: '%s is not a valid %s',
      email: 'O campo %s não é um %s válido',
      url: '%s is not a valid %s',
      hex: '%s is not a valid %s'
    },
    string: {
      len: '%s deve ter exatamente %s caracteres',
      min: '%s deve ter ao menos %s caracteres',
      max: '%s não pode ser maior que %s caracteres',
      range: '%s deve ser entre %s e %s caracteres'
    },
    number: {
      len: '%s não pode ser igual a %s',
      min: '%s não pode ser menos que %s',
      max: '%s não pode ser maior que %s',
      range: '%s deve ser entre %s e %s'
    },
    array: {
      len: '%s deve ter exatamente %s campos',
      min: '%s deve ter ao menos %s campos',
      max: '%s deve ter no máximo %s campos',
      range: '%s deve ser entre %s e %s campos'
    },
    pattern: {
      mismatch: 'O valor de %s (%s) não bate com nosso formato %s'
    },
    clone: function clone() {
      var cloned = JSON.parse(JSON.stringify(this));
      cloned.clone = this.clone;
      return cloned;
    }
  }