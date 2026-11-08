#!/bin/sh
# Canjean Exchange Rate API: no key, no sign-up. Docs: https://canjean.com/exchange-rate-api

# Rate as a plain number
curl https://canjean.com/api/rate/USD/EUR.txt

# Rate in JSON, with the date
curl https://canjean.com/api/rate/USD/MXN.json

# Convert an amount
curl https://canjean.com/api/convert/100/USD/EUR.json

# All rates against a base currency, as JSON or CSV
curl https://canjean.com/api/rates/EUR.json
curl https://canjean.com/api/rates/USD.csv
