import './bulma';
import 'animate.css/animate.min.css';

window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
function getCookie(name) {
  if (!document.cookie) {
    return null;
  }

  const xsrfCookies = document.cookie.split(';')
    .map((c) => c.trim())
    .filter((c) => c.startsWith(`${name}=`));

  if (xsrfCookies.length === 0) {
    return null;
  }
  return decodeURIComponent(xsrfCookies[0].split('=')[1]);
}

const csrfToken = getCookie('XSRF-TOKEN');
window.$http = {

  setupHeaders: (newHeaders) => {
    const myHeaders = new Headers();

    myHeaders.append('credentials', 'same-origin');
    myHeaders.append('Accept', 'application/json');
    myHeaders.append('Content-Type', 'application/json');
    myHeaders.append('X-Requested-With', 'XMLHttpRequest');
    myHeaders.append('X-XSRF-TOKEN', csrfToken);
    if (newHeaders) {
      newHeaders.forEach((key) => {
        myHeaders.append(key, newHeaders[key]);
      });
    }
    return myHeaders;
  },
  noDataFetch(url, headersConf, config) {
    const myHeaders = this.setupHeaders(headersConf);
    return fetch(url, {
      headers: myHeaders,
      ...config,
    });
  },
  dataFetch(url, data, headersConf, config) {
    const myHeaders = this.setupHeaders(headersConf);
    return fetch(url, {
      headers: myHeaders,
      method: 'POST',
      body: JSON.stringify(data),
      ...config,
    });
  },
  get(url, config = {}, headersConf) {
    // eslint-disable-next-line no-param-reassign
    config.method = 'GET';
    let params = '';
    if (config.params) {
      params += '?';
      const paramsKeys = Object.keys(config.params);
      paramsKeys.forEach((paramKey) => {
        params += `${paramKey}=${config.params[paramKey]}&`;
      });
      params = params.replace(/&$/g, '');
    }
    return this.noDataFetch(`${url}${params}`, headersConf, config);
  },
  delete(url, headersConf, config = {}) {
    // eslint-disable-next-line no-param-reassign
    config.method = 'DELETE';
    return this.noDataFetch(url, headersConf, config);
  },
  post(url, data = {}, headersConf, config = {}) {
    // eslint-disable-next-line no-param-reassign
    config.method = 'POST';
    return this.dataFetch(url, data, headersConf, config);
  },
  put(url, data = {}, headersConf, config = {}) {
    // eslint-disable-next-line no-param-reassign
    config.method = 'PUT';
    return this.dataFetch(url, data, headersConf, config);
  },
  patch(url, data = {}, headersConf, config = {}) {
    // eslint-disable-next-line no-param-reassign
    config.method = 'PATCH';
    return this.dataFetch(url, data, headersConf, config);
  },
  custom(url, data = {}, headersConf, config) {
    return this.dataFetch(url, data, headersConf, config);
  },
};
// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
