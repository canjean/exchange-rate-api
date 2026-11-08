# Live exchange rates in Google Sheets and Excel

**Google Sheets** – paste into any cell (change the currency codes as needed):

```
=IMPORTDATA("https://canjean.com/api/rate/USD/EUR.txt")
```

If your spreadsheet uses a decimal comma (Spanish, German, French, Portuguese…), add `?decimal=comma`:

```
=IMPORTDATA("https://canjean.com/api/rate/USD/EUR.txt?decimal=comma")
```

Convert an amount directly:

```
=IMPORTDATA("https://canjean.com/api/convert/100/USD/EUR.txt")
```

**Excel for Windows (Microsoft 365):**

```
=NUMBERVALUE(WEBSERVICE("https://canjean.com/api/rate/USD/EUR.txt"), ".")
```
