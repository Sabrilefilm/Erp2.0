<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->get('filter', 'all');

        $query = $user->notifications()->orderByDesc('created_at');
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->limit(50)->get();

        $nouveau = $notifications->filter(fn ($n) => $n->created_at->gte(now()->subHours(12)));
        $aujourdhui = $notifications->filter(fn ($n) => $n->created_at->lt(now()->subHours(12)) && $n->created_at->gte(now()->startOfDay()));
        $plusTot = $notifications->filter(fn ($n) => $n->created_at->lt(now()->startOfDay()));

        return view('notifications.index', [
            'notifications' => $notifications,
            'nouveau' => $nouveau,
            'aujourdhui' => $aujourdhui,
            'plusTot' => $plusTot,
            'filter' => $filter,
        ]);
    }

    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $data = is_array($notification->data) ? $notification->data : (array) $notification->data;
        $notification->markAsRead();

        $url = $data['url'] ?? route('dashboard');
        // Toujours rediriger vers le chemin relatif pour garder la session (évite déconnexion si URL absolue stockée)
        if (is_string($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
            $path = parse_url($url, PHP_URL_PATH);
            $query = parse_url($url, PHP_URL_QUERY);
            $url = ($path ?? '') . ($query ? '?' . $query : '');
        }
        if (empty($url)) {
            $url = route('dashboard');
        }

        return redirect()->to($url);
    }
}
