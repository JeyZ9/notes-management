### ขั้นตอนการติดตั้ง JWT Token ในโปรเจกต์

1.  **ติดตั้งแพ็กเกจ JWT**: ใช้ Composer เพื่อติดตั้งแพ็กเกจ JWT สำหรับ Laravel
    
    ```bash
    composer require tymon/jwt-auth
    ```

2.  **ตั้งค่า Provider และ Alias**: เพิ่ม Service Provider และ Facade ในไฟล์ `config/app.php`
    
    ```php
    'providers' => [
        // ...
        Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ],

    'aliases' => [
        // ...
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
    ],
    ```

3.  **เผยแพร่ไฟล์การตั้งค่า**: รันคำสั่ง Artisan เพื่อเผยแพร่ไฟล์ `jwt.php`
    
    ```bash
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

4.  **สร้าง Secret Key**: สร้าง Secret Key สำหรับ JWT โดยใช้คำสั่ง Artisan
    
    ```bash
    php artisan jwt:secret
    ```
    คำสั่งนี้จะสร้างค่า `JWT_SECRET` ในไฟล์ `.env` อัตโนมัติ

5.  **ตรวจสอบไฟล์ .env**: ตรวจสอบให้แน่ใจว่ามีบรรทัด `JWT_SECRET=...` อยู่ในไฟล์ `.env`

6.  **พร้อมใช้งาน JWT ในโปรเจกต์**: สามารถใช้งาน JWT สำหรับการ Authentication ได้ทันที