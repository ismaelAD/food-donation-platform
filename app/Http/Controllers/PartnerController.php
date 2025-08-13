<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\PartnerStatusUpdated;
use App\Notifications\PartnerLevelUp;
use App\Models\SponsorshipTier;


class PartnerController extends Controller
{
public function index()
    {
        $partners = Partner::withCount('donations')
            // eager‑load only the one active sponsorship whose end_at > now()
            ->with(['currentSponsorship.tier'])
            ->get();


        return Inertia::render('Partners', [
            'partners' => $partners,
            'sponsorshipTiers'  => SponsorshipTier::all(['id','name']),

        ]);
    }

    public function show($id)
    {
        $partner = Partner::with('currentSponsorship.tier')
            ->findOrFail($id);

        return Inertia::render('Partners/Show', [
            'partner'           => $partner,
            // still pass all tiers if you need to build a dropdown elsewhere
            'sponsorshipTiers'  => SponsorshipTier::all(['id','name']),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $partner = Partner::create($data);

        return response()->json($partner, 201);
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'string',
            'type' => 'string',
            'status' => 'in:pending,approved,suspended',
            'level' => 'integer|min:1'
        ]);

        $partner = Partner::findOrFail($id);
        $partner->update($data);

        return response()->json($partner);
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return response()->json(['message' => 'Partner deleted']);
    }

    public function profile(Request $request)
{
    $partner = $request->user()->partnerProfile;
    return Inertia::render('Partner/Profile', ['partner' => $partner]);
}

public function history(Request $request)
{
    $donations = $request->user()->partnerProfile->donations;
    return Inertia::render('Partner/History', ['donations' => $donations]);
}

public function updateStatus(Request $request, Partner $partner)
{
    $request->validate(['status' => 'required|in:pending,approved,suspended']);

    $partner->status = $request->status;
    $partner->save();

    $partner->user->notify(new PartnerStatusUpdated($partner));

    return back()->with('success', 'Status updated');
}


public function updateLevel()
{
    $partner = Partner::find($id); 
    $count    = $partner->donations()->count();
    $newLevel = min(floor($count / 10) + 1, 10);

    if ($partner->level !== $newLevel) {
        $partner->level = $newLevel;
        $partner->save();
        $partner->user->notify(new \App\Notifications\PartnerLevelUp($newLevel));
    }

    $this->user->notify(new PartnerLevelUp($newLevel));
}




}
