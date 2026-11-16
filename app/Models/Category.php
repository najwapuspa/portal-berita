<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Nama kategori dinamis berdasarkan locale aktif (ID / EN).
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            return config('categories.' . $this->slug . '.name_en', $this->name);
        }
        return $this->name;
    }
}