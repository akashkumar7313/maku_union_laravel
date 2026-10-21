<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@maku.local');
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            $this->command?->warn('ADMIN_PASSWORD खाली है; प्रशासक उपयोगकर्ता नहीं बनाया गया।');

            return;
        }

        User::updateOrCreate(['email' => $email], [
            'name' => 'मुख्य प्रशासक',
            'password' => $password,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command?->info('प्रशासक खाता तैयार: '.$email);
    }
}
