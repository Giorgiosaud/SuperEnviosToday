import currencyLib from 'currency.js';

const formatOptions = {
    symbol: '$', precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
};

export default function (value, options = formatOptions) {
    let amount = parseFloat(value);
    if(isNaN(amount)){
       amount=0
    }
    const c = currencyLib(amount, options);
    return c.format();
}
