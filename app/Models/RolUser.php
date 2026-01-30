<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RolUser extends Model
{
    use HasCompositeKey;
    protected $table = 'RolUser';

    protected $primaryKey = ['idRol', 'idUser'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idRol',
        'idUser'
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
