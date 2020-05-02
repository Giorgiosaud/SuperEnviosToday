import currencyLib from 'currency.js';

const formatOptions = {
    symbol: '$', precision: 2, separator: ',', decimal: '.', formatWithSymbol: true,
};

export default function (value, options = formatOptions) {
    const amount = parseFloat(value);
    const c = currencyLib(amount, options);
    return c.format();
}
