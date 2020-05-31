import Vue from 'vue';

import {library} from '@fortawesome/fontawesome-svg-core';
import {
    faEnvelope,
    faExclamationTriangle,
    faPassport,
    faCheck,
    faUpload,
    faPhone,
    faCommentDollar,
    faSearchDollar,
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
    faCommentDollar,
    faChevronLeft,
    faChevronRight,
    faPhone,
    faCoins,
    faPiggyBank,
    faAddressBook,
    faSadTear

);


Vue.component('font-awesome-icon', FontAwesomeIcon);
