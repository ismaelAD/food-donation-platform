<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\PlacePhoto;


class PlaceController extends Controller
{
    use AuthorizesRequests;

public function index()
{
    $places = Place::with('photos')->get();
    
    // Debug : voir les données avant transformation
    \Log::info('Photos avant transformation:', $places->first()?->photos?->toArray() ?? []);
    
    // Transformer les URLs des photos
    $places = $places->map(function ($place) {
        $place->photos = $place->photos->map(function ($photo) {
            $originalPath = $photo->path;
            $photo->url = Storage::url($photo->path); // Utiliser 'path' au lieu de 'url'
            
            // Debug : voir la transformation
            \Log::info("Photo Path: {$originalPath} -> {$photo->url}");
            
            return $photo;
        });
        return $place;
    });
    
    return Inertia::render('PlacesList', compact('places'));
}

    public function create()
    {
        return Inertia::render('PlaceCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'google_maps_link' => 'nullable|url',
            'contact_info' => 'required|string',
            'availability' => 'required|date',
            'photos.*' => 'nullable|image|max:5120'
        ]);

        $place = Auth::user()->places()->create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $place->photos()->create([
                    'path' => $photo->store('places', 'public')
                ]);
            }
        }

        return redirect()->route('places.index');
    }

    public function show(Place $place)
    {
        $place->load('photos', 'requests.user');
        $isOwner = Auth::id() === $place->user_id;
        return Inertia::render('PlaceRequests', compact('place', 'isOwner'));
    }
public function edit(Place $place) 
{
    if (Auth::id() !== $place->user_id) {
        abort(403, 'Unauthorized');
    }
    
    // Eager load des photos si pas déjà fait
    if (!$place->relationLoaded('photos')) {
        $place->load('photos');
    }
    
    // Transformer les URLs
    $place->setRelation('photos', $place->photos->map(function ($photo) {
        $photo->url = Storage::url($photo->path);
        return $photo;
    }));
    
    return Inertia::render('PlaceEdit', compact('place'));
}

public function update(Request $request, Place $place)
{
    if (Auth::id() !== $place->user_id) {
        abort(403, 'Unauthorized');
    }

    $data = $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'address' => 'required|string',
        'google_maps_link' => 'nullable|url',
        'contact_info' => 'required|string',
        'availability' => 'required|date',
        'photos.*' => 'nullable|image|max:5120'
    ]);

    $place->update($data);

    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $place->photos()->create([
                'path' => $photo->store('places', 'public')
            ]);
        }
    }

    return redirect()->route('places.index');
}

public function destroy(Place $place)
{
    if (Auth::id() !== $place->user_id) {
        abort(403, 'Unauthorized');
    }

    $place->delete();

    return;
}

public function myPlaces()
{
    $places = auth()->user()->places()->with('photos')->get();
    return Inertia::render('MyPlaces', compact('places'));
}
public function deletePhoto(Place $place, PlacePhoto $photo)
{
    // Vérifier que la photo appartient bien à ce place
    if ($photo->place_id !== $place->id) {
        abort(404, 'Photo not found for this place');
    }
    
    // Vérifier que l'utilisateur est propriétaire du place
    if (Auth::id() !== $place->user_id) {
        abort(403, 'Unauthorized');
    }
    
    // Supprimer le fichier du storage
    if ($photo->path && Storage::disk('public')->exists($photo->path)) {
        Storage::disk('public')->delete($photo->path);
    }
    
    // Supprimer en base
    $photo->delete();
    
    // Rediriger vers la page d'édition avec un message de succès
    return redirect()->back()->with('success', 'Photo deleted successfully');
}
}
