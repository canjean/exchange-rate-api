=== Canjean Currency Converter ===
Contributors: canjean
Tags: currency converter, exchange rates, currency, widget, shortcode
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A free live currency converter for posts, pages and sidebars: 150+ currencies, 5 languages, light or dark. No API key, no cookies.

== Description ==

Add a clean, mobile-friendly currency converter to your site in a minute. It is made for travel blogs, expat guides, online shops and anyone who writes about prices in other currencies.

* **150+ currencies**, including Bitcoin, with flags.
* **Live calculation** as the visitor types; swap button included.
* **5 languages:** English, Spanish, Portuguese, German and French. By default it follows your site's language.
* **Light and dark themes** that fit into any design and don't inherit broken styles from the theme.
* **No API key, no account, no cookies and no tracking.**
* **Lightweight:** one small script, loaded only on pages that show the converter.

= How to add it =

* **Shortcode:** `[canjean_converter]` anywhere in a post or page (or in a Shortcode block).
* **Widget:** Appearance → Widgets → "Canjean Currency Converter" for sidebars and footers.

Each converter can have its own currencies, amount, language and theme:

`[canjean_converter from="GBP" to="EUR" amount="50" lang="auto" theme="dark"]`

* `from`, `to`: three-letter currency codes (USD, EUR, JPY, THB, BTC…). Visitors can choose any other currency in the converter.
* `amount`: the starting amount.
* `lang`: `auto` (your site's language), `en`, `es`, `pt`, `de` or `fr`.
* `theme`: `light` or `dark`.

Defaults for all converters are in Settings → Currency Converter.

= Credit link (optional) =

Nothing is added to your site unless you choose it. After activation the plugin asks you once whether to show a small "Exchange rates by Canjean" link under the converter; answer "No thanks" or close the notice and it will not ask again. The link is off by default, the converter works exactly the same without it, and you can change your choice at any time in Settings → Currency Converter.

== External services ==

This plugin shows exchange rates from Canjean (https://canjean.com), a free currency conversion service. Without it the converter cannot calculate anything.

* **What is sent and when:** when a visitor opens a page that shows the converter, their browser downloads the rates file `https://canjean.com/api/rates.json` and the currency flag images from `https://canjean.com/assets/flags/`. As with any web request, the server receives the visitor's IP address and browser user agent. No cookies are set, no personal data is sent by the plugin, and nothing is used for tracking or advertising.
* **Nothing is sent from your WordPress server** and no data about your site or its users is collected.
* **Rates** are indicative mid-market rates (USD base, updated daily) and are not an offer to buy or sell currency.
* Privacy policy: https://canjean.com/privacy-policy
* About the service and its data sources: https://canjean.com/about

== Installation ==

1. Install from Plugins → Add New (search for "Canjean Currency Converter") or upload the zip, then activate it.
2. Optional: set your default currencies, language and theme in Settings → Currency Converter.
3. Add `[canjean_converter]` to a post or page, or add the widget to a sidebar.

== Frequently Asked Questions ==

= Is it free? Do I need an API key? =

Yes, it is free, and no key or account is needed.

= How often are the rates updated? =

The rates are refreshed daily from public central bank and market data. The converter shows the date of the rates it is using.

= Can I show several converters on one page? =

Yes. Use the shortcode as many times as you like, each with its own currencies.

= Does it slow down my site? =

No. The script is small, loads in the footer and is only added to pages that show a converter.

= Does it work with page builders? =

Yes, anywhere you can place a shortcode (Gutenberg Shortcode block, Elementor, Divi, classic editor, text widgets).

= Does it add links to my site? =

Only if you say yes to the optional credit link (in the one-time question after activation or in the settings). It is off by default.

== Screenshots ==

1. The converter in the light theme.
2. The dark theme.
3. Settings → Currency Converter.

== Changelog ==

= 1.0.0 =
* First release: shortcode, widget, settings page, 5 languages, light and dark themes, optional credit link (off by default, asked once after activation).

== Upgrade Notice ==

= 1.0.0 =
First release.
