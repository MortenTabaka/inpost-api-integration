# 📦 InPost Courier Shipment Creator

Projekt PHP do tworzenia przesyłek kurierskich w trybie uproszczonym za pomocą [API InPost](https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/18153501/Creating+a+shipment+in+the+simplified+mode).  
Wykorzystuje Guzzle do komunikacji HTTP i vlucas/phpdotenv do konfiguracji.

## 🚀 Wymagania

✅ PHP >= 8.1  
✅ Composer  
✅ Konto w InPost z dostępem do API  
✅ Token API i ID organizacji w InPost

---

## 🔧 Instalacja

Utworzenie katalogu i instalacja bibliotek
```bash
git clone git@github.com:MortenTabaka/marcin-tabaka-focus-garden-recrutement-task.git
cd marcin-tabaka-focus-garden-recrutement-task
composer install
```

Uzupełnij zmienne środowiskowe w pliku `.env`

## 🚀 Użycie
```bash
php create_shipment.php
```
