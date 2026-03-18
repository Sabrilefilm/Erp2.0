<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeRequiredController extends Controller
{
    /**
     * Affiche la page de changement de mot de passe obligatoire (après déblocage temporaire).
     */
    public function show()
    {
        return view('auth.password-change-required');
    }

    /**
     * Enregistre le nouveau mot de passe et autorise l'accès.
     */
    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Veuillez saisir un nouveau mot de passe.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
        ]);

        $user = $request->user();
        $user->update([
            'password' => $request->password,
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre mot de passe a été modifié. Vous pouvez utiliser l\'application.');
    }
}
