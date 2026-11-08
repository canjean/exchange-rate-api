// Canjean Exchange Rate API (browser or Node 18+). CORS is enabled, no key needed.
// Docs: https://canjean.com/exchange-rate-api

const rate = await fetch('https://canjean.com/api/rate/EUR/USD.json').then(r => r.json());
console.log(`1 EUR = ${rate.rate} USD (${rate.date})`);

const conv = await fetch('https://canjean.com/api/convert/250/GBP/JPY.json').then(r => r.json());
console.log(`250 GBP = ${conv.result.toFixed(2)} JPY`);

const all = await fetch('https://canjean.com/api/rates/USD.json').then(r => r.json());
console.log(`1 USD = ${all.rates.BRL} BRL`);
