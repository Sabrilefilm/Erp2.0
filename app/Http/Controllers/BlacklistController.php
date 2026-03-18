<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function index(Request $request)
    {
        $query = Blacklist::with('ajoutePar')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%");
            });
        }

        $blacklist = $query->paginate(20)->withQueryString();

        return view('blacklist.index', compact('blacklist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'raison' => 'nullable|string|max:500',
        ]);

        $exists = Blacklist::where('username', $request->username)->exists();
        if ($request->filled('phone')) {
            $exists = $exists || Blacklist::where('phone', $request->phone)->exists();
        }
        if ($exists) {
            return back()->with('error', 'Cet utilisateur est déjà dans la liste.');
        }

        Blacklist::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->filled('phone') ? $request->phone : null,
            'raison' => $request->raison,
            'ajoute_par' => $request->user()->id,
        ]);

        return back()->with('success', 'Entrée ajoutée à la liste.');
    }

    public function destroy(Blacklist $blacklist)
    {
        $blacklist->delete();
        return back()->with('success', 'Entrée retirée de la liste.');
    }
}
