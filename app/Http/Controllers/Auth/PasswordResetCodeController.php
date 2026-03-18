<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetCodeController extends Controller
{
    /** Durée de validité du code en minutes (max 30 min). */
    private const CODE_EXPIRE_MINUTES = 30;

    /** Longueur du code affiché (alphanumérique). */
    private const CODE_LENGTH = 8;

    /**
     * Affiche la page pour générer un code (réservée aux utilisateurs autorisés).
     */
    public function showGenerateForm()
    {
        $this->authorizeGenerate();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'email', 'role']);

        return view('auth.passwords.generate-code', [
            'users' => $users,
        ]);
    }

    /**
     * Génère un code de réinitialisation pour l'utilisateur choisi.
     */
    public function generate(Request $request)
    {
        $this->authorizeGenerate();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        $emailKey = $user->email ?: 'id:' . $user->id;
        $plainCode = Str::upper(Str::random(self::CODE_LENGTH));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $emailKey],
            [
                'token' => Hash::make($plainCode),
                'created_at' => now(),
            ]
        );

        return back()->with([
            'generated_code' => $plainCode,
            'generated_for_user' => $user->name,
            'generated_for_username' => $user->username,
            'code_expires_at' => now()->addMinutes(self::CODE_EXPIRE_MINUTES),
        ]);
    }

    /**
     * Page invité : formulaire pour réinitialiser le mot de passe avec identifiant + code.
     */
    public function showResetWithCodeForm()
    {
        return view('auth.passwords.reset-with-code');
    }

    /**
     * Traite la réinitialisation avec identifiant (username) + code.
     */
    public function resetWithCode(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'code' => 'required|string|size:' . self::CODE_LENGTH,
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'code.size' => 'Le code doit contenir ' . self::CODE_LENGTH . ' caractères.',
        ]);

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [strtolower(trim($request->username))])
            ->first();

        if (! $user) {
            return back()->withErrors(['username' => 'Aucun compte avec cet identifiant.']);
        }

        $emailKey = $user->email ?: 'id:' . $user->id;
        $record = DB::table('password_reset_tokens')->where('email', $emailKey)->first();

        if (! $record || ! Hash::check(strtoupper($request->code), $record->token)) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(self::CODE_EXPIRE_MINUTES)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $emailKey)->delete();
            return back()->withErrors(['code' => 'Ce code a expiré. Demandez-en un nouveau à l\'administrateur.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_reset_tokens')->where('email', $emailKey)->delete();

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.');
    }

    private function authorizeGenerate(): void
    {
        $user = auth()->user();
        if (! $user->hasRoleOrAbove('manageur')) {
            abort(403, 'Seuls les manageurs et au-dessus peuvent générer un code de réinitialisation.');
        }
    }
}
