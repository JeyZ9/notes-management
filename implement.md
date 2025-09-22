# เอกสารขั้นตอนการสร้าง CRUD API สำหรับจัดการสินค้าด้วย Laravel

เอกสารนี้อธิบายขั้นตอนการสร้าง RESTful API สำหรับการจัดการข้อมูลสินค้า (มือถือ) โดยใช้ Laravel Framework, ฐานข้อมูล SQLite, และสร้างเอกสารประกอบ API ด้วย Swagger (l5-swagger)

**หมายเหตุ**: คำสั่งบางอย่าง เช่น `php artisan key:generate`, `touch database/database.sqlite`, `php artisan migrate` และ `php artisan db:seed` อาจถูกรันโดยอัตโนมัติระหว่างการตั้งค่าสภาพแวดล้อมหรือเมื่อโปรเจกต์ถูกสร้างขึ้นครั้งแรก

## 1. การตั้งค่าโปรเจกต์และฐานข้อมูล

เริ่มต้นจากการสร้างโปรเจกต์ Laravel และตั้งค่าให้ใช้ฐานข้อมูล SQLite

### 1.1. ตั้งค่าการเชื่อมต่อฐานข้อมูล

ตรวจสอบและกำหนดค่าในไฟล์ `.env` เพื่อให้แน่ใจว่า Laravel จะใช้ `sqlite` เป็นตัวเชื่อมต่อฐานข้อมูล

```dotenv
DB_CONNECTION=sqlite
```

### 1.2. สร้าง Application Key

Laravel ต้องการ Application Key สำหรับการเข้ารหัสข้อมูล หากยังไม่มี สามารถสร้างได้ด้วยคำสั่ง:

```bash
php artisan key:generate
```

คำสั่งนี้จะสร้างคีย์ที่ไม่ซ้ำกันและบันทึกลงในไฟล์ `.env` โดยอัตโนมัติ

### 1.3. สร้างไฟล์ฐานข้อมูล

สร้างไฟล์ฐานข้อมูลเปล่าๆ ขึ้นมาในไดเรกทอรี `database/` สำหรับ SQLite

```bash
touch database/database.sqlite
```

คำสั่งนี้จะสร้างไฟล์ `database.sqlite` เปล่าๆ ขึ้นมา เพื่อให้ Laravel สามารถเชื่อมต่อและสร้างตารางได้

## 2. การสร้างตารางสินค้า (Products)

ใช้ Artisan command ของ Laravel เพื่อสร้างไฟล์ Migration สำหรับตาราง `products`

### 2.1. สร้าง Migration

```bash
php artisan make:migration create_products_table --create=products
```

### 2.2. แก้ไขไฟล์ Migration

เพิ่มคอลัมน์ `name` (ชื่อสินค้า) และ `price` (ราคา) ในไฟล์ migration ที่เพิ่งสร้างขึ้น (`database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php`)

```php
// ...
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    });
}
// ...
```

### 2.3. รัน Migration

สั่งให้ Laravel สร้างตารางในฐานข้อมูลตามโครงสร้างที่กำหนดในไฟล์ migration

```bash
php artisan migrate
```

คำสั่งนี้จะรันไฟล์ migration ทั้งหมดที่ยังไม่ได้รันในโปรเจกต์ และสร้างตาราง `products` รวมถึงตารางอื่นๆ ที่ Laravel ต้องการ

## 3. สร้าง Model และ Controller

### 3.1. สร้าง Product Model

สร้าง Eloquent Model ชื่อ `Product` เพื่อใช้เป็นตัวแทนของข้อมูลในตาราง `products`

```bash
php artisan make:model Product
```

จากนั้นกำหนด `fillable` property ใน `app/Models/Product.php` เพื่ออนุญาตให้บันทึกข้อมูลลงฟิลด์ `name` และ `price` ได้

```php
// app/Models/Product.php
class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price'];
}
```

### 3.2. สร้าง ProductController

สร้าง Controller สำหรับจัดการ Logic ของ API ทั้งหมด

```bash
php artisan make:controller ProductController --api
```

จากนั้น เพิ่มเมธอดสำหรับ CRUD operations (index, store, show, update, destroy) ใน `app/Http/Controllers/ProductController.php`

## 4. กำหนดเส้นทาง (API Routes)

### 4.1. สร้างไฟล์ API Routes

สร้างไฟล์ `routes/api.php` และกำหนด `apiResource` route เพื่อเชื่อมโยง HTTP requests ไปยังเมธอดต่างๆ ใน `ProductController`

```php
// routes/api.php
use App\Http\Controllers\ProductController;
// ...
Route::apiResource('products', ProductController::class);
```

### 4.2. ลงทะเบียน API Routes

ในไฟล์ `bootstrap/app.php` ต้องทำการเพิ่ม `api` route file เข้าไปในการตั้งค่า `withRouting`

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // เพิ่มบรรทัดนี้
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ...
```

## 5. การสร้างข้อมูลตัวอย่าง (Database Seeding)

### 5.1. สร้าง Seeder

สร้าง `ProductSeeder` เพื่อเพิ่มข้อมูลตัวอย่างลงในฐานข้อมูล

```bash
php artisan make:seeder ProductSeeder
```

แก้ไขไฟล์ `database/seeders/ProductSeeder.php` เพื่อเพิ่มข้อมูลสินค้าตัวอย่าง

### 5.2. รัน Seeder

สั่งให้ Laravel นำข้อมูลตัวอย่างเข้าสู่ฐานข้อมูล

```bash
php artisan db:seed
```

คำสั่งนี้จะรัน Seeder ที่กำหนดไว้ใน `DatabaseSeeder.php` หรือรัน Seeder ที่ระบุโดยตรง เพื่อใส่ข้อมูลเริ่มต้นลงในตาราง เช่น ข้อมูลสินค้าตัวอย่าง

## 6. การสร้างเอกสาร API ด้วย Swagger

### 6.1. ติดตั้ง l5-swagger

ใช้ Composer เพื่อติดตั้ง package `darkaonline/l5-swagger`

```bash
composer require "darkaonline/l5-swagger"
```

### 6.2. Publish ไฟล์ตั้งค่า

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### 6.3. เพิ่ม Annotations

เพิ่ม OpenAPI (Swagger) annotations ใน Controller และ Model เพื่ออธิบายรายละเอียดของ API

-   **`app/Http/Controllers/Controller.php`**: เพิ่ม `@OA\Info` เพื่อกำหนดข้อมูลโดยรวมของ API
-   **`app/Http/Controllers/ProductController.php`**: เพิ่ม `@OA\Get`, `@OA\Post`, `@OA\Put`, `@OA\Delete` ในแต่ละเมธอดเพื่ออธิบาย endpoint
-   **`app/Models/Product.php`**: เพิ่ม `@OA\Schema` และ `@OA\Property` เพื่ออธิบายโครงสร้างข้อมูลของ Product

### 6.4. สร้างเอกสาร

รันคำสั่งเพื่อสร้างไฟล์เอกสาร Swagger

```bash
php artisan l5-swagger:generate
```

## 7. ทดสอบ API และดูเอกสาร

สุดท้าย รันเซิร์ฟเวอร์เพื่อทดสอบ

```bash
php artisan serve
```

-   **API Endpoints**: `http://localhost:8000/api/products`
-   **Swagger Documentation**: `http://localhost:8000/api/documentation`

---
