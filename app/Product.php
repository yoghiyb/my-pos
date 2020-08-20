<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    /**
     * Memanggil relasi kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
