# Canjean Exchange Rate API

Free exchange rate API for **150+ currencies (plus BTC)**, with **no API key and no sign-up**. JSON, CSV or plain text, CORS enabled, and ready for Google Sheets and Excel.

By [Canjean](https://canjean.com), a free currency converter in English, Spanish, Portuguese, German and French.

**Full documentation:** https://canjean.com/exchange-rate-api · **OpenAPI 3:** [`openapi.json`](openapi.json)

## Endpoints

| URL | Returns |
|---|---|
| `https://canjean.com/api/rate/USD/EUR.txt` | The rate as a plain number: `0.896901` |
| `https://canjean.com/api/rate/USD/EUR.json` | The rate in JSON, with the date |
| `https://canjean.com/api/convert/100/USD/EUR.txt` | An amount converted, as a plain number |
| `https://canjean.com/api/convert/100/USD/EUR.json` | An amount converted, with rate and result in JSON |
| `https://canjean.com/api/rates/EUR.json` | All rates against a base currency, in JSON |
| `https://canjean.com/api/rates/USD.csv` | All rates as CSV (`currency,rate`) |

Use ISO 4217 codes (USD, EUR, GBP, JPY, MXN, BRL, INR, IDR…) plus BTC. Add `?decimal=comma` to the `.txt` endpoints for spreadsheets that use a decimal comma.

```json
GET https://canjean.com/api/rate/USD/EUR.json

{"from":"USD","to":"EUR","rate":0.89690055,"date":"2026-11-08","source":"https://canjean.com","docs":"https://canjean.com/exchange-rate-api"}
```

## Examples

- [curl](examples/curl.sh)
- [JavaScript (fetch)](examples/fetch.js)
- [Python](examples/python.py)
- [PHP](examples/php.php)
- [Google Sheets and Excel](examples/spreadsheets.md)

## Terms of use

- Free for personal and commercial use.
- Limit: 120 requests per minute per IP. Responses are cached for an hour, so there is no need to call more often.
- Daily reference rates from a public-domain (CC0) dataset compiled from central banks and markets. They are indicative mid-market rates, not suitable for trading.
- Please credit Canjean with a link in your app, documentation or README:

```markdown
[Exchange rates by Canjean](https://canjean.com)
```

## Converter widget

Embed a live currency converter on any website. Generate the code with your currencies, language and theme at https://canjean.com/widgets, or use this:

```html
<div class="canjean-widget" data-from="USD" data-to="EUR" data-amount="100" data-lang="en" data-theme="light"><a href="https://canjean.com/convert-USD-to-EUR">USD to EUR by Canjean</a></div>
<script src="https://canjean.com/assets/js/widget.js" async></script>
```

## WordPress plugin

The [`wordpress-plugin`](wordpress-plugin) folder contains **Canjean Currency Converter**, which adds the converter as a widget, a shortcode (`[canjean_converter from="USD" to="EUR"]`) and a settings page. GPLv2 or later.

## Open data

Canjean also publishes open datasets under CC BY 4.0:

- **Minimum wages and iPhone prices by country** – [Zenodo, DOI 10.5281/zenodo.24039147](https://doi.org/10.5281/zenodo.24039147) · [Kaggle](https://www.kaggle.com/datasets/canjean/minimum-wages-and-iphone-prices-by-country) · interactive versions: [minimum wage by country](https://canjean.com/minimum-wage), [iPhone Price Index](https://canjean.com/iphone-price-index)

## License

Code examples and docs: [MIT](LICENSE). WordPress plugin: GPLv2 or later.

Questions or corrections: contact@canjean.com
