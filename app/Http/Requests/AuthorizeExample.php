<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizeExample extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Test 1
        // Überprüfen, ob der Benutzer diese Aktion laut Policy ausführen darf
        return $this->user()->can('update', $this->route('post'));

        // Test 2
        // Überprüfen, ob der aktuell angemeldete Benutzer der Besitzer der Posts ist
        // $post = Post::find($this->route('post'));
        // return $post && $this->user()->id === $post->user_id;

        // Test 3
        // Nur Admins dürfen Unterkategorien erstellen
        return auth()->user()->role === 'Admin';
    }
}
