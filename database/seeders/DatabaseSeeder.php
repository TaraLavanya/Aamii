<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Product;
use App\Models\Visitor;
use App\Models\Category;
use App\Models\Exhibitor;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usersData = $this->getUsersData();
        foreach ($usersData as $userData) {
            User::factory()->create($userData);
        }
    }

    private function getUsersData()
    {
        $password = Hash::make('password');
        return [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'user_id' => 'EMP0001',
                'created_by' => 1,
                'updated_by' => 1,
                'is_active' => 1,
                'password' => $password
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@gmail.com',
                'user_id' => 'EMP0002',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Shamili',
                'email' => 'shamili@gmail.com',
                'user_id' => 'EMP0007',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Aishu',
                'email' => 'aishu@gmail.com',
                'user_id' => 'EMP0008',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Sarumathi',
                'email' => 'sarumathi@gmail.com',
                'user_id' => 'EMP0009',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Keerthi',
                'email' => 'keerthi@gmail.com',
                'user_id' => 'EMP0010',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Keerthika',
                'email' => 'keerthika@gmail.com',
                'user_id' => 'EMP0011',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Nanthini',
                'email' => 'nandhini@gmail.com',
                'user_id' => 'EMP0012',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Abinaya',
                'email' => 'abinaya@gmail.com',
                'user_id' => 'EMP0013',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
            [
                'name' => 'Akshaya',
                'email' => 'akshaya@gmail.com',
                'user_id' => 'EMP0014',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'password' => $password
            ],
        ];
    }
}
