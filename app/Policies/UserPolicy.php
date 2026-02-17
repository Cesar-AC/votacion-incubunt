<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:ver:*',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:ver:*',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:agregar',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:editar',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:eliminar',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:agregar',
            'user:crud:*',
            'user:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'user:crud:eliminar',
            'user:crud:*',
            'user:*'
        ]);
    }
}
