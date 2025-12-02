<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class AreaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:ver:*',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Area $area): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "area:crud:ver:{$area->idArea}",
            'area:crud:ver:*',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:agregar',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:editar',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:eliminar',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:agregar',
            'area:crud:*',
            'area:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'area:crud:eliminar',
            'area:crud:*',
            'area:*'
        ]);
    }
}
