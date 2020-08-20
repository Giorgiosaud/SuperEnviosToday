import Vue from 'vue';

import { library } from '@fortawesome/fontawesome-svg-core';
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
  faSignOutAlt,
  faAddressBook,
  faChevronLeft,
  faSadTear,
  faPeopleArrows,
  faCoins,
  faPiggyBank,
  faFileSignature,
  faSignInAlt,
  faChevronRight,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(
  faEnvelope,
  faSearchDollar,
  faExclamationTriangle,
  faFileSignature,
  faSignInAlt,
  faPassport,
  faUpload,
  faCheck,
  faKey,
  faSignOutAlt,
  faCommentDollar,
  faChevronLeft,
  faChevronRight,
  faPhone,
  faCoins,
  faPiggyBank,
  faAddressBook,
  faSadTear,
  faPeopleArrows,

);

Vue.component('font-awesome-icon', FontAwesomeIcon);
