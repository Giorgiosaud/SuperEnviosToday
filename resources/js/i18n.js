/* Config file for i18n plugin */

import Vue from 'vue';
import VueI18n from 'vue-i18n';
import messages from './messages';

// The default language is spanish.
// If you add another language to your project be sure to:
// STEP 1: import the validations messages from vee-validate.

Vue.use(VueI18n);

// Get page language from modyo, change to your needs
const LANG = window?.locale ?? 'es';

function loadLocaleMessages() {
  return messages;
}

export default new VueI18n({
  locale: LANG,
  fallbackLocale: 'es',
  messages: loadLocaleMessages(),
});
