<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'User';

    protected $primaryKey = 'idUser';

    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'RolUser', 'idUser', 'idRol');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'UserPermiso', 'idUser', 'idPermiso');
    }

    public function excepciones_permisos()
    {
        return $this->belongsToMany(Permiso::class, 'ExcepcionPermiso', 'idUser', 'idPermiso');
    }

    public function perfil()
    {
        return $this->hasOne(PerfilUsuario::class, 'idUser');
    }

    public function participante()
    {
        return $this->hasMany(Participante::class, 'idUser');
    }
}
