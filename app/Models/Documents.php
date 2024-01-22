<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise',
        'title',
        'contents',
        'category_id'
    ];

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }
}
