<?php

namespace App\Policies;

use App\Models\EstadoElecciones;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class EstadoEleccionesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:ver:*',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EstadoElecciones $estadoElecciones): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "estado_elecciones:crud:ver:{$estadoElecciones->id}",
            'estado_elecciones:crud:ver:*',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:agregar',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:editar',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:eliminar',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:agregar',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_elecciones:crud:eliminar',
            'estado_elecciones:crud:*',
            'estado_elecciones:*'
        ]);
    }
}
