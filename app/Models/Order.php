<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id', 'phone', 'total_amount', 'status', 'created_at'];

 /**
     * @return float Общая сумма заказа
     */
    public function getTotalAmountAttribute()
    {
        $productsData = json_decode($this->attributes['data'], true)['products'];

        $totalAmount = 0;

        foreach ($productsData as $product) {
            $productTotal = $product['price'] * $product['quantity'];
            $totalAmount += $productTotal;
        }

        return $totalAmount;
    }
}
