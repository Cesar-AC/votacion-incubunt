<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaLog extends Model
{
    protected $table = 'CategoriaLog';
    protected $primaryKey = 'idCategoriaLog';
    public $timestamps = false;
    protected $fillable = [
        'idCategoriaLog',
        'nombre'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class, 'idCategoriaLog');
    }
}
