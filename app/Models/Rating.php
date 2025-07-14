<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\product;
use App\Models\User;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
    ];

    // Each rating belongs to a user
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    // Each rating belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
