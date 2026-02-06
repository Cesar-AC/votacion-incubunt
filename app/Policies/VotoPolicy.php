<?php

namespace App\Policies;

use App\Models\VotoCandidato;
use App\Models\VotoPartido;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class VotoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:ver:*',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VotoCandidato|VotoPartido $voto): bool
    {
        $votoId = $voto instanceof VotoCandidato ? $voto->idVotoCandidato : $voto->idVotoPartido;
        
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "voto:crud:ver:{$votoId}",
            'voto:crud:ver:*',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:agregar',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VotoCandidato|VotoPartido $voto): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:editar',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VotoCandidato|VotoPartido $voto): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:eliminar',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VotoCandidato|VotoPartido $voto): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:agregar',
            'voto:crud:*',
            'voto:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VotoCandidato|VotoPartido $voto): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'voto:crud:eliminar',
            'voto:crud:*',
            'voto:*'
        ]);
    }
}
