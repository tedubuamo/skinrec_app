<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id_category';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['category_name', 'id_category', 'description'];

    public function products()
    {
        return $this->hasMany(product::class, 'id_category', 'id_category');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['id_category'] = (string) $array['id_category']; // Konversi id_brand menjadi string
        return $array;
    }

    use HasFactory;
    use HasFactory;
}
