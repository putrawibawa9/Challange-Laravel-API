<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
  protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });
    }

    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $i = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
    
    protected $fillable = ['name', 'description', 'price', 'stock', 'category_id', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

       public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

      public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
