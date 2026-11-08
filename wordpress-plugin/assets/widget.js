/* Canjean Currency Converter — front-end script bundled with the WordPress plugin. https://canjean.com/widgets
 * Turns every <div class="canjean-widget" data-from="USD" data-to="EUR" data-amount="100" data-lang="en" data-theme="light">
 * into a converter. An <a> inside the div (only when the site owner opts in) is shown as the footer link.
 * No cookies and no tracking: it only downloads https://canjean.com/api/rates.json (USD-based rates) and the flag images. */
(function () {
	'use strict';
	var ORIGIN = 'https://canjean.com';
	var API = ORIGIN + '/api/rates.json';
	var TXT = {
		en: { title: 'Currency converter', amount: 'Amount', result: 'Converted to', swap: 'Swap currencies', updated: 'Updated', loading: 'Loading rates…', error: 'Rates unavailable right now' },
		es: { title: 'Conversor de divisas', amount: 'Cantidad', result: 'Equivale a', swap: 'Intercambiar monedas', updated: 'Actualizado', loading: 'Cargando tipos…', error: 'Tipos no disponibles ahora mismo' },
		pt: { title: 'Conversor de moedas', amount: 'Valor', result: 'Equivale a', swap: 'Trocar moedas', updated: 'Atualizado', loading: 'Carregando cotações…', error: 'Cotações indisponíveis no momento' },
		de: { title: 'Währungsrechner', amount: 'Betrag', result: 'Entspricht', swap: 'Währungen tauschen', updated: 'Aktualisiert', loading: 'Kurse werden geladen…', error: 'Kurse derzeit nicht verfügbar' },
		fr: { title: 'Convertisseur de devises', amount: 'Montant', result: 'Équivaut à', swap: 'Inverser les devises', updated: 'Mis à jour', loading: 'Chargement des taux…', error: 'Taux indisponibles pour le moment' }
	};
	var LOCALE = { en: 'en-US', es: 'es-ES', pt: 'pt-BR', de: 'de-DE', fr: 'fr-FR' };
	var FLAG_SPECIAL = { ANG: 'cw', XCG: 'cw', XPF: 'pf', CNH: 'cn' };
	var BADGE = { BTC: '₿', XAU: 'Au', XAG: 'Ag', XPT: 'Pt', XPD: 'Pd', XDR: 'SDR' };

	var CSS = '.cjw.cjw{text-align:left;letter-spacing:normal;text-transform:none;word-spacing:normal;font-style:normal;direction:ltr}.cjw input,.cjw select,.cjw button,.cjw output{text-align:left;margin:0;min-height:0;max-width:none;line-height:normal;letter-spacing:normal;text-transform:none;box-shadow:none;outline-offset:2px}.cjw img{max-width:none;margin:0;padding:0;border:0}.cjw p,.cjw span,.cjw div,.cjw label{font-family:inherit}.cjw.cjw{--cjw-bg:#fff;--cjw-fg:#0d1630;--cjw-muted:#5a6680;--cjw-line:#e2e7f0;--cjw-field:#f4f6fb;--cjw-accent:#1554d1;--cjw-ok:#0d8a55;box-sizing:border-box;width:100%;max-width:400px;border-radius:18px;overflow:hidden;background:var(--cjw-bg);color:var(--cjw-fg);font:15px/1.4 system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;box-shadow:0 2px 4px rgba(8,37,99,.06),0 12px 32px rgba(8,37,99,.14);border:1px solid var(--cjw-line)}.cjw *{box-sizing:border-box;margin:0}.cjw.cjw-dark{--cjw-bg:#111a2e;--cjw-fg:#e8edf7;--cjw-muted:#9aa7bf;--cjw-line:#223152;--cjw-field:#16223b;--cjw-accent:#8fb2ff;--cjw-ok:#46c38c;box-shadow:0 12px 32px rgba(0,0,0,.4)}.cjw .cjw-head{display:flex;align-items:center;gap:10px;padding:12px 16px;color:#fff;background:radial-gradient(400px 120px at 90% -40%,rgba(80,160,255,.55),transparent 60%),linear-gradient(135deg,#082563 0%,#0f3fa6 55%,#1c63e6 100%)}.cjw .cjw-mark{display:inline-grid;place-items:center;width:24px;height:24px;border-radius:7px;background:linear-gradient(135deg,#fff,#d7e5ff);color:#0f3fa6;font-weight:800;font-size:13px}.cjw .cjw-title{font-weight:700;font-size:14px;letter-spacing:.01em}.cjw .cjw-body{padding:16px}.cjw .cjw-field{display:flex;align-items:center;gap:8px;background:var(--cjw-field);border:1px solid var(--cjw-line);border-radius:14px;padding:8px 8px 8px 14px;transition:border-color .15s}.cjw .cjw-field:focus-within{border-color:var(--cjw-accent)}.cjw .cjw-val{flex:1;min-width:0;display:flex;flex-direction:column}.cjw .cjw-lbl{font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--cjw-muted)}.cjw .cjw-amt,.cjw .cjw-out{width:100%;border:0;background:transparent;color:var(--cjw-fg);font:inherit;font-size:22px;font-weight:700;padding:0;outline:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.cjw .cjw-amt::-webkit-outer-spin-button,.cjw .cjw-amt::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}.cjw .cjw-amt{-moz-appearance:textfield}.cjw .cjw-cur{position:relative;flex:0 0 auto;display:flex;align-items:center;gap:7px;height:42px;padding:0 26px 0 10px;border-radius:11px;background:var(--cjw-bg);border:1px solid var(--cjw-line);font-weight:700;font-size:15px}.cjw .cjw-cur:after{content:"";position:absolute;right:10px;top:50%;width:7px;height:7px;margin-top:-5px;border:solid var(--cjw-muted);border-width:0 2px 2px 0;transform:rotate(45deg)}.cjw .cjw-cur select{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;font-size:16px}.cjw .cjw-flag{width:22px;height:22px;border-radius:50%;object-fit:cover;flex:0 0 22px;box-shadow:0 0 0 1px rgba(0,0,0,.08)}.cjw .cjw-badge{display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%;background:var(--cjw-accent);color:#fff;font-size:9px;font-weight:800;flex:0 0 22px}.cjw .cjw-mid{position:relative;height:12px}.cjw .cjw-swap{position:absolute;left:50%;top:50%;width:36px;height:36px;margin:-18px 0 0 -18px;border-radius:50%;border:1px solid var(--cjw-line);background:var(--cjw-bg);color:var(--cjw-accent);cursor:pointer;display:grid;place-items:center;box-shadow:0 2px 8px rgba(13,22,48,.12);transition:transform .2s;z-index:1;padding:0}.cjw .cjw-swap:hover{transform:rotate(180deg)}.cjw .cjw-swap svg{width:18px;height:18px}.cjw .cjw-rate{margin-top:14px;display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600}.cjw .cjw-dot{width:8px;height:8px;border-radius:50%;background:var(--cjw-ok);box-shadow:0 0 0 3px rgba(13,138,85,.18);flex:0 0 8px}.cjw .cjw-when{margin-top:2px;padding-left:16px;font-size:12px;color:var(--cjw-muted)}.cjw .cjw-foot{display:flex;justify-content:flex-end;padding:10px 16px;border-top:1px solid var(--cjw-line);font-size:12px}.cjw .cjw-foot a{color:var(--cjw-accent);text-decoration:none;font-weight:600}.cjw .cjw-foot a:hover{text-decoration:underline}';
	// (every selector is prefixed with .cjw so the host theme's CSS does not break the layout)
	var SWAP_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4v16M3 8l4-4 4 4M17 20V4m4 12-4 4-4-4"/></svg>';
	var ratesPromise = null;

	function loadRates() {
		if (!ratesPromise) {
			ratesPromise = fetch(API, { credentials: 'omit' }).then(function (r) {
				if (!r.ok) throw new Error('HTTP ' + r.status);
				return r.json();
			});
		}
		return ratesPromise;
	}

	function el(tag, cls, attrs) {
		var e = document.createElement(tag);
		if (cls) e.className = cls;
		for (var k in (attrs || {})) e.setAttribute(k, attrs[k]);
		return e;
	}

	function flagNode(code) {
		var badge = function () { var b = el('span', 'cjw-badge', { 'aria-hidden': 'true' }); b.textContent = BADGE[code] || code.slice(0, 2); return b; };
		if (BADGE[code] || (code.charAt(0) === 'X' && !FLAG_SPECIAL[code])) return badge();
		var img = el('img', 'cjw-flag', { alt: '', width: '22', height: '22', src: ORIGIN + '/assets/flags/' + (FLAG_SPECIAL[code] || code.slice(0, 2).toLowerCase()) + '.svg' });
		img.onerror = function () { if (img.parentNode) img.parentNode.replaceChild(badge(), img); };
		return img;
	}

	// Currency button: visible flag + code, with a transparent native <select> on top (accessible and mobile-friendly)
	function currencyPicker(label) {
		var box = el('span', 'cjw-cur');
		var code = el('span', 'cjw-code');
		var sel = el('select', '', { 'aria-label': label });
		box.appendChild(el('span'));
		box.appendChild(code);
		box.appendChild(sel);
		box.setCode = function (c) { sel.value = c; code.textContent = c; box.replaceChild(flagNode(c), box.firstChild); };
		sel.addEventListener('change', function () { box.setCode(sel.value); });
		box.sel = sel;
		return box;
	}

	function init(box) {
		if (box.getAttribute('data-cjw-ready')) return;
		box.setAttribute('data-cjw-ready', '1');
		var lang = TXT[box.dataset.lang] ? box.dataset.lang : 'en';
		var t = TXT[lang], locale = LOCALE[lang];
		var from = (box.dataset.from || 'USD').toUpperCase(), to = (box.dataset.to || 'EUR').toUpperCase();
		var amount = parseFloat(box.dataset.amount) > 0 ? parseFloat(box.dataset.amount) : 100;
		var credit = box.querySelector('a');

		var w = el('div', 'cjw' + (box.dataset.theme === 'dark' ? ' cjw-dark' : ''), { lang: lang });
		var head = el('div', 'cjw-head');
		var mark = el('span', 'cjw-mark', { 'aria-hidden': 'true' }); mark.textContent = 'c';
		var title = el('span', 'cjw-title'); title.textContent = t.title;
		head.appendChild(mark); head.appendChild(title);

		var body = el('div', 'cjw-body');
		var f1 = el('div', 'cjw-field'), v1 = el('label', 'cjw-val'), l1 = el('span', 'cjw-lbl');
		l1.textContent = t.amount;
		var amt = el('input', 'cjw-amt', { type: 'number', min: '0', step: 'any', inputmode: 'decimal' });
		amt.value = amount;
		v1.appendChild(l1); v1.appendChild(amt);
		var pFrom = currencyPicker(t.amount);
		f1.appendChild(v1); f1.appendChild(pFrom);

		var mid = el('div', 'cjw-mid');
		var swap = el('button', 'cjw-swap', { type: 'button', 'aria-label': t.swap, title: t.swap });
		swap.innerHTML = SWAP_SVG;
		mid.appendChild(swap);

		var f2 = el('div', 'cjw-field'), v2 = el('div', 'cjw-val'), l2 = el('span', 'cjw-lbl');
		l2.textContent = t.result;
		var out = el('output', 'cjw-out', { 'aria-live': 'polite' });
		out.textContent = '…';
		v2.appendChild(l2); v2.appendChild(out);
		var pTo = currencyPicker(t.result);
		f2.appendChild(v2); f2.appendChild(pTo);

		var rate = el('div', 'cjw-rate'), dot = el('span', 'cjw-dot', { 'aria-hidden': 'true' }), rateTxt = el('span');
		rateTxt.textContent = t.loading;
		rate.appendChild(dot); rate.appendChild(rateTxt);
		var when = el('div', 'cjw-when');

		body.appendChild(f1); body.appendChild(mid); body.appendChild(f2); body.appendChild(rate); body.appendChild(when);
		w.appendChild(head); w.appendChild(body);
		if (credit) { var foot = el('div', 'cjw-foot'); foot.appendChild(credit); w.appendChild(foot); } // no link, no empty footer
		box.innerHTML = '';
		box.appendChild(w);

		loadRates().then(function (data) {
			var rates = data.rates || {};
			Object.keys(rates).sort().forEach(function (c) {
				[pFrom.sel, pTo.sel].forEach(function (s) { var o = el('option', '', { value: c }); o.textContent = c; s.appendChild(o); });
			});
			pFrom.setCode(rates[from] ? from : 'USD');
			pTo.setCode(rates[to] ? to : 'EUR');
			var fmt = function (x) {
				var a = Math.abs(x);
				return new Intl.NumberFormat(locale, a >= 1 ? { maximumFractionDigits: 2, minimumFractionDigits: 2 } : { maximumSignificantDigits: 4 }).format(x);
			};
			var updated = '';
			try { // daily rates: date only ("2026-11-05")
				updated = /^\d{4}-\d{2}-\d{2}$/.test(data.updated)
					? new Intl.DateTimeFormat(locale, { dateStyle: 'medium', timeZone: 'UTC' }).format(new Date(data.updated + 'T12:00:00Z'))
					: new Intl.DateTimeFormat(locale, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(data.updated));
			} catch (e) {}
			when.textContent = updated ? t.updated + ' ' + updated : '';
			// Long numbers: shrink the font to the available width instead of cutting the number
			function fit(node, len) {
				var room = node.clientWidth || 200, size = 22;
				while (size > 13 && len * size * 0.6 > room) size--;
				node.style.fontSize = size + 'px';
			}
			function calc() {
				var f = pFrom.sel.value, g = pTo.sel.value, r = rates[g] / rates[f];
				var v = parseFloat(String(amt.value).replace(',', '.')) || 0;
				out.textContent = fmt(v * r) + ' ' + g;
				fit(out, out.textContent.length); fit(amt, String(amt.value).length + 4);
				rateTxt.textContent = '1 ' + f + ' = ' + fmt(r) + ' ' + g;
			}
			amt.addEventListener('input', calc);
			pFrom.sel.addEventListener('change', calc);
			pTo.sel.addEventListener('change', calc);
			swap.addEventListener('click', function () { var x = pFrom.sel.value; pFrom.setCode(pTo.sel.value); pTo.setCode(x); calc(); });
			calc();
		}).catch(function () { rateTxt.textContent = t.error; out.textContent = '—'; });
	}

	function start() {
		if (!document.getElementById('cjw-style')) {
			var st = el('style', '', { id: 'cjw-style' });
			st.textContent = CSS;
			document.head.appendChild(st);
		}
		Array.prototype.forEach.call(document.querySelectorAll('.canjean-widget'), init);
	}
	window.canjeanWidgetInit = start; // lets pages render converters added later
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
})();
