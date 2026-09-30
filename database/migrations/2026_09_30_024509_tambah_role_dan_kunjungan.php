<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $t) {
                $t->string('role', 20)->default('user');
            });
        }

        if (Schema::hasTable('articles') && !Schema::hasColumn('articles', 'views')) {
            Schema::table('articles', function (Blueprint $t) {
                $t->unsignedBigInteger('views')->default(0);
            });
        }

        if (!Schema::hasTable('kunjungan')) {
            Schema::create('kunjungan', function (Blueprint $t) {
                $t->date('tanggal')->primary();
                $t->unsignedBigInteger('jumlah')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};