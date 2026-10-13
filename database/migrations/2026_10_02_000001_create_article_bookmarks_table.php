<?php
// MIGRATION INI BELUM DIJALANKAN — menunggu persetujuan.
// Tujuan: menyimpan berita yang di-bookmark/disimpan oleh user.
// Jalankan dengan: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Satu user hanya bisa bookmark satu artikel sekali
            $table->unique(['user_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_bookmarks');
    }
};
