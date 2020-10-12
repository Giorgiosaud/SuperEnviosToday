const accountsEs = require('../lang/es/accounts.php');
const authEs = require('../lang/es/auth.php');
const banksEs = require('../lang/es/banks.php');
const currenciesEs = require('../lang/es/currencies.php');
const emailEs = require('../lang/es/email.php');
const messageEs = require('../lang/es/message.php');
const paginationEs = require('../lang/es/pagination.php');
const passwordsEs = require('../lang/es/passwords.php');
const pendingTransactionsEs = require('../lang/es/pendingTransactions.php');
const ratesEs = require('../lang/es/rates.php');
const receiverEs = require('../lang/es/receiver.php');
const settingsEs = require('../lang/es/settings.php');
const transactionEs = require('../lang/es/transaction.php');
const usersEs = require('../lang/es/users.php');
const validationEs = require('../lang/es/validation.php');
const authEn = require('../lang/en/auth.php');
const paginationEn = require('../lang/en/pagination.php');
const passwordsEn = require('../lang/en/passwords.php');
const validationEn = require('../lang/en/validation.php');

export default {
  // The key format should be: 'locale.filename'.
  es: {
    accounts: accountsEs,
    auth: authEs,
    banks: banksEs,
    currencies: currenciesEs,
    email: emailEs,
    message: messageEs,
    pagination: paginationEs,
    passwords: passwordsEs,
    pendingTransactions: pendingTransactionsEs,
    rates: ratesEs,
    receiver: receiverEs,
    settings: settingsEs,
    transaction: transactionEs,
    users: usersEs,
    validation: validationEs,
  },
  en: {
    auth: authEn,
    pagination: paginationEn,
    passwords: passwordsEn,
    validation: validationEn,
  },
};
