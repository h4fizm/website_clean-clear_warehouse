# 🧪 API TESTING GUIDE

## 📋 Prerequisites

1. **Login terlebih dahulu** untuk mendapatkan authentication token
2. **Start Laravel server**: `php artisan serve`
3. **API Base URL**: `http://localhost:8000/api`

## 🔐 Authentication Setup

### Step 1: Login dulu via web
```bash
# Buka browser ke http://localhost:8000/login
# Login dengan credentials yang ada
```

### Step 2: Dapatkan CSRF Token (jika menggunakan web middleware)
```bash
curl -c cookies.txt http://localhost:8000/login
```

### Step 3: Gunakan cookie untuk authenticated requests
```bash
curl -b cookies.txt http://localhost:8000/api/items
```

---

## 🚀 TESTING EXAMPLES

### 1. **Regions API**

#### Get All Regions
```bash
curl -X GET "http://localhost:8000/api/regions" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -b cookies.txt
```

#### Get Region by ID
```bash
curl -X GET "http://localhost:8000/api/regions/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Plants by Region
```bash
curl -X GET "http://localhost:8000/api/regions/1/plants" \
     -H "Accept: application/json" \
     -b cookies.txt
```

---

### 2. **Plants API**

#### Get All Plants
```bash
curl -X GET "http://localhost:8000/api/plants" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Plant by ID
```bash
curl -X GET "http://localhost:8000/api/plants/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

---

### 3. **Items API**

#### Get All Items
```bash
curl -X GET "http://localhost:8000/api/items" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Item by ID
```bash
curl -X GET "http://localhost:8000/api/items/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Items by Category
```bash
curl -X GET "http://localhost:8000/api/items/category/Baik" \
     -H "Accept: application/json" \
     -b cookies.txt

# Test kategori lain:
# /api/items/category/Baru
# /api/items/category/Rusak
# /api/items/category/Afkir
```

---

### 4. **Stocks API**

#### Get Current Stocks
```bash
curl -X GET "http://localhost:8000/api/stocks/current" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Stocks by Location
```bash
curl -X GET "http://localhost:8000/api/stocks/current/location/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Stocks by Item
```bash
curl -X GET "http://localhost:8000/api/stocks/current/item/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Initial Stocks
```bash
curl -X GET "http://localhost:8000/api/stocks/initial" \
     -H "Accept: application/json" \
     -b cookies.txt
```

---

### 5. **Transaction Logs API**

#### Get All Transaction Logs
```bash
curl -X GET "http://localhost:8000/api/transactions/logs" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Transactions by Date Range
```bash
curl -X GET "http://localhost:8000/api/transactions/logs/date-range?start_date=2025-01-01&end_date=2025-12-31" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Transactions by Movement Type
```bash
curl -X GET "http://localhost:8000/api/transactions/logs/type/Penerimaan" \
     -H "Accept: application/json" \
     -b cookies.txt

# Test tipe lain:
# /api/transactions/logs/type/Penyaluran
# /api/transactions/logs/type/Transaksi Sales
# /api/transactions/logs/type/Pemusnahan
```

---

### 6. **Destruction Submissions API**

#### Get All Destruction Submissions
```bash
curl -X GET "http://localhost:8000/api/destruction-submissions" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Submissions by Status
```bash
curl -X GET "http://localhost:8000/api/destruction-submissions/status/PROSES" \
     -H "Accept: application/json" \
     -b cookies.txt

# Test status lain:
# /api/destruction-submissions/status/DONE
# /api/destruction-submissions/status/DITOLAK
```

---

### 7. **Destination Sales API**

#### Get All Destination Sales
```bash
curl -X GET "http://localhost:8000/api/destination-sales" \
     -H "Accept: application/json" \
     -b cookies.txt
```

---

### 8. **Dashboard API**

#### Get Stock Data
```bash
curl -X GET "http://localhost:8000/api/stock-data" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Update Stock Capacity (POST)
```bash
curl -X POST "http://localhost:8000/api/stock-capacity" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -b cookies.txt \
     -d '{
       "item_id": 1,
       "location_id": 1,
       "capacity": 1000
     }'
```

---

### 9. **Legacy API (Compatibility)**

#### Get Pusat Materials (DataTable)
```bash
curl -X GET "http://localhost:8000/api/pusat-materials" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Facility Materials
```bash
curl -X GET "http://localhost:8000/api/facility-materials/1" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Transaksi Facilities
```bash
curl -X GET "http://localhost:8000/api/transaksi-facilities" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get Aktivitas Transaksi
```bash
curl -X GET "http://localhost:8000/api/aktivitas-transaksi" \
     -H "Accept: application/json" \
     -b cookies.txt
```

#### Get UPP Materials
```bash
curl -X GET "http://localhost:8000/api/upp-materials" \
     -H "Accept: application/json" \
     -b cookies.txt
```

---

### 10. **Public API (No Authentication)**

#### Debug Route
```bash
curl -X GET "http://localhost:8000/api/pusat-materials-debug" \
     -H "Accept: application/json"
```

#### Get Regions (Public)
```bash
curl -X GET "http://localhost:8000/api/regions-public" \
     -H "Accept: application/json"
```

#### Get Items (Public)
```bash
curl -X GET "http://localhost:8000/api/items-public" \
     -H "Accept: application/json"
```

#### Get Current Stocks (Public)
```bash
curl -X GET "http://localhost:8000/api/stocks-public" \
     -H "Accept: application/json"
```

---

## 🧪 TESTING WITH PHP

### Using Laravel HTTP Client
```php
// Install: composer require guzzlehttp/guzzle

use Illuminate\Support\Facades\Http;

// Login dulu
$response = Http::post('http://localhost:8000/login', [
    'email' => 'your@email.com',
    'password' => 'yourpassword'
]);

$cookies = $response->getHeaderLine('Set-Cookie');

// Test API
$response = Http::withHeaders([
    'Accept' => 'application/json',
    'Cookie' => $cookies
])->get('http://localhost:8000/api/items');

return $response->json();
```

---

## 🔍 TROUBLESHOOTING

### Common Issues & Solutions:

#### 1. **401 Unauthorized**
```bash
# Make sure you're logged in and cookies are set
curl -c cookies.txt http://localhost:8000/login
# Then use -b cookies.txt in your requests
```

#### 2. **403 Forbidden**
```bash
# Check your permissions in the database
# Make sure your user has the required permissions
```

#### 3. **404 Not Found**
```bash
# Check if the route exists and the URL is correct
# Make sure Laravel server is running
```

#### 4. **500 Server Error**
```bash
# Check Laravel logs: storage/logs/laravel.log
# Check if database tables exist
# Run: php artisan migrate:status
```

---

## 📊 EXPECTED RESPONSES

### Successful Response Format:
```json
{
  "data": [
    {
      "id": 1,
      "nama_material": "Contoh Material",
      "kode_material": "MAT001",
      "kategori_material": "Baik",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

### Error Response Format:
```json
{
  "message": "Unauthenticated.",
  "status": 401
}
```

---

## 🚀 QUICK TEST SCRIPT

Buat file `test_api.sh` untuk testing cepat:

```bash
#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "🧪 Testing API Endpoints..."
echo "=========================="

# Test public endpoint
echo "1. Testing Public API..."
curl -s -X GET "$BASE_URL/pusat-materials-debug" | jq .
echo ""

# Login
echo "2. Logging in..."
curl -c cookies.txt -s -X POST "http://localhost:8000/login" \
     -H "Content-Type: application/json" \
     -d '{"email":"your@email.com","password":"yourpassword"}'

# Test authenticated endpoints
echo "3. Testing Regions API..."
curl -s -b cookies.txt -X GET "$BASE_URL/regions" | jq .
echo ""

echo "4. Testing Items API..."
curl -s -b cookies.txt -X GET "$BASE_URL/items" | jq .
echo ""

echo "✅ Testing Complete!"
```

Run: `chmod +x test_api.sh && ./test_api.sh`

---

## 📱 POSTMAN COLLECTION

1. Import the following JSON to Postman:
2. Set up authentication (Cookie/Session)
3. Set environment variable `{{baseUrl}} = http://localhost:8000/api`

Happy Testing! 🎉