<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Consulta;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Consulta');
    }

    public function view(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('View:Consulta');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Consulta');
    }

    public function update(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('Update:Consulta');
    }

    public function delete(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('Delete:Consulta');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Consulta');
    }

    public function restore(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('Restore:Consulta');
    }

    public function forceDelete(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('ForceDelete:Consulta');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Consulta');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Consulta');
    }

    public function replicate(AuthUser $authUser, Consulta $consulta): bool
    {
        return $authUser->can('Replicate:Consulta');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Consulta');
    }

}