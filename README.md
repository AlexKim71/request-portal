# Портал звернень клієнтів

Вебсистема керування зверненнями клієнтів корпоративного сайту.

## Стек

- PHP 8.2+
- Laravel 11
- Blade та Bootstrap 5
- MySQL 8 (для локального тестування також можна використати SQLite)
- PHPUnit
- Laravel Breeze (Blade) для автентифікації

## Встановлення

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

Налаштуйте підключення до бази даних у `.env`, потім виконайте:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Сідер створює тестові облікові записи:

- менеджер: `manager@example.com` / `password123`
- клієнт: `client@example.com` / `password123`

## Перевірка

```bash
php artisan test
npm run build
```

Feature-тести використовують SQLite in-memory, тому для них має бути встановлено
розширення PHP `pdo_sqlite`.