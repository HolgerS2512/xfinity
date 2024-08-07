<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user can view the model.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function view(User $user, Model $model)
    {
        // Implementiere hier die Logik, um zu bestimmen, ob der Benutzer das Modell sehen darf
        return $user->id === $model->user_id;
    }

    /**
     * Determine if the given user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        // Implementiere hier die Logik, um zu bestimmen, ob der Benutzer das Modell erstellen darf
        return $user->role === 'admin';
    }

    /**
     * Determine if the given user can update the model.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function update(User $user, Model $model)
    {
        // Implementiere hier die Logik, um zu bestimmen, ob der Benutzer das Modell aktualisieren darf
        return $user->id === $model->user_id;
    }

    /**
     * Determine if the given user can delete the model.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function delete(User $user, Model $model)
    {
        // Implementiere hier die Logik, um zu bestimmen, ob der Benutzer das Modell löschen darf
        return $user->id === $model->user_id;
    }
}
