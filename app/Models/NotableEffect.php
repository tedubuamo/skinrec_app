<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotableEffect extends Model
{
    protected $table = 'notable_effect';
    protected $primaryKey = 'id_notable';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_notable', 'effect_name', 'acne_free', 'soothing',
        'brightening', 'moisturizing', 'hydrating', 'pore_care',
        'anti_aging', 'balancing', 'uv_protection', 'skin_barrier',
        'refreshing', 'oil_control', 'no_whitecast', 'black_spot', 'description'
    ];

    protected $casts = [
        'acne_free' => 'boolean',
        'soothing' => 'boolean',
        'brightening' => 'boolean',
        'moisturizing' => 'boolean',
        'hydrating' => 'boolean',
        'pore_care' => 'boolean',
        'anti_aging' => 'boolean',
        'balancing' => 'boolean',
        'uv_protection' => 'boolean',
        'skin_barrier' => 'boolean',
        'refreshing' => 'boolean',
        'oil_control' => 'boolean',
        'no_whitecast' => 'boolean',
        'black_spot' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'id_notable', 'id_notable');
    }

    use HasFactory;
}
