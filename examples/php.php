<?php
// Canjean Exchange Rate API: no key, no sign-up. Docs: https://canjean.com/exchange-rate-api

$rate = json_decode(file_get_contents('https://canjean.com/api/rate/USD/EUR.json'), true);
echo "1 USD = {$rate['rate']} EUR ({$rate['date']})\n";

$conv = json_decode(file_get_contents('https://canjean.com/api/convert/100/EUR/GBP.json'), true);
printf("100 EUR = %.2f GBP\n", $conv['result']);
