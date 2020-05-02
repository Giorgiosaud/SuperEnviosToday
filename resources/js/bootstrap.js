import './bulma'
import 'animate.css/animate.min.css'

window._ = require('lodash');


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// window.axios.defaults.headers.common['Authorization'] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1IiwianRpIjoiNTY5OTc2OTQwNzI1NDk2MmIxZWIyZDA4MTFhMGZhMzczMWE5NzZmOGMxMjFlMzRkYzNlMzliMDZjZDM4YzJlMTQ4ZDA5OTAyNDU3YzM3NTUiLCJpYXQiOjE1ODgzNzc0NjksIm5iZiI6MTU4ODM3NzQ2OSwiZXhwIjoxNjE5OTEzNDY5LCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.ERkVihEVjDQnvAEoTLDwzQiJjotydxRp6qc4ac8y9YJpoHnMcCUPN_aHmJTIRvTw4xzecRtmxfzeAzPVEMJcooqhCDBg6pVX--CKleFaDPs8MIVMFrigiVtdhWyl5iRY_fwSjyjJDjBAY3t50kimP_TZ86qZ5eyOaRf-eo2r9-bh8w_ASjliigUMJvV1Ck7weKC5F-EIQRu8g5bJlAXqWJ55DWDkr6whltQo8toF6KKAT6oYUNPiFDp5sdUFwmt1DcYUuhsgJuE3xwk5EsBtjL5fj58LZUsS6Y3CgpmGfK5FHyBhubr5-uaBAxk4IoDjlFxJeYklE8CPCvI9EwvNSXS8UVhxHncZGgcKyHsXV3jFXdI_DBlkRgUBZI3vdL3bq6WH3gXVtONLreU4kQK9X5VAHhGnFlKd9Dt5BiXD9s1lwu5NCA4ri0Hy4bxPsmqa8IBsj3Ct1g1uos7XE_C2Y_c-dMa9ZmuCKQvI2lDWrAK4gH6ZJR_hG3VmxZfe861Lxk_KlhH9orrj5UQU4y_BD8RFjkSXhfAQWJ0UwHVIoakRuZrf8T-SsWs5tNNhFkY6a_s8gKc0BUPQddtIIvecEe-V3Po18q1k_gS-oACJE3GmskpyGy20MVaRSTAxEzUBWTjy_pM1G1rzMzx2l4WfZH--uBnclqvieJdIAKGXJZg';


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
