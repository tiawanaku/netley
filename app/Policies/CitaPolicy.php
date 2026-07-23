<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cita;
use Illuminate\Auth\Access\HandlesAuthorization;

class CitaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cita');
    }

    public function view(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('View:Cita');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cita');
    }

    public function update(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('Update:Cita');
    }

    public function delete(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('Delete:Cita');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Cita');
    }

    public function restore(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('Restore:Cita');
    }

    public function forceDelete(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('ForceDelete:Cita');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Cita');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Cita');
    }

    public function replicate(AuthUser $authUser, Cita $cita): bool
    {
        return $authUser->can('Replicate:Cita');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cita');
    }

}