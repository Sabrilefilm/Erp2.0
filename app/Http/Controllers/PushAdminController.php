<?php

namespace App\Http\Controllers;

use App\Jobs\SendPushNotificationJob;
use App\Models\NotificationTemplate;
use App\Models\PushNotificationLog;
use App\Models\ScheduledPushNotification;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PushAdminController extends Controller
{
    public function __construct(
        protected WebPushService $webPush
    ) {}

    public function index(): View
    {
        $configured = $this->webPush->isConfigured();
        $recentLogs = PushNotificationLog::with('sender')->orderByDesc('sent_at')->limit(5)->get();
        $scheduledCount = ScheduledPushNotification::whereNull('sent_at')->where('send_at', '>', now())->count();
        return view('push-admin.index', [
            'configured' => $configured,
            'recentLogs' => $recentLogs,
            'scheduledCount' => $scheduledCount,
        ]);
    }

    public function sendForm(): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $templates = NotificationTemplate::where('active', true)->orderBy('label')->get();
        $roles = User::ROLE_LABELS;
        return view('push-admin.send', [
            'users' => $users,
            'templates' => $templates,
            'roles' => $roles,
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'target_type' => 'required|in:user,role,all',
            'target_value' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
            'template_key' => 'nullable|string|max:100',
        ]);

        if (! $this->webPush->isConfigured()) {
            return back()->with('error', 'Les notifications push ne sont pas configurées (clés VAPID manquantes).');
        }

        $targetType = $request->input('target_type');
        $targetValue = $request->input('target_value');
        if ($targetType === 'user' && empty($targetValue)) {
            return back()->withErrors(['target_value' => 'Veuillez sélectionner un utilisateur.']);
        }
        if ($targetType === 'role' && empty($targetValue)) {
            return back()->withErrors(['target_value' => 'Veuillez sélectionner un rôle.']);
        }

        SendPushNotificationJob::dispatch(
            $request->input('title'),
            $request->input('body') ?? '',
            $targetType,
            $targetValue ?: null,
            $request->user()->id,
            $request->input('template_key'),
            []
        );

        return redirect()->route('push-admin.index')->with('success', 'Notification mise en file d\'envoi.');
    }

    public function templates(): View
    {
        $templates = NotificationTemplate::orderBy('label')->get();
        return view('push-admin.templates', ['templates' => $templates]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => 'required|string|max:80|unique:notification_templates,key',
            'label' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
            'active' => 'nullable|boolean',
        ]);
        NotificationTemplate::create([
            'key' => $request->input('key'),
            'label' => $request->input('label'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'active' => $request->boolean('active', true),
        ]);
        return back()->with('success', 'Modèle créé.');
    }

    public function updateTemplate(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
            'active' => 'nullable|boolean',
        ]);
        $template->update([
            'label' => $request->input('label'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'active' => $request->boolean('active', true),
        ]);
        return back()->with('success', 'Modèle mis à jour.');
    }

    public function scheduledForm(): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $templates = NotificationTemplate::where('active', true)->orderBy('label')->get();
        $roles = User::ROLE_LABELS;
        $scheduled = ScheduledPushNotification::with('creator')->whereNull('sent_at')->where('send_at', '>', now())->orderBy('send_at')->get();
        return view('push-admin.scheduled', [
            'users' => $users,
            'templates' => $templates,
            'roles' => $roles,
            'scheduled' => $scheduled,
        ]);
    }

    public function storeScheduled(Request $request): RedirectResponse
    {
        $request->validate([
            'target_type' => 'required|in:user,role,all',
            'target_value' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
            'template_key' => 'nullable|string|max:100',
            'send_at_date' => 'required|date',
            'send_at_time' => 'required|string|max:5',
        ]);

        $sendAt = \Carbon\Carbon::parse($request->input('send_at_date') . ' ' . $request->input('send_at_time'));
        if ($sendAt->isPast()) {
            return back()->withErrors(['send_at_date' => 'La date et l\'heure doivent être futures.']);
        }

        $targetType = $request->input('target_type');
        $targetValue = $request->input('target_value');
        if ($targetType === 'user' && empty($targetValue)) {
            return back()->withErrors(['target_value' => 'Veuillez sélectionner un utilisateur.']);
        }
        if ($targetType === 'role' && empty($targetValue)) {
            return back()->withErrors(['target_value' => 'Veuillez sélectionner un rôle.']);
        }

        ScheduledPushNotification::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'target_type' => $targetType,
            'target_value' => $targetValue ?: null,
            'send_at' => $sendAt,
            'created_by' => $request->user()->id,
            'template_key' => $request->input('template_key'),
        ]);

        return back()->with('success', 'Notification planifiée.');
    }

    public function history(Request $request): View
    {
        $logs = PushNotificationLog::with('sender')->orderByDesc('sent_at')->paginate(20);
        return view('push-admin.history', ['logs' => $logs]);
    }
}
