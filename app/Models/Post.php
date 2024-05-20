<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
        'category_id'
    ];

    protected function category():BelongTo
    {
        return $this->belongTo(Category::class);

    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }
}
