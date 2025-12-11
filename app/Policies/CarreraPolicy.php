<?php

namespace App\Policies;

use App\Models\Carrera;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class CarreraPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:ver:*',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Carrera $carrera): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "carrera:crud:ver:{$carrera->id}",
            'carrera:crud:ver:*',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:agregar',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:editar',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:eliminar',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:agregar',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'carrera:crud:eliminar',
            'carrera:crud:*',
            'carrera:*'
        ]);
    }
}
