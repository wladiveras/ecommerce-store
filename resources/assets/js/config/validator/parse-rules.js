
import AsyncValidator from 'async-validator'
import Validators from 'async-validator/es/validator'
import * as util from 'async-validator/es/util'
import rules from 'async-validator/es/rule/';
import { isEmptyValue } from 'async-validator/es/util';

const files = require.context('./rules', true, /(\/)(?!.*\/)(?!-.*$).*\.js$/i)
files.keys().map(key => {
    let vName = key.split('/').pop().split('.')[0]

    rules[vName] = files(key).default

    Validators[vName] = function (rule, value, callback, source, options) {
        var ruleType = rule.type;
        var errors = [];
        var validate = rule.required || !rule.required && source.hasOwnProperty(rule.field);
        if (validate) {
            if (isEmptyValue(value, ruleType) && !rule.required) {
                return callback();
            }
            rules.required(rule, value, source, errors, options, ruleType);
            if (!isEmptyValue(value, ruleType)) {
                rules[vName](rule, value, source, errors, options);
            }
        }
        callback(errors);
    }
    //files(key).default

})
