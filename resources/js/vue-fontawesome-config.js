import Vue from 'vue';

import {library} from '@fortawesome/fontawesome-svg-core';
import {
    faEnvelope,
    faExclamationTriangle,
    faPassport,
    faCheck,
    faUpload,
    faPhone,
    faKey,
    faAddressBook,
    faChevronLeft,
    faSadTear,
    faCoins,
    faPiggyBank,
    faChevronRight
} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(
    faEnvelope,
    faExclamationTriangle,
    faPassport,
    faUpload,
    faCheck,
    faKey,
    faChevronLeft,
    faChevronRight,
    faPhone,
    faCoins,
    faPiggyBank,
    faAddressBook,
    faSadTear

);


Vue.component('font-awesome-icon', FontAwesomeIcon);
