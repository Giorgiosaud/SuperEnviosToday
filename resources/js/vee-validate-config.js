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
    min,
    email,
    numeric,
    min_value,
    max_value,
    confirmed,
    alpha_dash
} from 'vee-validate/dist/rules';

extend('required', required);
extend('confirmed', confirmed);
extend('alpha_dash', alpha_dash);
extend('email', email);
extend('numeric', numeric);
extend('min_value', min_value);
extend('min', min);
extend('max_value', max_value);
configure({
    classes: {
        valid: 'is-success',
        invalid: 'is-danger',
    }
})
