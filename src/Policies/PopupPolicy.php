<?php

namespace SiteApps\ContactWidget\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use SiteApps\ContactWidget\Models\Popup;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupPolicy
{
    use HandlesAuthorization;

    public function viewAny(Authenticatable $user): bool
    {
        return $this->allows($user, 'view_any_callback::popup', 'view_any_popup');
    }

    public function view(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'view_callback::popup', 'view_popup');
    }

    public function create(Authenticatable $user): bool
    {
        return $this->allows($user, 'create_callback::popup', 'create_popup');
    }

    public function update(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'update_callback::popup', 'update_popup');
    }

    public function delete(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'delete_callback::popup', 'delete_popup');
    }

    public function deleteAny(Authenticatable $user): bool
    {
        return $this->allows($user, 'delete_any_callback::popup', 'delete_any_popup');
    }

    public function forceDelete(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'force_delete_callback::popup', 'force_delete_popup');
    }

    public function forceDeleteAny(Authenticatable $user): bool
    {
        return $this->allows($user, 'force_delete_any_callback::popup', 'force_delete_any_popup');
    }

    public function restore(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'restore_callback::popup', 'restore_popup');
    }

    public function restoreAny(Authenticatable $user): bool
    {
        return $this->allows($user, 'restore_any_callback::popup', 'restore_any_popup');
    }

    public function replicate(Authenticatable $user, Popup $popup): bool
    {
        return $this->allows($user, 'replicate_callback::popup', 'replicate_popup');
    }

    public function reorder(Authenticatable $user): bool
    {
        return $this->allows($user, 'reorder_callback::popup', 'reorder_popup');
    }

    protected function allows(Authenticatable $user, string ...$permissions): bool
    {
        if (! config('contact-widget.authorize_with_shield', false)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
