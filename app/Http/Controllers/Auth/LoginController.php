<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const FAILED_ATTEMPTS_MAX = 5;

    private const CACHE_KEY_PREFIX = 'login_fails:';

    private const BLOCK_DURATION_MINUTES = 30;

    public function showLoginForm(Request $request)
    {
        $captcha = $this->getOrCreateCaptchaInSession($request);
        return view('auth.login', [
            'showCaptcha' => $captcha !== null,
            'captchaNum1' => $captcha['num1'] ?? null,
            'captchaNum2' => $captcha['num2'] ?? null,
        ]);
    }

    public function login(Request $request)
    {
        $login = strtolower(trim((string) $request->input('username', '')));
        $password = $request->input('password');

        if (! Schema::hasColumn('users', 'username')) {
            throw ValidationException::withMessages([
                'username' => __('La connexion par utilisateur n\'est pas disponible. Exécutez : php artisan migrate'),
            ]);
        }

        $userByUsername = $login !== '' ? User::query()->whereRaw('LOWER(username) = ?', [$login])->first() : null;

        if ($userByUsername && $userByUsername->isLoginBlocked()) {
            return redirect()->route('compte-bloque-temporaire');
        }

        $cacheKey = self::CACHE_KEY_PREFIX . $login;
        $failedCount = (int) Cache::get($cacheKey, 0);

        if ($failedCount >= self::FAILED_ATTEMPTS_MAX) {
            $rules = [
                'username' => 'required|string',
                'password' => 'required',
                'captcha_answer' => 'required|integer',
            ];
            $request->validate($rules);

            $expected = $request->session()->get('login_captcha_sum');
            if ($expected === null || (int) $request->input('captcha_answer') !== (int) $expected) {
                if ($userByUsername) {
                    $userByUsername->update([
                        'login_blocked_until' => now()->addMinutes(self::BLOCK_DURATION_MINUTES),
                        'must_change_password' => true,
                    ]);
                }
                Cache::forget($cacheKey);
                $request->session()->forget(['login_captcha_num1', 'login_captcha_num2', 'login_captcha_sum']);
                return redirect()->route('compte-bloque-temporaire');
            }
            $request->session()->forget(['login_captcha_num1', 'login_captcha_num2', 'login_captcha_sum']);
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::query()->whereRaw('LOWER(username) = ?', [$login])->first();

        if (! $user || ! Hash::check($password, $user->getAuthPassword())) {
            $newCount = $failedCount + 1;
            Cache::put($cacheKey, $newCount, now()->addMinutes(30));
            if ($newCount >= self::FAILED_ATTEMPTS_MAX) {
                $this->setNewCaptchaInSession($request);
                throw ValidationException::withMessages([
                    'username' => __('Identifiants incorrects. Après plusieurs tentatives, répondez à la question ci-dessous pour continuer.'),
                ]);
            }
            throw ValidationException::withMessages([
                'username' => __('Identifiants incorrects.'),
            ]);
        }

        Cache::forget($cacheKey);
        $request->session()->forget(['login_captcha_num1', 'login_captcha_num2', 'login_captcha_sum']);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->must_change_password) {
            return redirect()->route('password.change-required');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function getOrCreateCaptchaInSession(Request $request): ?array
    {
        if ($request->session()->has('login_captcha_sum')) {
            return [
                'num1' => $request->session()->get('login_captcha_num1'),
                'num2' => $request->session()->get('login_captcha_num2'),
                'sum' => $request->session()->get('login_captcha_sum'),
            ];
        }
        return null;
    }

    private function setNewCaptchaInSession(Request $request): array
    {
        $num1 = random_int(1, 12);
        $num2 = random_int(1, 12);
        $sum = $num1 + $num2;
        $request->session()->put('login_captcha_num1', $num1);
        $request->session()->put('login_captcha_num2', $num2);
        $request->session()->put('login_captcha_sum', $sum);
        return ['num1' => $num1, 'num2' => $num2, 'sum' => $sum];
    }
}
