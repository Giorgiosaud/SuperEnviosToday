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
configure({
  classes: {
    valid: 'is-success',
    invalid: 'is-danger',
  },
});
