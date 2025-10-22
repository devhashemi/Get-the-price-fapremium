# 💰 Get the Price — FaPremium.ir

> Fetches live Telegram Premium and Stars pricing directly from [**FaPremium.ir**](https://fapremium.ir/store/) — the official Persian platform for secure and instant Telegram subscriptions.

---

## 🧩 Overview

**Get-the-price-fapremium** is a simple open-source PHP API that reads and parses real-time pricing data from  
[`https://fapremium.ir/store/`](https://fapremium.ir/store/)  
and returns it as clean JSON.

It uses public structured data (JSON-LD) and internal JavaScript objects (`plansData`) from FaPremium’s store page to provide the latest plan prices, including **1-month, 3-month, 6-month, 12-month Premium subscriptions**, and **Telegram Stars packages**.

---

## 🌐 Live Example


**Example Response:**
```json
{
  "ok": true,
  "source_url": "https://fapremium.ir/store/",
  "updated_at": "2025-10-22T15:00:00+01:00",
  "premium": [
    {"id":"1m","label":"اشتراک ۱ ماهه تلگرام پرمیوم","price":5890000,"currency":"IRR"},
    {"id":"3m","label":"اشتراک ۳ ماهه تلگرام پرمیوم","price":16650000,"currency":"IRR"},
    {"id":"6m","label":"اشتراک ۶ ماهه تلگرام پرمیوم","price":21330000,"currency":"IRR"},
    {"id":"12m","label":"اشتراک ۱۲ ماهه تلگرام پرمیوم","price":36780000,"currency":"IRR"}
  ],
  "stars": [
    {"id":"50","label":"50 استارز","price":110000,"currency":"IRR"},
    {"id":"100","label":"100 استارز","price":220000,"currency":"IRR"},
    {"id":"200","label":"200 استارز","price":440000,"currency":"IRR"}
  ]
}
```

##⚙️ How It Works

Connects to https://fapremium.ir/store/

Extracts Premium plan data from the embedded JSON-LD (<script type="application/ld+json">)

Parses fallback data from the in-page plansData JavaScript object

Cleans and converts IRR (ریال) prices into consistent numeric format

Caches results for 10 minutes to avoid excess load

Returns a structured JSON response ready for bots or dashboards
