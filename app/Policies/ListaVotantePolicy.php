<?php

namespace App\Policies;

use App\Models\ListaVotante;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class ListaVotantePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:ver:*',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ListaVotante $listaVotante): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "lista_votante:crud:ver:{$listaVotante->id}",
            'lista_votante:crud:ver:*',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:agregar',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:editar',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:eliminar',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:agregar',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'lista_votante:crud:eliminar',
            'lista_votante:crud:*',
            'lista_votante:*'
        ]);
    }
}
