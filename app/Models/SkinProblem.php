<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinProblem extends Model
{
    protected $table = 'skin_problem';
    protected $primaryKey = 'id_problem';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_problem', 'problem_name', 'kulit_kusam', 'jerawat',
        'bekas_jerawat', 'pori_pori_besar', 'flek_hitam',
        'garis_halus_dan_kerutan', 'komedo', 'warna_kulit_tidak_merata',
        'kemerahan', 'description'
    ];

    protected $casts = [
        'kulit_kusam' => 'boolean',
        'jerawat' => 'boolean',
        'bekas_jerawat' => 'boolean',
        'pori_pori_besar' => 'boolean',
        'flek_hitam' => 'boolean',
        'garis_halus_dan_kerutan' => 'boolean',
        'komedo' => 'boolean',
        'warna_kulit_tidak_merata' => 'boolean',
        'kemerahan' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'id_problem', 'id_problem');
    }

    use HasFactory;
}
