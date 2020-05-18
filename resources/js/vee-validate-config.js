/* eslint-disable camelcase */
/* eslint-disable no-underscore-dangle */

/* Config file for VeeValidate */
/* Import and extend the rules you need to use. */
import {
    extend,
    configure
} from 'vee-validate';
import {
    required,
    required_if,
    min,
    email,
    numeric,
    min_value,
    max_value,
    confirmed,
    length,
    alpha_dash
} from 'vee-validate/dist/rules';

extend('required', required);
extend('required_if', required_if);
extend('confirmed', confirmed);
extend('alpha_dash', alpha_dash);
extend('email', email);
extend('numeric', numeric);
extend('min_value', min_value);
extend('min', min);
extend('max_value', max_value);
extend('length', length);

configure({
    classes: {
        valid: 'is-success',
        invalid: 'is-danger',
    }
})
