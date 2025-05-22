<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Entry;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Entry\EntryResource;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $activeVault = Auth::user()->getActiveVault();

        if (!$activeVault) {
            return Inertia::render('users/entries/Index', [
                'entires' => [],
                'message' => 'Aucun coffre actif sélectionné.'
            ]);
        }
        return Inertia::render('users/entries/Index', [
            'entries' => EntryResource::collection($activeVault->entries()->where('category', 'login')->get())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $auth =  User::find(Auth::user()->id);
        $vaultId = $auth->getActiveVault()->id;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'url' => 'nullable|string|url',
            'notes' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'category' => 'required|string|in:login,card,identity,secure_note,crypto,medical,wifi,document,other',
            'favorite' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $entry = new Entry();
        $entry->vault_id = $vaultId;
        $entry->title = $validated['title'];
        $entry->username = $validated['username'] ?? null;
        $entry->password = $validated['password'] ?? null;
        $entry->url = $validated['url'] ?? null;
        $entry->notes = $validated['notes'] ?? null;
        $entry->icon = $validated['icon'] ?? null;
        $entry->category = $validated['category'];
        $entry->favorite = $validated['favorite'] ?? false;
        $entry->save();

        // Sync tags
        if (isset($validated['tags'])) {
            $entry->tags()->sync($validated['tags']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'entry' => $entry->load('tags'),
                'message' => 'Entry created successfully'
            ], 201);
        }

        return redirect()->route('entries.index')->with('success', 'Entry created successfully');
    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
