<?php

namespace App\Policies;

use App\Models\EstadoParticipante;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class EstadoParticipantePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:ver:*',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EstadoParticipante $estadoParticipante): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "estado_participante:crud:ver:{$estadoParticipante->id}",
            'estado_participante:crud:ver:*',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:agregar',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:editar',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:eliminar',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:agregar',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'estado_participante:crud:eliminar',
            'estado_participante:crud:*',
            'estado_participante:*'
        ]);
    }
}
