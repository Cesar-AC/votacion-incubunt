<?php

namespace App\Policies;

use App\Models\PropuestaPartido;
use App\Models\User;
use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Auth\Access\Response;

class PropuestaPartidoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:ver:*',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropuestaPartido $propuestaPartido): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            "propuesta_partido:crud:ver:{$propuestaPartido->id}",
            'propuesta_partido:crud:ver:*',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:agregar',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropuestaPartido $propuestaPartido): bool
    {
        // Verificar permisos generales
        $tienePermiso = ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:editar',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);

        if (!$tienePermiso) {
            return false;
        }

        // Verificar que el usuario sea candidato del mismo partido
        $candidato = \App\Models\Candidato::where('idUsuario', $user->idUser)->first();
        
        if (!$candidato) {
            return false;
        }

        return $propuestaPartido->idPartido == $candidato->idPartido;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropuestaPartido $propuestaPartido): bool
    {
        // Verificar permisos generales
        $tienePermiso = ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:eliminar',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);

        if (!$tienePermiso) {
            return false;
        }

        // Verificar que el usuario sea candidato del mismo partido
        $candidato = \App\Models\Candidato::where('idUsuario', $user->idUser)->first();
        
        if (!$candidato) {
            return false;
        }

        return $propuestaPartido->idPartido == $candidato->idPartido;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:agregar',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ValidadorPermisos::usuarioTienePermisos($user, [
            'propuesta_partido:crud:eliminar',
            'propuesta_partido:crud:*',
            'propuesta_partido:*'
        ]);
    }
}
