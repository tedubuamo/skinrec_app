<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'id_product';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_product', 'product_name', 'id_brand', 'id_category',
        'id_skintype', 'id_problem', 'id_notable', 'description',
        'price', 'picture_src', 'rating', 'image_url'
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(brand::class, 'id_brand', 'id_brand');
    }

    public function category()
    {
        return $this->belongsTo(category::class, 'id_category', 'id_category');
    }

    public function skinType()
    {
        return $this->belongsTo(skintype::class, 'id_skintype', 'id_skintype');
    }

    public function skinProblem()
    {
        return $this->belongsTo(skin_problem::class, 'id_problem', 'id_problem');
    }

    public function notableEffect()
    {
        return $this->belongsTo(notable_effect::class, 'id_notable', 'id_notable');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['id_product'] = (string) $array['id_product']; // Konversi id_brand menjadi string
        return $array;
    }

    use HasFactory;
}
