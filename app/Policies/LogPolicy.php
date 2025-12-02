<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class LogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:ver:*',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Log $log): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "log:crud:ver:{$log->id}",
            'log:crud:ver:*',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:agregar',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:editar',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:eliminar',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:agregar',
            'log:crud:*',
            'log:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'log:crud:eliminar',
            'log:crud:*',
            'log:*'
        ]);
    }
}
