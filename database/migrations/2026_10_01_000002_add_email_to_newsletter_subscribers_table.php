<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel mungkin sudah ada dari run sebelumnya tanpa kolom email
        // Tambahkan kolom email jika belum ada
        if (Schema::hasTable('newsletter_subscribers') && ! Schema::hasColumn('newsletter_subscribers', 'email')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                $table->string('email')->unique()->after('id');
            });
        }

        // Jika tabel belum ada sama sekali, buat lengkap
        if (! Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('newsletter_subscribers', 'email')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                $table->dropUnique(['email']);
                $table->dropColumn('email');
            });
        }
    }
};
