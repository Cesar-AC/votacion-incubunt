<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class ExcepcionPermiso extends Model
{
    use HasCompositeKey;
    protected $table = 'ExcepcionPermiso';

    protected $primaryKey = ['idUser', 'idPermiso'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idUser',
        'idPermiso'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'idPermiso');
    }
}
