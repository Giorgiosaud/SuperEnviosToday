/* eslint-disable camelcase */
/* eslint-disable no-underscore-dangle */

/* Config file for VeeValidate */
/* Import and extend the rules you need to use. */
import {
  extend,
  configure,
} from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';

Object.keys(rules).forEach((rule) => {
  extend(rule, rules[rule]);
});
extend('decimals', {
  validate: (value, { decimals = '*', separator = '.' } = {}) => {
    if (value === null || value === undefined || value === '') {
      return {
        valid: false,
      };
    }
    if (Number(decimals) === 0) {
      return {
        valid: /^-?\d*$/.test(value),
      };
    }
    const regexPart = decimals === '*' ? '+' : `{1,${decimals}}`;
    const regex = new RegExp(`^[-+]?\\d*(\\${separator}\\d${regexPart})?([eE][-]?\\d+)?$`);

    return {
      valid: regex.test(value),
    };
  },
  message: 'El campo {_field_} debe contener solamente valores decimales',
});
extend('balance', {
  params: ['balance'],
  validate: (value, { balance }) => balance >= 0,
  message: 'El campo {_field_} tiene un monto invalido',
});
configure({
  classes: {
    valid: 'is-success',
    invalid: 'is-danger',
  },
});
