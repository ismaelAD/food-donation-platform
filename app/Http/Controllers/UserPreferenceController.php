<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPreferenceController extends Controller
{
    /**
     * Affiche le formulaire de préférences et les matches
     */
    public function index()
    {
        $prefs = auth()->user()->preference;
        
        // Récupère aussi les catégories disponibles et matched donations si besoin
        $categories = ['Fruits', 'Vegetables', 'Meals', 'Desserts']; // ou depuis config/db
        $matched = []; // on peut peupler via un service de matching ici

        return Inertia::render('Preferences', [
            'preferences' => $prefs,
            'categories'  => $categories,
            'matched'     => $matched,
        ]);
    }

    /**
     * Crée les préférences
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'preferred_categories' => 'nullable|array',
            'min_quantity'         => 'nullable|integer|min:1',
            'max_distance'         => 'nullable|integer|min:1',
            'available_from'       => 'nullable|date_format:H:i',
            'available_until'      => 'nullable|date_format:H:i',
        ]);

        auth()->user()->preference()->create($data);

        // Redirige via Inertia vers la même page pour rafraîchir les props
        return redirect()->route('preferences');
    }

    /**
     * Met à jour les préférences
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'preferred_categories' => 'nullable|array',
            'min_quantity'         => 'nullable|integer|min:1',
            'max_distance'         => 'nullable|integer|min:1',
            'available_from'       => 'nullable|date_format:H:i',
            'available_until'      => 'nullable|date_format:H:i',
        ]);

        $pref = UserPreference::where('user_id', auth()->id())->findOrFail($id);
        $pref->update($data);

        return redirect()->route('preferences');
    }

    /**
     * Supprime les préférences
     */
    public function destroy($id)
    {
        $pref = UserPreference::where('user_id', auth()->id())->findOrFail($id);
        $pref->delete();

        return redirect()->route('preferences');
    }
}
