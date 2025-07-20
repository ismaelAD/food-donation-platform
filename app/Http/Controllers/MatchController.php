<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatchController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('preference');

        $pref = $user->preference;

        if (!$pref) {
            return Inertia::render('Preferences', [
                'user' => $user,
                'matchedDonations' => [],
                'message' => 'No preferences found',
            ]);
        }

        $query = Donation::query();

        if ($pref->preferred_categories) {
            $query->whereIn('category', $pref->preferred_categories);
        }

        $query->where('quantity', '>=', $pref->min_quantity);

        if ($pref->available_from && $pref->available_until) {
            $query->where('available_from', '<=', $pref->available_from)
                  ->where('available_until', '>=', $pref->available_until);
        }

        if ($pref->max_distance && $user->latitude && $user->longitude) {
            $query->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) < ?
            ", [$user->latitude, $user->longitude, $user->latitude, $pref->max_distance]);
        }

        $matches = $query->get();

        return Inertia::render('Preferences', [
            'user' => $user,
            'preferences' => $pref,
            'matched' => $matches,
            'categories' => ['Fruits', 'Vegetables', 'Meals', 'Desserts']

        ]);
    }
}
