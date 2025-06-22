<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'month', 'year'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(MonthlyListItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'monthly_list_items');
    }
}
