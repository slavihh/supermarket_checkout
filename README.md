# Business Logic
### 1. Input
The checkout flow starts from a simple string, e.g:
```
AABBC
```
This is handled by the Form - App\Form\CheckoutType and validations are applied.

If the form is valid, the controller calls:
```
$checkoutService->checkout($itemsString)
```

### 2. Parsing the input

The checkout method in the CheckoutService firstly calls the parser which is referenced as a service as the parser can be changed.
The data we receive is a hash map which shows us how many skus are inside the string. For example:
```
'AAABBD' → ['A' => 3, 'B' => 2, 'D' => 1]
```

### 3. Loading the products
CheckoutService then loads the products and if any sku from the input is missing, it throws and exception.

### 4. Line Price Calculation
For each sku/quantity pair, CheckoutService uses PriceCaclculatorService which validates the quantity and applies promotions and returns a line item for the receipt.

### 5. Building the sale
When everything is calculated back in the checkout service we create a new Sale entity with publicId, createdAt, totalPrice (in cents) is created. For each LineItemPriceResult a new SaleItem is created with references to product, and quantity and linePrice data.

# Project Setup (Docker)

## 1. Start Docker services

```
cp .env .env.dev.local
```
Set the database URL (match your docker-compose.yml):
```
DATABASE_URL="mysql://app:app_password@db:3306/app_db?serverVersion=8.4&charset=utf8mb4"
```

## 2. Install Dependencies
```
docker compose exec php composer install
```

## 3. Database Setup
Create the database:
```
docker compose exec php bin/console doctrine:database:create --if-not-exists
```
Run migrations:
```
docker compose exec php bin/console doctrine:migrations:migrate
```
Load fixtures:
```
docker compose exec php bin/console doctrine:fixtures:load -n
```

# Access the app

Open your browser and load http://localhost:8080
