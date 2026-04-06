<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brand extends Model
{
    protected $table = 'brand';
    protected $primaryKey = 'id_brand';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_brand', 'brand_name', 'description'];

    public function products()
    {
        return $this->hasMany(product::class, 'id_brand', 'id_brand');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['id_brand'] = (string) $array['id_brand']; // Konversi id_brand menjadi string
        return $array;
    }

    use HasFactory;

    use HasFactory;
}
