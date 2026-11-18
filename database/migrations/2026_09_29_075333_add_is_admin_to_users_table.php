<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('password');
            });
        }
    }

    public function down(): void
    {
        // Sengaja dikosongkan (no-op) saat rollback untuk menjamin kolom 'is_admin'
        // dan hak akses pengguna yang sudah ada tidak terhapus secara tidak sengaja.
    }
};