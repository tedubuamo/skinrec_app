<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class skintype extends Model
{
    protected $table = 'skintype';
    protected $primaryKey = 'id_skintype';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_skintype', 'skintype_name', 'sensitive', 'combination',
        'oily', 'dry', 'normal', 'description'
    ];

    protected $casts = [
        'sensitive' => 'boolean',
        'combination' => 'boolean',
        'oily' => 'boolean',
        'dry' => 'boolean',
        'normal' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(product::class, 'id_skintype', 'id_skintype');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['id_skintype'] = (string) $array['id_skintype']; // Konversi id_brand menjadi string
        return $array;
    }
    use HasFactory;
}
