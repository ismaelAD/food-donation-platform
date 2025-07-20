<?php

namespace App\Http\Controllers;

use App\Models\Pickup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Donation;
use App\Notifications\PickupScheduled;
use App\Models\User;

class PickupController extends Controller
{
    public function index()
    {
        return Pickup::with(['user', 'donation'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);

        $data['user_id'] = auth()->id();
        $pickup = Pickup::create($data);

        $pickup->donation->user->notify(new PickupScheduled(auth()->user(), $pickup));

        return redirect()->route('donations.show', $data['donation_id'])
                         ->with('success', 'Pickup scheduled successfully!');
    }

    public function show($id)
    {
        $pickup = Pickup::with(['user', 'donation'])->findOrFail($id);
        return response()->json($pickup);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'scheduled_at' => 'date|after:now',
            'status' => 'in:pending,confirmed,canceled,completed',
            'notes' => 'nullable|string'
        ]);

        $pickup = Pickup::findOrFail($id);
        $pickup->update($data);

        return response()->json($pickup);
    }

    public function destroy($id)
    {
        $pickup = Pickup::findOrFail($id);
        $pickup->delete();

        return response()->json(['message' => 'Pickup deleted']);
    }

   public function create(Request $request)
    {
        $donation = Donation::with('user')
            ->findOrFail($request->query('donation_id'));

        return Inertia::render('Pickups/Create', [
            'donation' => $donation,
            'user' => auth()->user()
        ]);
    }

}
