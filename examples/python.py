"""Canjean Exchange Rate API: no key, no sign-up. Docs: https://canjean.com/exchange-rate-api"""
import requests

rate = requests.get("https://canjean.com/api/rate/USD/EUR.json", timeout=10).json()
print(f"1 USD = {rate['rate']} EUR ({rate['date']})")

conv = requests.get("https://canjean.com/api/convert/599/MYR/IDR.json", timeout=10).json()
print(f"599 MYR = {conv['result']:,.0f} IDR")

rates = requests.get("https://canjean.com/api/rates/USD.json", timeout=10).json()["rates"]
print(f"1 USD = {rates['COP']} COP")
