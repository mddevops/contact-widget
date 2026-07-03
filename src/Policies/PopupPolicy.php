<?php

namespace SiteApps\ContactWidget\Policies;

use SiteApps\ContactWidget\Models\Popup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_callback::popup') || $user->can('view_any_popup');
    }

    public function view(User $user, Popup $popup): bool
    {
        return $user->can('view_callback::popup') || $user->can('view_popup');
    }

    public function create(User $user): bool
    {
        return $user->can('create_callback::popup') || $user->can('create_popup');
    }

    public function update(User $user, Popup $popup): bool
    {
        return $user->can('update_callback::popup') || $user->can('update_popup');
    }

    public function delete(User $user, Popup $popup): bool
    {
        return $user->can('delete_callback::popup') || $user->can('delete_popup');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_callback::popup') || $user->can('delete_any_popup');
    }

    public function forceDelete(User $user, Popup $popup): bool
    {
        return $user->can('force_delete_callback::popup') || $user->can('force_delete_popup');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_callback::popup') || $user->can('force_delete_any_popup');
    }

    public function restore(User $user, Popup $popup): bool
    {
        return $user->can('restore_callback::popup') || $user->can('restore_popup');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_callback::popup') || $user->can('restore_any_popup');
    }

    public function replicate(User $user, Popup $popup): bool
    {
        return $user->can('replicate_callback::popup') || $user->can('replicate_popup');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_callback::popup') || $user->can('reorder_popup');
    }
}
