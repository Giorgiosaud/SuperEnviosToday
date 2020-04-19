import Vue from 'vue';

import {library} from '@fortawesome/fontawesome-svg-core';
import {
    faEnvelope,
    faExclamationTriangle,
    faPassport,
    faCheck,
    faPhone,
    faKey,
    faAddressBook,
    faChevronLeft,
    faSadTear,
    faChevronRight
} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(
    faEnvelope,
    faExclamationTriangle,
    faPassport,
    faCheck,
    faKey,
    faChevronLeft,
    faChevronRight,
    faPhone,
    faAddressBook,
    faSadTear

);


Vue.component('font-awesome-icon', FontAwesomeIcon);
