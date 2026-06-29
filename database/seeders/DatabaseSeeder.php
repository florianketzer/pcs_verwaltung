<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory()->create([
        //     'username' => 'vision',
        //     'email' => 'f.ketzer@kr-vision.de',
        //     'name' => 'Florian Ketzer',
        //     'password' => Hash::make('gaudi123')
        // ]);

        // \App\Models\User::factory(10)->create();

        $servicecontracts = [
            [
                'name' => 'ARN2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'ARN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'ARO2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'ARO8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'BRN2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'BRN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'BRO2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'BRO8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'BRO8 oE',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'LRN2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'LRN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'MRN2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'MRN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'PRN2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'PRN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'PRO2',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'PRO8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'RNN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'SRN8',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'SRN4',
                'producer_name' => '',
                'can_expire' => false,
            ],
            [
                'name' => 'Avaya IPOSS - IP Office',
                'producer_name' => 'IPO CO - DEL REM TECH SUPT 8X5 - IP500 V2',
                'can_expire' => true,
            ],
            [
                'name' => 'Avaya IPOSS - Server Edition',
                'producer_name' => 'IP CO - DEL REM TECH SUPT 8X5 - HP DL120G7',
                'can_expire' => true,
            ],
            [
                'name' => 'Avaya IPOSS - ASBCE',
                'producer_name' => 'IP CO - DEL REM TECH SUPT 8X5 - HP DL120G7',
                'can_expire' => true,
            ],
            [
                'name' => 'Avaya IPOSS - CIE',
                'producer_name' => 'SA ESSENTIAL SUPT CIE R3',
                'can_expire' => true,
            ],
            [
                'name' => 'Innovaphone SSA',
                'producer_name' => 'Innovaphone SSA licenses - SSC',
                'can_expire' => true,
            ],
            [
                'name' => 'Audiocodes',
                'producer_name' => 'ACTS-SUPP-9X5',
                'can_expire' => true,
            ],
            [
                'name' => 'Estos ProCall',
                'producer_name' => 'estos Software-Pflege',
                'can_expire' => true,
            ],
            [
                'name' => 'Estos MetaDirectory',
                'producer_name' => 'estos Software Assurance',
                'can_expire' => true,
            ],
            [
                'name' => 'Estos Mobility Service',
                'producer_name' => 'ProCall Mobility Services',
                'can_expire' => true,
            ],
            [
                'name' => 'Estos Meetings',
                'producer_name' => 'ProCall Meetings Services',
                'can_expire' => true,
            ],
        ];

        foreach($servicecontracts as $servicecontract) {
            \App\Models\Servicecontract::factory()->create([
                'name' => $servicecontract['name'],
                'producer_name' => $servicecontract['producer_name'],
                'can_expire' => key_exists('can_expire', $servicecontract) ? $servicecontract['can_expire'] : false
            ]);
        }

    }
}
