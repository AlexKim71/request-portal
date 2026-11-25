# Project Context for Review

> **Важное уточнение по идентичности проекта.** В запрошенном описании указана тема «управление номерным фондом отеля» и стек Angular + ASP.NET Core. Состав текущего репозитория этому не соответствует: здесь реализован портал клиентских обращений на PHP/Laravel с серверными Blade-шаблонами. В репозитории не обнаружены `*.csproj`, `Program.cs`, `AngularApi.csproj`, `angular.json`, `src/app`, `proxy.conf.json`, `launchSettings.json`, Angular Signals, Reactive Forms, DataAnnotations, HTTP Interceptors, `test-api.sh` или `Test-Api.ps1`. Ниже приведён фактический контекст, без приписывания проекту отсутствующих технологий. Если курсовая действительно про отель, перед ревью следует предоставить правильный репозиторий либо отдельную ветку проекта.

# 1. Общая информация и стек технологий

## Назначение

Репозиторий `request-portal` — веб-система для обработки обращений клиентов корпоративного сайта. Клиент просматривает каталог услуг, создаёт и отслеживает свои обращения; менеджер просматривает очередь, назначает себя ответственным, переводит обращения по статусам и оставляет комментарии.

Это не гостиничная система: в модели данных нет номеров, бронирований, гостей, размещения или инвентаризации номерного фонда. Тема в README репозитория — «Вебсистема керування зверненнями клієнтів корпоративного сайту».

## Фактический стек

- **Backend / web runtime:** PHP `^8.2`, Laravel Framework `^12.0` (Composer manifest). В отчёте о нагрузке указан runtime Laravel 12.72.0 / PHP 8.2.36.
- **Архитектурный стиль:** Laravel MVC, server-side rendered HTML через Blade, web routes, cookie/session authentication, Eloquent ORM.
- **Frontend:** Blade + обычные HTML-формы; Alpine.js 3 и Axios подключаются через Vite. В бизнес-шаблонах применяются Bootstrap 5 классы, а общий layout загружает Bootstrap 5.3.3 с CDN. В `resources/css/app.css` остаются Tailwind directives; это не Angular SPA.
- **Build:** Vite 7, `laravel-vite-plugin`; PostCSS/Tailwind конфигурации есть.
- **Database:** приложение настраивает SQLite, MySQL/MariaDB, PostgreSQL и SQL Server через конфигурацию Laravel; `.env.example` задаёт `DB_CONNECTION=sqlite`. Отчётный benchmark выполнялся на SQLite. Фактический локальный `.env` намеренно не читался и не включён.
- **Validation:** Laravel FormRequest / `$request->validate()` server-side; отдельного набора C# DataAnnotations или Angular Reactive Forms нет.
- **Authorization:** Laravel auth middleware, собственный middleware `manager`, Gates и Policy.
- **Testing:** PHPUnit 11; в `phpunit.xml` тестовое подключение настроено на SQLite `:memory:`.
- **Utility scripts:** Python 3, без сторонних пакетов, для нагрузочного прогона и заполнения отдельной benchmark-базы.

## Несоответствия между README и manifests

README содержит устаревшие сведения: Laravel 11, MySQL 8 и Bootstrap 5 перечислены как стек. `composer.json` требует Laravel 12, `.env.example` указывает SQLite, а шаблоны подключают Bootstrap через CDN при наличии Tailwind-инструментов в сборке. Перед отправкой на ревью README и курсовую стоит согласовать с фактическим кодом и точно описать смешанное использование стилей.

# 2. Структура проекта (дерево файлов)

Дерево ниже построено по рабочему дереву проекта. Исключены `node_modules`, `bin`, `obj`, `.git`, `.angular`, `dist`, `public`, `vendor`, runtime-кеш Laravel и локальный `.env`; содержимое секретов и runtime-логов не копируется.

```text
.
|-- app/
|   |-- Enums/
|   |   |-- RequestStatus.php
|   |   `-- UserRole.php
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Auth/
|   |   |   |   |-- AuthenticatedSessionController.php
|   |   |   |   |-- ConfirmablePasswordController.php
|   |   |   |   |-- EmailVerificationNotificationController.php
|   |   |   |   |-- EmailVerificationPromptController.php
|   |   |   |   |-- NewPasswordController.php
|   |   |   |   |-- PasswordController.php
|   |   |   |   |-- PasswordResetLinkController.php
|   |   |   |   |-- RegisteredUserController.php
|   |   |   |   `-- VerifyEmailController.php
|   |   |   |-- ClientRequestController.php
|   |   |   |-- Controller.php
|   |   |   |-- ManagerRequestController.php
|   |   |   |-- ProfileController.php
|   |   |   `-- ServiceController.php
|   |   |-- Middleware/
|   |   |   `-- EnsureManager.php
|   |   `-- Requests/
|   |       |-- Auth/
|   |       |   `-- LoginRequest.php
|   |       |-- ProfileUpdateRequest.php
|   |       |-- StoreClientRequest.php
|   |       |-- StoreComment.php
|   |       `-- UpdateRequestStatus.php
|   |-- Models/
|   |   |-- ClientRequest.php
|   |   |-- RequestHistory.php
|   |   |-- Service.php
|   |   `-- User.php
|   |-- Policies/
|   |   `-- ClientRequestPolicy.php
|   |-- Providers/
|   |   `-- AppServiceProvider.php
|   |-- Services/
|   |   `-- RequestStatusTransition.php
|   `-- View/
|       `-- Components/
|           |-- AppLayout.php
|           `-- GuestLayout.php
|-- bootstrap/
|   |-- app.php
|   `-- providers.php
|-- config/
|   |-- app.php
|   |-- auth.php
|   |-- cache.php
|   |-- database.php
|   |-- filesystems.php
|   |-- logging.php
|   |-- mail.php
|   |-- queue.php
|   |-- services.php
|   `-- session.php
|-- database/
|   |-- factories/
|   |   `-- UserFactory.php
|   |-- migrations/
|   |   |-- 0001_01_01_000000_create_users_table.php
|   |   |-- 0001_01_01_000001_create_cache_table.php
|   |   |-- 0001_01_01_000002_create_jobs_table.php
|   |   |-- 2024_01_01_000001_add_role_to_users_table.php
|   |   |-- 2024_01_01_000002_create_services_table.php
|   |   |-- 2024_01_01_000003_create_client_requests_table.php
|   |   `-- 2024_01_01_000004_create_request_histories_table.php
|   |-- seeders/
|   |   `-- DatabaseSeeder.php
|   `-- .gitignore
|-- docs/
|   |-- admin-guide.md
|   |-- api-routes.md
|   |-- Project_Context_For_Review.md
|   |-- user-guide.md
|   `-- ІС_КР_122_АІ-243_Гаврилов_ПЗ.docx
|-- resources/
|   |-- css/
|   |   `-- app.css
|   |-- js/
|   |   |-- app.js
|   |   `-- bootstrap.js
|   `-- views/
|       |-- auth/
|       |   |-- confirm-password.blade.php
|       |   |-- forgot-password.blade.php
|       |   |-- login.blade.php
|       |   |-- register.blade.php
|       |   |-- reset-password.blade.php
|       |   `-- verify-email.blade.php
|       |-- client-requests/
|       |   |-- create.blade.php
|       |   |-- index.blade.php
|       |   `-- show.blade.php
|       |-- components/
|       |   |-- application-logo.blade.php
|       |   |-- auth-session-status.blade.php
|       |   |-- danger-button.blade.php
|       |   |-- dropdown-link.blade.php
|       |   |-- dropdown.blade.php
|       |   |-- input-error.blade.php
|       |   |-- input-label.blade.php
|       |   |-- modal.blade.php
|       |   |-- nav-link.blade.php
|       |   |-- primary-button.blade.php
|       |   |-- responsive-nav-link.blade.php
|       |   |-- secondary-button.blade.php
|       |   `-- text-input.blade.php
|       |-- layouts/
|       |   |-- app.blade.php
|       |   |-- guest.blade.php
|       |   `-- navigation.blade.php
|       |-- manager/
|       |   |-- index.blade.php
|       |   `-- show.blade.php
|       |-- profile/
|       |   |-- partials/
|       |   |   |-- delete-user-form.blade.php
|       |   |   |-- update-password-form.blade.php
|       |   |   `-- update-profile-information-form.blade.php
|       |   `-- edit.blade.php
|       |-- services/
|       |   `-- index.blade.php
|       |-- dashboard.blade.php
|       `-- welcome.blade.php
|-- routes/
|   |-- auth.php
|   |-- console.php
|   `-- web.php
|-- scripts/
|   |-- load-test.py
|   `-- seed-load-test-data.py
|-- tests/
|   |-- Feature/
|   |   |-- Auth/
|   |   |   |-- AuthenticationTest.php
|   |   |   |-- EmailVerificationTest.php
|   |   |   |-- PasswordConfirmationTest.php
|   |   |   |-- PasswordResetTest.php
|   |   |   |-- PasswordUpdateTest.php
|   |   |   `-- RegistrationTest.php
|   |   |-- AvailabilityTest.php
|   |   |-- ExampleTest.php
|   |   `-- ProfileTest.php
|   |-- Unit/
|   |   |-- ExampleTest.php
|   |   `-- RequestStatusTransitionTest.php
|   `-- TestCase.php
|-- .editorconfig
|-- .env.example
|-- .gitattributes
|-- .gitignore
|-- artisan
|-- composer.json
|-- composer.lock
|-- package-lock.json
|-- package.json
|-- phpunit.xml
|-- postcss.config.js
|-- README.md
|-- storage (runtime files omitted)
|-- tailwind.config.js
`-- vite.config.js
```

# 3. Архитектура и поток данных

## Фактическая архитектура

Приложение — единый Laravel web application, не отдельный REST backend + SPA. Запрос браузера проходит через Laravel web middleware (включая cookie session и CSRF для форм), именованный маршрут в `routes/web.php`, контроллер, FormRequest/Gate/Policy и Eloquent-модели. HTML рендерится сервером через Blade; POST/PATCH формы обычно завершаются redirect и flash status/errors. `routes/api.php` в дереве нет.

```text
Браузер
  -> GET/POST/PATCH web route (routes/web.php, routes/auth.php)
  -> Laravel middleware: auth / manager / CSRF
  -> controller + FormRequest / Gate / Policy
  -> Eloquent models -> configured SQL database
  -> Blade HTML response or redirect + session flash/errors
```

У Alpine.js/Axios нет в проверенном коде отдельного API-клиента для бизнес-функций. Вызовы `HttpInterceptor` и Angular Signals/Reactive Forms отсутствуют.

## Основные сущности и связи

- `User`: учётная запись с enum-ролью `client` или `manager`; auth хранится через web session guard.
- `Service`: каталог услуг, у услуги есть `is_active`.
- `ClientRequest`: обращение с уникальным номером `REQ-YYYYMMDD-XXXXXX`, услугой, клиентом, необязательным ответственным менеджером, статусом, темой/описанием, контактными данными и причиной отклонения.
- `RequestHistory`: история изменений статуса и комментариев; `is_internal` отделяет внутренние сообщения от публичных.
- Связи и ограничения FK задаются миграциями в `database/migrations/`.

## Поток обращения и статусы

1. Посетитель просматривает `/` или `/services`; каталог показывает только активные услуги и поддерживает поиск/пагинацию.
2. Авторизованный клиент создаёт обращение через `POST /my/requests`. `StoreClientRequest` проверяет поля; контроллер задаёт клиента и начальный статус и в одной DB-транзакции создаёт запись вместе с событием истории.
3. Клиент видит только собственные записи в `/my/requests`; просмотр одной записи защищён `ClientRequestPolicy`. Публичная история исключает внутренние комментарии.
4. Middleware `manager` закрывает `/manager/*` для пользователей не-менеджеров. Менеджер может назначить себя ответственным, добавить комментарий и запросить смену статуса.
5. `RequestStatusTransition` разрешает `Нова -> В роботі`, `Нова -> Відхилена`, `В роботі -> Завершена`, `В роботі -> Відхилена`; завершённые и отклонённые статусы терминальны. При изменении статуса запись и история сохраняются в транзакции. Причина обязательна для отклонения.
6. Клиент может отменить собственное обращение только в статусе `Нова`; отмена фиксируется как `rejected` с причиной «Скасовано користувачем.» и событием истории.

## Маршруты и доступ

- Публичные: `GET /`, `GET /services`, framework health-check `GET /up`.
- Клиентские маршруты `/my/requests*`: аутентифицированная сессия; создание ограничено клиентской ролью, просмотр/отмена авторизуются Policy/Gate.
- Менеджерские `/manager/requests*`: `auth` + middleware `manager`.
- Breeze authentication endpoints: register, login, reset password, email verification, password confirmation, logout — см. `routes/auth.php`.

`docs/api-routes.md` — обзор маршрутов интерфейса, а не описание JSON REST API. Там перечислены benchmark-команды, health-check, критерии измерения и результаты.

## Валидация и ошибки: что действительно реализовано

- `StoreClientRequest`, `StoreComment`, `UpdateRequestStatus`, `ProfileUpdateRequest` — Laravel FormRequest rules; регистрация/некоторые auth actions используют `$request->validate()`.
- Запрет доступа middleware возвращает HTTP 403. Не найденная модель при implicit route binding обычно приводит к 404.
- Валидационные ошибки обычного web-запроса обрабатываются Laravel, обычно redirect назад с error bag/session input; для JSON-клиента Laravel форматировал бы JSON согласно content negotiation, но отдельного API-контракта нет.
- Недопустимый бизнес-переход статуса генерирует `ValidationException`; собственного JSON error envelope нет.
- В приложении не найдены специальные HTTP 400/409 контракты для бизнес-конфликтов; не следует описывать 400/404/409 как реализованную схему REST.

## Тесты и измерения

- `tests/Unit/RequestStatusTransitionTest.php` проверяет некоторые допустимые/запрещённые переходы статусов.
- `tests/Feature/AvailabilityTest.php` проверяет `/up` (HTTP 200 и текст `Application up`); это не проверка БД.
- `scripts/load-test.py` делает параллельные HTTP-запросы, полностью читает body, измеряет client-side latency, p50/p95/max, throughput и долю 2xx. `scripts/seed-load-test-data.py` заполняет только пустую мигрированную SQLite таблицу услуг 50 тестовыми записями.
- В `docs/api-routes.md` зафиксирован локальный запуск: 200 запросов, concurrency 10, четыре PHP worker процесса. `/up`: 200/200, p95 107,16 мс, 135,50 запросов/с; `/services`: 200/200, p95 223,91 мс, 49,42 запросов/с. Это локальный SQLite benchmark, не production SLA; CPU/RAM не фиксировались.

# 4. Исходный код бэкенда (C#)

В репозитории нет C# backend-файлов; поэтому `Program.cs`, `AngularApi.csproj`, `launchSettings.json` и DataAnnotations привести невозможно. Ниже вместо выдуманного C# приведены фактические PHP/Laravel backend-исходники: контроллеры (включая auth), модели, запросы валидации, authorization, workflow, enums, routes, миграции и тесты.

> `DatabaseSeeder.php` намеренно не копируется: он содержит демонстрационные учётные данные с паролями в исходном коде. `.env` и `storage/logs` также не читались и не включены.

## Исходные файлы из репозитория

### `app/Enums/RequestStatus.php`

```php
<?php

namespace App\Enums;

enum RequestStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Нова',
            self::IN_PROGRESS => 'В роботі',
            self::COMPLETED => 'Завершена',
            self::REJECTED => 'Відхилена',
        };
    }
}
```

### `app/Enums/UserRole.php`

```php
<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case MANAGER = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::CLIENT => 'Клієнт',
            self::MANAGER => 'Менеджер',
        };
    }
}
```

### `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(auth()->user()->getRedirectRoute());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

### `app/Http/Controllers/Auth/ConfirmablePasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
```

### `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
```

### `app/Http/Controllers/Auth/EmailVerificationPromptController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
```

### `app/Http/Controllers/Auth/NewPasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
```

### `app/Http/Controllers/Auth/PasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
```

### `app/Http/Controllers/Auth/PasswordResetLinkController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
```

### `app/Http/Controllers/Auth/RegisteredUserController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => \App\Enums\UserRole::CLIENT, // <--- Добавляем эту строку
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect($user->getRedirectRoute());
    }
}
```

### `app/Http/Controllers/Auth/VerifyEmailController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
```

### `app/Http/Controllers/ClientRequestController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\StoreComment;
use App\Models\ClientRequest;
use App\Models\RequestHistory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ClientRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ClientRequest::class);

        $query = ClientRequest::with('service')
            ->where('client_id', $request->user()->id);

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where('number', 'like', "%{$search}%");
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('client-requests.index', compact('requests'));
    }

    public function create()
    {
        Gate::authorize('create', ClientRequest::class);
        $services = Service::active()->orderBy('name')->get();
        return view('client-requests.create', compact('services'));
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->id;
        $data['status']    = RequestStatus::NEW;

        $clientRequest = DB::transaction(function () use ($data) {
            $req = ClientRequest::create($data);

            RequestHistory::create([
                'client_request_id' => $req->id,
                'user_id'           => $data['client_id'],
                'from_status'       => null,
                'to_status'         => RequestStatus::NEW,
                'is_internal'       => false,
                'comment'           => 'Звернення створено.',
            ]);

            return $req;
        });

        return redirect()
            ->route('client-requests.show', $clientRequest)
            ->with('status', 'Звернення успішно створено. Номер: ' . $clientRequest->number);
    }

    public function show(ClientRequest $clientRequest)
    {
        Gate::authorize('view', $clientRequest);
        $clientRequest->load(['service', 'histories.user', 'manager']);

        return view('client-requests.show', ['request' => $clientRequest]);
    }

    public function cancel(Request $request, ClientRequest $clientRequest)
    {
        Gate::authorize('cancel', $clientRequest);

        DB::transaction(function () use ($request, $clientRequest) {
            $old = $clientRequest->status;
            $clientRequest->update([
                'status'           => RequestStatus::REJECTED,
                'rejection_reason' => 'Скасовано користувачем.',
            ]);
            RequestHistory::create([
                'client_request_id' => $clientRequest->id,
                'user_id'           => $request->user()->id,
                'from_status'       => $old,
                'to_status'         => RequestStatus::REJECTED,
                'is_internal'       => false,
                'comment'           => 'Звернення скасовано.',
            ]);
        });

        return back()->with('status', 'Звернення скасовано.');
    }

    public function comment(StoreComment $request, ClientRequest $clientRequest)
    {
        Gate::authorize('view', $clientRequest);

        $isInternal = $request->user()->isManager() && (bool) $request->input('is_internal', false);

        RequestHistory::create([
            'client_request_id' => $clientRequest->id,
            'user_id'           => $request->user()->id,
            'from_status'       => $clientRequest->status,
            'to_status'         => $clientRequest->status,
            'is_internal'       => $isInternal,
            'comment'           => $request->validated('comment'),
        ]);

        return back()->with('status', 'Коментар додано.');
    }
}
```

### `app/Http/Controllers/Controller.php`

```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
```

### `app/Http/Controllers/ManagerRequestController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\UpdateRequestStatus;
use App\Models\ClientRequest;
use App\Models\RequestHistory;
use App\Services\RequestStatusTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientRequest::with(['service', 'client']);

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($q2) => $q2->where('email', 'like', "%{$search}%"));
            });
        }

        if ($date = $request->string('date')->trim()->toString()) {
            $query->whereDate('created_at', $date);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('manager.index', compact('requests'));
    }

    public function show(ClientRequest $clientRequest)
    {
        $clientRequest->load(['service', 'client', 'manager', 'histories.user']);

        return view('manager.show', [
            'request'     => $clientRequest,
            'allowedNext' => RequestStatusTransition::allowedNext($clientRequest->status),
        ]);
    }

    public function assign(Request $request, ClientRequest $clientRequest)
    {
        $clientRequest->update(['manager_id' => $request->user()->id]);

        return back()->with('status', 'Вас призначено відповідальним.');
    }

    public function updateStatus(UpdateRequestStatus $request, ClientRequest $clientRequest)
    {
        $data      = $request->validated();
        $newStatus = RequestStatus::from($data['status']);

        if (!RequestStatusTransition::canTransition($clientRequest->status, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Перехід зі статусу «{$clientRequest->status->label()}» у «{$newStatus->label()}» не дозволено.",
            ]);
        }

        DB::transaction(function () use ($request, $clientRequest, $newStatus, $data) {
            $old = $clientRequest->status;
            $clientRequest->update([
                'status'           => $newStatus,
                'rejection_reason' => $newStatus === RequestStatus::REJECTED ? $data['rejection_reason'] : null,
                'manager_id'       => $clientRequest->manager_id ?? $request->user()->id,
            ]);

            RequestHistory::create([
                'client_request_id' => $clientRequest->id,
                'user_id'           => $request->user()->id,
                'from_status'       => $old,
                'to_status'         => $newStatus,
                'is_internal'       => false,
                'comment'           => $data['comment'] ?? null,
            ]);
        });

        return back()->with('status', 'Статус оновлено.');
    }
}
```

### `app/Http/Controllers/ProfileController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
```

### `app/Http/Controllers/ServiceController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()->active();

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(9)->withQueryString();

        return view('services.index', compact('services'));
    }
}
```

### `app/Http/Middleware/EnsureManager.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isManager()) {
            abort(403, 'Доступ лише для менеджерів.');
        }
        return $next($request);
    }
}
```

### `app/Http/Requests/Auth/LoginRequest.php`

```php
<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
```

### `app/Http/Requests/ProfileUpdateRequest.php`

```php
<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
```

### `app/Http/Requests/StoreClientRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

    public function rules(): array
    {
        return [
            'service_id'    => ['required', 'exists:services,id'],
            'subject'       => ['required', 'string', 'min:5', 'max:200'],
            'body'          => ['required', 'string', 'min:20', 'max:5000'],
            'contact_name'  => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required'    => 'Оберіть послугу.',
            'service_id.exists'      => 'Обрана послуга недійсна.',
            'subject.required'       => 'Вкажіть тему звернення.',
            'subject.min'            => 'Тема має містити щонайменше 5 символів.',
            'body.required'          => 'Опишіть суть звернення.',
            'body.min'               => 'Опис має містити щонайменше 20 символів.',
            'contact_name.required'  => 'Вкажіть контактну особу.',
            'contact_email.required' => 'Вкажіть email для зв\'язку.',
            'contact_email.email'    => 'Некоректний формат email.',
            'contact_phone.regex'    => 'Некоректний формат телефону.',
        ];
    }
}
```

### `app/Http/Requests/StoreComment.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComment extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'comment'     => ['required', 'string', 'min:2', 'max:2000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Введіть текст коментаря.',
            'comment.min'      => 'Коментар має містити щонайменше 2 символи.',
        ];
    }
}
```

### `app/Http/Requests/UpdateRequestStatus.php`

```php
<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequestStatus extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(RequestStatus::class)],
            'rejection_reason' => [
                'nullable', 'string', 'min:10', 'max:1000',
                Rule::requiredIf(fn () => $this->input('status') === RequestStatus::REJECTED->value),
            ],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'           => 'Оберіть новий статус.',
            'rejection_reason.required' => 'Вкажіть причину відхилення.',
            'rejection_reason.min'      => 'Причина має містити щонайменше 10 символів.',
        ];
    }
}
```

### `app/Models/ClientRequest.php`

```php
<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClientRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'service_id', 'client_id', 'manager_id',
        'status', 'subject', 'body',
        'contact_name', 'contact_email', 'contact_phone',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return ['status' => RequestStatus::class];
    }

    protected static function booted(): void
    {
        static::creating(function (ClientRequest $request) {
            if (empty($request->number)) {
                $request->number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        return 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function service(): BelongsTo   { return $this->belongsTo(Service::class); }
    public function client(): BelongsTo    { return $this->belongsTo(User::class, 'client_id'); }
    public function manager(): BelongsTo   { return $this->belongsTo(User::class, 'manager_id'); }

    public function histories(): HasMany
    {
        return $this->hasMany(RequestHistory::class)->orderBy('created_at');
    }

    public function publicHistories(): HasMany
    {
        return $this->hasMany(RequestHistory::class)
            ->where('is_internal', false)
            ->orderBy('created_at');
    }
}
```

### `app/Models/RequestHistory.php`

```php
<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_request_id', 'user_id',
        'from_status', 'to_status', 'is_internal', 'comment',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => RequestStatus::class,
            'to_status'   => RequestStatus::class,
            'is_internal' => 'boolean',
        ];
    }

    public function clientRequest(): BelongsTo { return $this->belongsTo(ClientRequest::class); }
    public function user(): BelongsTo          { return $this->belongsTo(User::class); }
}
```

### `app/Models/Service.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### `app/Models/User.php`

```php
<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    public function isManager(): bool { return $this->role === UserRole::MANAGER; }
    public function isClient(): bool  { return $this->role === UserRole::CLIENT; }

    public function getRedirectRoute(): string
    {
        return match($this->role) {
            \App\Enums\UserRole::MANAGER => route('manager.index', absolute: false),
            default => route('client-requests.index', absolute: false),
        };
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'client_id');
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'manager_id');
    }
}
```

### `app/Policies/ClientRequestPolicy.php`

```php
<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\ClientRequest;
use App\Models\User;

class ClientRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClientRequest $request): bool
    {
        return $user->isManager() || $request->client_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isClient();
    }

    public function cancel(User $user, ClientRequest $request): bool
    {
        if ($user->isClient()) {
            return $request->client_id === $user->id
                && $request->status === RequestStatus::NEW;
        }
        if ($user->isManager()) {
            return in_array($request->status, [RequestStatus::NEW, RequestStatus::IN_PROGRESS], true);
        }
        return false;
    }

    public function manage(User $user): bool
    {
        return $user->isManager();
    }
}
```

### `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

### `app/Services/RequestStatusTransition.php`

```php
<?php

namespace App\Services;

use App\Enums\RequestStatus;

class RequestStatusTransition
{
    /** @var array<string, string[]> */
    private const ALLOWED = [
        RequestStatus::NEW->value => [
            RequestStatus::IN_PROGRESS->value,
            RequestStatus::REJECTED->value,
        ],
        RequestStatus::IN_PROGRESS->value => [
            RequestStatus::COMPLETED->value,
            RequestStatus::REJECTED->value,
        ],
        RequestStatus::COMPLETED->value => [],
        RequestStatus::REJECTED->value  => [],
    ];

    public static function canTransition(RequestStatus $from, RequestStatus $to): bool
    {
        return in_array($to->value, self::ALLOWED[$from->value] ?? [], true);
    }

    /** @return RequestStatus[] */
    public static function allowedNext(RequestStatus $from): array
    {
        return array_map(
            fn (string $v) => RequestStatus::from($v),
            self::ALLOWED[$from->value] ?? []
        );
    }
}
```

### `app/View/Components/AppLayout.php`

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
```

### `app/View/Components/GuestLayout.php`

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
```

### `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'manager' => \App\Http\Middleware\EnsureManager::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### `bootstrap/providers.php`

```php
<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
];
```

### `routes/auth.php`

```php
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
```

### `routes/console.php`

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

### `routes/web.php`

```php
<?php

use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\ManagerRequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController; // Не забудьте импорт
use Illuminate\Support\Facades\Route;

Route::get('/', [ServiceController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

// Добавляем маршрут dashboard, чтобы тесты Breeze не падали
Route::get('/dashboard', function () {
    if (auth()->user()->isManager()) {
        return redirect()->route('manager.index');
    }
    return redirect()->route('client-requests.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Маршруты профиля из Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Кабінет клієнта
    Route::get('/my/requests', [ClientRequestController::class, 'index'])->name('client-requests.index');
    Route::get('/my/requests/create', [ClientRequestController::class, 'create'])->name('client-requests.create');
    Route::post('/my/requests', [ClientRequestController::class, 'store'])->name('client-requests.store');
    Route::get('/my/requests/{clientRequest}', [ClientRequestController::class, 'show'])->name('client-requests.show');
    Route::post('/my/requests/{clientRequest}/cancel', [ClientRequestController::class, 'cancel'])->name('client-requests.cancel');
    Route::post('/my/requests/{clientRequest}/comment', [ClientRequestController::class, 'comment'])->name('client-requests.comment');

    // Кабінет менеджера
    Route::prefix('manager')->name('manager.')->middleware('manager')->group(function () {
        Route::get('/requests', [ManagerRequestController::class, 'index'])->name('index');
        Route::get('/requests/{clientRequest}', [ManagerRequestController::class, 'show'])->name('show');
        Route::post('/requests/{clientRequest}/assign', [ManagerRequestController::class, 'assign'])->name('assign');
        Route::patch('/requests/{clientRequest}/status', [ManagerRequestController::class, 'updateStatus'])->name('status');
        Route::post('/requests/{clientRequest}/comment', [ClientRequestController::class, 'comment'])->name('comment');
    });
});

require __DIR__.'/auth.php';
```

### `database/migrations/0001_01_01_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

### `database/migrations/0001_01_01_000001_create_cache_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
```

### `database/migrations/0001_01_01_000002_create_jobs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
```

### `database/migrations/2024_01_01_000001_add_role_to_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('client')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

### `database/migrations/2024_01_01_000002_create_services_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
```

### `database/migrations/2024_01_01_000003_create_client_requests_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('new');
            $table->string('subject');
            $table->text('body');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone', 30)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['client_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
```

### `database/migrations/2024_01_01_000004_create_request_histories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_request_id')->constrained('client_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->boolean('is_internal')->default(false);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_histories');
    }
};
```

### `database/factories/UserFactory.php`

```php
<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => UserRole::CLIENT,
        ];
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => UserRole::MANAGER]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
```

# 5. Исходный код фронтенда (Angular)

Angular-приложения в репозитории нет: отсутствуют `src/app/*.ts`, Angular templates, `src/main.ts`, `app.config.ts`, `angular.json` и `proxy.conf.json`. Фактический frontend — Blade SSR. Ниже включены бизнес-шаблоны и frontend entry/build файлы, чтобы ревьюер мог анализировать реальные формы и пользовательский поток.

## Исходные файлы из репозитория

### `resources/js/app.js`

```javascript
import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
```

### `resources/js/bootstrap.js`

```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

### `resources/css/app.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Портал звернень')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('services.index') }}">Портал звернень</a>
        <div class="d-flex gap-2 flex-wrap">
            @auth
                @if(auth()->user()->isClient())
                    <a href="{{ route('client-requests.index') }}" class="btn btn-outline-light btn-sm">Мої звернення</a>
                    <a href="{{ route('client-requests.create') }}" class="btn btn-primary btn-sm">Нове звернення</a>
                @endif
                @if(auth()->user()->isManager())
                    <a href="{{ route('manager.index') }}" class="btn btn-outline-light btn-sm">Керування</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Вийти ({{ auth()->user()->name }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Вхід</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Реєстрація</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### `resources/views/client-requests/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Нове звернення')
@section('content')
    <h1 class="mb-3">Нове звернення</h1>

    <form method="POST" action="{{ route('client-requests.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Послуга</label>
            <select name="service_id" class="form-select" required>
                <option value="">— Оберіть —</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        @selected(old('service_id', request('service_id')) == $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Тема</label>
            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control"
                   required minlength="5" maxlength="200">
        </div>
        <div class="col-12">
            <label class="form-label">Опис</label>
            <textarea name="body" class="form-control" rows="5" required minlength="20"
                      maxlength="5000">{{ old('body') }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Контактна особа</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}"
                   class="form-control" required maxlength="100">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email) }}"
                   class="form-control" required maxlength="150">
        </div>
        <div class="col-md-4">
            <label class="form-label">Телефон</label>
            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                   class="form-control" maxlength="30">
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Створити звернення</button>
            <a href="{{ route('client-requests.index') }}" class="btn btn-outline-secondary">Скасувати</a>
        </div>
    </form>
@endsection
```

### `resources/views/client-requests/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Мої звернення')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Мої звернення</h1>
        <a href="{{ route('client-requests.create') }}" class="btn btn-primary">Нове звернення</a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Пошук за номером">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Усі статуси</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary">Фільтрувати</button></div>
    </form>

    @if($requests->isEmpty())
        <div class="alert alert-info">Звернень не знайдено.</div>
    @else
        <div class="list-group">
            @foreach($requests as $req)
                <a href="{{ route('client-requests.show', $req) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $req->number }}</strong> — {{ $req->subject }}
                            <div class="small text-muted">
                                {{ $req->service->name }} · {{ $req->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <span class="badge bg-secondary align-self-start">{{ $req->status->label() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-3">{{ $requests->links() }}</div>
    @endif
@endsection
```

### `resources/views/client-requests/show.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Звернення ' . $request->number)
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="mb-1">Звернення {{ $request->number }}</h1>
            <div class="text-muted">{{ $request->subject }}</div>
        </div>
        <span class="badge bg-secondary fs-6">{{ $request->status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Деталі</div>
                <div class="card-body">
                    <p><strong>Послуга:</strong> {{ $request->service->name }}</p>
                    <p><strong>Створено:</strong> {{ $request->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Опис:</strong></p>
                    <p class="text-break">{!! nl2br(e($request->body)) !!}</p>
                    <hr>
                    <p><strong>Контактна особа:</strong> {{ $request->contact_name }}</p>
                    <p><strong>Email:</strong> {{ $request->contact_email }}</p>
                    <p><strong>Телефон:</strong> {{ $request->contact_phone ?: '—' }}</p>
                    @if($request->manager)
                        <p><strong>Відповідальний:</strong> {{ $request->manager->name }}</p>
                    @endif
                    @if($request->rejection_reason)
                        <div class="alert alert-warning mt-3">
                            <strong>Причина відхилення:</strong> {{ $request->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @can('cancel', $request)
                <form method="POST" action="{{ route('client-requests.cancel', $request) }}" class="mb-3"
                      onsubmit="return confirm('Скасувати звернення?')">
                    @csrf
                    <button class="btn btn-outline-danger w-100">Скасувати звернення</button>
                </form>
            @endcan

            <div class="card">
                <div class="card-header">Додати коментар</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('client-requests.comment', $request) }}">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" required maxlength="2000"></textarea>
                        <button class="btn btn-primary btn-sm">Надіслати</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Історія</h3>
    <div class="list-group">
        @foreach($request->publicHistories as $h)
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($h->from_status && $h->from_status !== $h->to_status)
                            <strong>{{ $h->from_status->label() }} → {{ $h->to_status->label() }}</strong>
                        @elseif($h->from_status === null)
                            <strong>Створено</strong>
                        @else
                            <strong>Коментар</strong>
                        @endif
                        — {{ $h->user->name }}
                    </div>
                    <small class="text-muted">{{ $h->created_at->format('d.m.Y H:i') }}</small>
                </div>
                @if($h->comment)<p class="mb-0 mt-1">{{ $h->comment }}</p>@endif
            </div>
        @endforeach
    </div>
@endsection
```

### `resources/views/manager/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Керування зверненнями')
@section('content')
    <h1 class="mb-3">Усі звернення</h1>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Номер, тема або email">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Усі статуси</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-auto"><button class="btn btn-primary">Фільтрувати</button></div>
    </form>

    @if($requests->isEmpty())
        <div class="alert alert-info">Звернень не знайдено.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Номер</th><th>Тема</th><th>Клієнт</th><th>Послуга</th>
                    <th>Статус</th><th>Створено</th><th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($requests as $req)
                    <tr>
                        <td>{{ $req->number }}</td>
                        <td>{{ $req->subject }}</td>
                        <td>{{ $req->client->name }}<br>
                            <small class="text-muted">{{ $req->client->email }}</small>
                        </td>
                        <td>{{ $req->service->name }}</td>
                        <td><span class="badge bg-secondary">{{ $req->status->label() }}</span></td>
                        <td>{{ $req->created_at->format('d.m.Y H:i') }}</td>
                        <td><a href="{{ route('manager.show', $req) }}" class="btn btn-sm btn-outline-primary">Відкрити</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $requests->links() }}</div>
    @endif
@endsection
```

### `resources/views/manager/show.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Звернення ' . $request->number)
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="mb-1">{{ $request->number }}</h1>
            <div class="text-muted">{{ $request->subject }}</div>
        </div>
        <span class="badge bg-secondary fs-6">{{ $request->status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Деталі</div>
                <div class="card-body">
                    <p><strong>Клієнт:</strong> {{ $request->client->name }} ({{ $request->client->email }})</p>
                    <p><strong>Послуга:</strong> {{ $request->service->name }}</p>
                    <p><strong>Контактна особа:</strong> {{ $request->contact_name }}</p>
                    <p><strong>Email:</strong> {{ $request->contact_email }}</p>
                    <p><strong>Телефон:</strong> {{ $request->contact_phone ?: '—' }}</p>
                    <hr>
                    <p class="text-break">{!! nl2br(e($request->body)) !!}</p>
                </div>
            </div>

            <h3 class="mt-4">Історія</h3>
            <div class="list-group">
                @foreach($request->histories as $h)
                    <div class="list-group-item {{ $h->is_internal ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($h->from_status && $h->from_status !== $h->to_status)
                                    <strong>{{ $h->from_status->label() }} → {{ $h->to_status->label() }}</strong>
                                @elseif($h->from_status === null)
                                    <strong>Створено</strong>
                                @else
                                    <strong>Коментар</strong>
                                @endif
                                — {{ $h->user->name }}
                                @if($h->is_internal)
                                    <span class="badge bg-warning text-dark">внутрішній</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $h->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                        @if($h->comment)<p class="mb-0 mt-1">{{ $h->comment }}</p>@endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-4">
            @if(!$request->manager_id)
                <form method="POST" action="{{ route('manager.assign', $request) }}" class="mb-3">
                    @csrf
                    <button class="btn btn-outline-primary w-100">Призначити себе</button>
                </form>
            @else
                <div class="alert alert-secondary">Відповідальний: {{ $request->manager->name }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-header">Змінити статус</div>
                <div class="card-body">
                    @if(empty($allowedNext))
                        <p class="text-muted mb-0">Статус термінальний, переходи недоступні.</p>
                    @else
                        <form method="POST" action="{{ route('manager.status', $request) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-2" required>
                                <option value="">— Оберіть —</option>
                                @foreach($allowedNext as $s)
                                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                @endforeach
                            </select>
                            <textarea name="rejection_reason" class="form-control mb-2" rows="2"
                                      placeholder="Причина відхилення (обов'язково, якщо відхиляєте)"></textarea>
                            <textarea name="comment" class="form-control mb-2" rows="2"
                                      placeholder="Коментар (необов'язково)"></textarea>
                            <button class="btn btn-primary w-100">Зберегти</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Додати коментар</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manager.comment', $request) }}">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" required maxlength="2000"></textarea>
                        <label class="form-check mb-2">
                            <input type="checkbox" name="is_internal" value="1" class="form-check-input">
                            <span class="form-check-label">Внутрішній коментар</span>
                        </label>
                        <button class="btn btn-primary btn-sm">Надіслати</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
```

### `resources/views/services/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Каталог послуг')
@section('content')
    <h1 class="mb-3">Каталог послуг</h1>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Пошук за назвою або описом">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Пошук</button>
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Скинути</a>
        </div>
    </form>

    @if($services->isEmpty())
        <div class="alert alert-info">Послуг не знайдено.</div>
    @else
        <div class="row g-3">
            @foreach($services as $service)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text text-muted">{{ $service->description }}</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('client-requests.create', ['service_id' => $service->id]) }}"
                               class="btn btn-sm btn-primary">Звернутись</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">{{ $services->links() }}</div>
    @endif
@endsection
```

### `resources/views/auth/login.blade.php`

```blade
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

### `resources/views/auth/register.blade.php`

```blade
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

### `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### `tailwind.config.js`

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
```

### `postcss.config.js`

```javascript
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

# 6. Скрипты и конфигурации

`test-api.sh` и `Test-Api.ps1` отсутствуют. Вместо них в репозитории имеются два Python-скрипта для локального performance testing, PHPUnit tests, Composer/NPM/Vite конфигурации. `composer.lock` и `package-lock.json` фиксируют резолвленные зависимости, но не дублируются в документе.


## Исходные файлы из репозитория

### `tests/Feature/Auth/AuthenticationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client-requests.index', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
```

### `tests/Feature/Auth/EmailVerificationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
```

### `tests/Feature/Auth/PasswordConfirmationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
```

### `tests/Feature/Auth/PasswordResetTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
```

### `tests/Feature/Auth/PasswordUpdateTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
```

### `tests/Feature/Auth/RegistrationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client-requests.index', absolute: false));
    }
}
```

### `tests/Feature/AvailabilityTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    public function test_the_health_endpoint_reports_application_availability(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertSeeText('Application up');
    }
}
```

### `tests/Feature/ExampleTest.php`

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // <--- Добавляем эту строку

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
```

### `tests/Feature/ProfileTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
```

### `tests/TestCase.php`

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
```

### `tests/Unit/ExampleTest.php`

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

### `tests/Unit/RequestStatusTransitionTest.php`

```php
<?php

namespace Tests\Unit;

use App\Enums\RequestStatus;
use App\Services\RequestStatusTransition;
use PHPUnit\Framework\TestCase;

class RequestStatusTransitionTest extends TestCase
{
    public function test_new_to_in_progress_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::IN_PROGRESS));
    }

    public function test_new_to_rejected_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::REJECTED));
    }

    public function test_new_to_completed_forbidden(): void
    {
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::COMPLETED));
    }

    public function test_completed_is_terminal(): void
    {
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::COMPLETED, RequestStatus::IN_PROGRESS));
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::COMPLETED, RequestStatus::NEW));
    }

    public function test_in_progress_to_completed_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::IN_PROGRESS, RequestStatus::COMPLETED));
    }
}
```

### `config/auth.php`

```php
<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
```

### `composer.json`

```json
{
    "$schema": "https://getcomposer.org/schema.json",
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/tinker": "^2.10.1"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/breeze": "^2.4",
        "laravel/pail": "^1.2.2",
        "laravel/pint": "^1.24",
        "laravel/sail": "^1.41",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^11.5.50"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "setup": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan migrate --force",
            "npm install",
            "npm run build"
        ],
        "dev": [
            "Composer\\Config::disableProcessTimeout",
            "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others"
        ],
        "test": [
            "@php artisan config:clear --ansi",
            "@php artisan test"
        ],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "pre-package-uninstall": [
            "Illuminate\\Foundation\\ComposerScripts::prePackageUninstall"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

### `package.json`

```json
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.2",
        "@tailwindcss/vite": "^4.0.0",
        "alpinejs": "^3.4.2",
        "autoprefixer": "^10.4.2",
        "axios": "^1.11.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^2.0.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.1.0",
        "vite": "^7.0.7"
    }
}
```

### `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_MAINTENANCE_DRIVER" value="file"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="BROADCAST_CONNECTION" value="null"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="DB_URL" value=""/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
        <env name="NIGHTWATCH_ENABLED" value="false"/>
    </php>
</phpunit>
```

### `scripts/load-test.py`

```python
#!/usr/bin/env python3
"""Run a small, dependency-free HTTP load test against the local application."""

import argparse
from http.client import HTTPException
import math
import sys
import time
from concurrent.futures import ThreadPoolExecutor
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen


def fetch(url: str, timeout: float) -> tuple[bool, float, int | None]:
    started = time.perf_counter()
    status = None

    try:
        request = Request(url, headers={"User-Agent": "request-portal-load-test/1.0"})
        with urlopen(request, timeout=timeout) as response:
            status = response.status
            response.read()
            success = 200 <= status < 300
    except HTTPError as error:
        status = error.code
        with error:
            error.read()
        success = False
    except (HTTPException, URLError, TimeoutError, OSError):
        success = False

    return success, (time.perf_counter() - started) * 1000, status


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--url", default="http://127.0.0.1:8000/services")
    parser.add_argument("--requests", type=int, default=200, dest="request_count")
    parser.add_argument("--concurrency", type=int, default=10)
    parser.add_argument("--timeout", type=float, default=5)
    parser.add_argument("--max-p95-ms", type=float, default=1000)
    args = parser.parse_args()

    if args.request_count < 1 or args.concurrency < 1 or args.timeout <= 0 or args.max_p95_ms <= 0:
        parser.error("requests, concurrency, timeout, and max-p95-ms must be positive")

    started = time.perf_counter()
    with ThreadPoolExecutor(max_workers=args.concurrency) as executor:
        results = list(executor.map(
            lambda _: fetch(args.url, args.timeout),
            range(args.request_count),
        ))
    elapsed = time.perf_counter() - started

    successful = sum(success for success, _, _ in results)
    latencies = sorted(latency for _, latency, _ in results)
    p95 = latencies[math.ceil(0.95 * len(latencies)) - 1]
    availability = successful / args.request_count * 100
    throughput = args.request_count / elapsed
    statuses = sorted({status for _, _, status in results if status is not None})

    print(f"URL: {args.url}")
    print(f"Requests: {args.request_count}; concurrency: {args.concurrency}")
    print(f"Successful responses: {successful}/{args.request_count} ({availability:.2f}%)")
    print(f"HTTP statuses: {', '.join(map(str, statuses)) or 'none'}")
    print(f"Elapsed: {elapsed:.3f} s; throughput: {throughput:.2f} requests/s")
    print(f"Latency: p50 {latencies[math.ceil(0.50 * len(latencies)) - 1]:.2f} ms; "
          f"p95 {p95:.2f} ms; max {latencies[-1]:.2f} ms")
    print(f"Acceptance (all responses successful and p95 <= {args.max_p95_ms:.0f} ms): "
          f"{'PASS' if successful == args.request_count and p95 <= args.max_p95_ms else 'FAIL'}")

    return 0 if successful == args.request_count and p95 <= args.max_p95_ms else 1


if __name__ == "__main__":
    sys.exit(main())
```

### `scripts/seed-load-test-data.py`

```python
#!/usr/bin/env python3
"""Seed 50 active services into an empty, migrated SQLite benchmark database."""

import argparse
import sqlite3
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import quote


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("database", type=Path, help="path to an empty, migrated SQLite database")
    args = parser.parse_args()

    database = args.database.expanduser().resolve()
    if not database.is_file():
        parser.error(f"database does not exist: {database}")

    connection = sqlite3.connect(f"file:{quote(str(database), safe='/')}?mode=rw", uri=True)
    try:
        table = connection.execute(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'services'"
        ).fetchone()
        if table is None:
            parser.error("database is missing the migrated services table")

        count = connection.execute("SELECT COUNT(*) FROM services").fetchone()[0]
        if count:
            parser.error("services table must be empty to avoid duplicating benchmark data")

        created_at = datetime.now(timezone.utc).isoformat(sep=" ", timespec="seconds")
        connection.executemany(
            """
            INSERT INTO services (name, description, is_active, created_at, updated_at)
            VALUES (?, ?, 1, ?, ?)
            """,
            [
                (
                    f"Demonstration service {number:02}",
                    "Service used for the local performance measurement.",
                    created_at,
                    created_at,
                )
                for number in range(1, 51)
            ],
        )
        connection.commit()
        print(f"Inserted 50 active services into {database}")
    finally:
        connection.close()

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
```
