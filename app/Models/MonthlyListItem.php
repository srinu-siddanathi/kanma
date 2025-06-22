<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyListItem extends Model
{
    use HasFactory;

    protected $fillable = ['monthly_list_id', 'product_id', 'quantity'];

    public function monthlyList()
    {
        return $this->belongsTo(MonthlyList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
