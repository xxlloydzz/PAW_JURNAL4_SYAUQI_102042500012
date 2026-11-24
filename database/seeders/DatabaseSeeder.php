<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission

        // 6. Akun Mahasiswa Tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 7. Mata Kuliah Tambahan dengan Semester dan SKS Berbeda
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Mata kuliah yang membahas perancangan basis data relasional, normalisasi, serta bahasa query SQL untuk pengelolaan data terstruktur.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Kecerdasan Buatan',
                'deskripsi' => 'Mata kuliah yang membahas konsep dasar kecerdasan buatan, metode pencarian, representasi pengetahuan, dan pengantar machine learning.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 8. Daftarkan Mahasiswa ke Mata Kuliah (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 9. Tugas Contoh pada Mata Kuliah Pertama
        $assignment1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Analisis Kebutuhan dan Perancangan MVC'],
            [
                'deskripsi' => 'Susun analisis kebutuhan sistem CeLOE LMS dan rancangan arsitektur MVC-nya. Tuliskan jawaban Anda secara terstruktur pada kolom pengumpulan tugas.',
                'deadline' => now()->addDays(7),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 2: Implementasi Middleware dan Penilaian Tugas'],
            [
                'deskripsi' => 'Implementasikan pengelompokan middleware auth dan role pada rute aplikasi, kemudian uji alur penilaian tugas oleh dosen beserta ekspor rekap nilai ke CSV.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 10. Contoh Pengumpulan Tugas: satu sudah dinilai, satu belum dinilai
        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Analisis kebutuhan CeLOE LMS mencakup dua aktor utama, yaitu dosen dan mahasiswa. Dosen membutuhkan fitur pengelolaan mata kuliah, materi, tugas, dan penilaian, sedangkan mahasiswa membutuhkan fitur pendaftaran kelas, akses materi, dan pengumpulan tugas. Arsitektur MVC memisahkan model untuk data, view untuk tampilan Blade, dan controller untuk logika bisnis sehingga aplikasi lebih mudah dikembangkan dan dirawat.',
                'nilai' => 88,
                'catatan_dosen' => 'Analisis kebutuhan sudah lengkap dan perancangan MVC dijelaskan dengan runtut. Pertahankan.',
                'submitted_at' => now()->subDays(2),
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'CeLOE LMS adalah sistem pembelajaran online untuk dosen dan mahasiswa. Pada pola MVC, model mengelola data pengguna dan mata kuliah, view menampilkan halaman katalog dan detail kelas, sedangkan controller mengatur alur pendaftaran kelas serta pengumpulan tugas dari mahasiswa.',
                'submitted_at' => now()->subDays(1),
            ]
        );
        
    }
}
