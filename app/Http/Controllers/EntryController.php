<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Entry;
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
            'entries' => EntryResource::collection($activeVault->entries()->get())
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
        //
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
