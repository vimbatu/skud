<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    public function run(): void {
        if(User::where('name','admin')->exists()) return;
        User::create([
            'name' => 'admin',
            'email' => 'vimbatu@gmail.com',
            'password' => Hash::make('!@#$%12345'),
            'role' => 'admin'
        ]);
    }
}
