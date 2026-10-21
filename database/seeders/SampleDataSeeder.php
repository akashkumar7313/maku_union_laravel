<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            ContentSeeder::class,
            AdminSeeder::class,
            DemoSeeder::class,
            ExtendedDemoSeeder::class,
        ]);

        $this->command?->info('पूरा सैंपल डेटा तैयार है — सेटिंग्स, सामग्री, सदस्य, भुगतान, शिकायतें, समाचार, गैलरी, टीम, शाखाएँ, अनुवाद और ऑडिट लॉग।');
    }
}
