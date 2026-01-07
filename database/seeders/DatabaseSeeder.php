<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        User::firstOrCreate(['email' => 'petugas@gmail.com'], [
            'name' => 'Petugas',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        \App\Models\Equipment::query()->insert([
            ['name' => 'Multimeter', 'code' => 'TL-001', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Solder Station', 'code' => 'TL-002', 'status' => 'loaned', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kabel LAN Tester', 'code' => 'TL-003', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Power Supply', 'code' => 'TL-004', 'status' => 'damaged', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tang Crimping', 'code' => 'TL-005', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $loanEquipmentId = \App\Models\Equipment::where('status','loaned')->first()->id ?? null;
        if ($loanEquipmentId) {
            \App\Models\Loan::create([
                'student_name' => 'Budi Santoso',
                'student_nis' => '18293',
                'equipment_id' => $loanEquipmentId,
                'borrowed_at' => now()->subDays(5)->toDateString(),
                'planned_return_at' => now()->subDays(2)->toDateString(),
                'returned_at' => null,
                'purpose' => 'Praktik Sistem Komputer',
                'status' => 'active',
            ]);
        }

        \App\Models\Staff::query()->insert([
            ['name' => 'Admin Toolman', 'position' => 'Kepala Toolman', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sutrisno', 'position' => 'Toolman', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rina Kurnia', 'position' => 'Guru Piket', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dewi Lestari', 'position' => 'Wali Kelas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Imam Syafi’i', 'position' => 'Admin Laboratorium', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\Student::query()->upsert([
            ['nis' => '18291', 'name' => 'Andi Pratama', 'class' => 'XII TKJ 1', 'type' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['nis' => '18292', 'name' => 'Siti Aminah', 'class' => 'XI RPL 2', 'type' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['nis' => '18293', 'name' => 'Budi Santoso', 'class' => 'XII TKJ 1', 'type' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['nis' => 'NIP-1975012000121001', 'name' => 'Drs. Bud Santoso, M.Pd.', 'class' => null, 'type' => 'teacher', 'created_at' => now(), 'updated_at' => now()],
            ['nis' => 'NIP-1977021000122001', 'name' => 'Ibu Ratna', 'class' => null, 'type' => 'teacher', 'created_at' => now(), 'updated_at' => now()],
        ], ['nis']);

        if (!AppSetting::first()) {
            AppSetting::create([
                'school_name' => 'SMK Nasional Dawarblandong Mojokerto',
                'department_name' => 'Teknik Komputer & Jaringan',
                'address' => 'Jl. Pendidikan No. 1, Dawarblandong, Mojokerto',
                'head_name' => 'Drs. Budi Santoso, M.Pd.',
                'head_nip' => '1975012000121001',
                'theme_primary' => '#0b3a82',
                'footer_text' => '© 2026 Tim IT SMK Nasional Dawarblandong',
                'logo_path' => 'img/logo.png',
            ]);
        }
    }
}
