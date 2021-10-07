import currencyLib from 'currency.js';

const formatOptions = {
  symbol: '$', precision: 8, separator: '.', decimal: ',', formatWithSymbol: true,
};

export default function currencyFilter(value, options = formatOptions) {
  let amount = parseFloat(value);
  if (Number.isNaN(amount)) {
    amount = 0;
  }
  const c = currencyLib(amount, options);
  return c.format();
}
