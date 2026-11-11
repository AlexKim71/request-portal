<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Менеджер',
            'email'    => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role'     => UserRole::MANAGER,
        ]);

        User::create([
            'name'     => 'Клієнт',
            'email'    => 'client@example.com',
            'password' => Hash::make('password123'),
            'role'     => UserRole::CLIENT,
        ]);

        $services = [
            ['name' => 'Консультація',                     'description' => 'Усна або письмова консультація з фахівцем.'],
            ['name' => 'Технічна підтримка',                'description' => 'Вирішення технічних питань і збоїв у роботі.'],
            ['name' => 'Документальний супровід',           'description' => 'Підготовка та перевірка документів.'],
            ['name' => 'Навчання персоналу',                'description' => 'Проведення навчальних заходів для співробітників.'],
            ['name' => 'Інтеграція з зовнішніми сервісами', 'description' => 'Налаштування інтеграцій із зовнішніми API.'],
        ];
        foreach ($services as $s) {
            Service::create($s);
        }
    }
}
