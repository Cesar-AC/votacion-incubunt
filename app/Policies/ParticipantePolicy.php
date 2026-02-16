<?php

namespace App\Policies;

use App\Models\Participante;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class ParticipantePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:ver:*',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Participante $participante): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "participante:crud:ver:{$participante->id}",
            'participante:crud:ver:*',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:agregar',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:editar',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:eliminar',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:agregar',
            'participante:crud:*',
            'participante:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'participante:crud:eliminar',
            'participante:crud:*',
            'participante:*'
        ]);
    }
}
