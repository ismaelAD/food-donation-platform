<?php

namespace App\Http\Controllers;

use App\Models\FoodRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Donation;
use App\Models\FoodRequestDonation;
use App\Models\Partner;
use App\Notifications\OrganizationRequestSent;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PartnershipRequestReceived;
use App\Models\PartnerRequest;



class FoodRequestController extends Controller
{
    public function index(Request $request)
{
    $orgId = $request->user()->id;

    $requests = FoodRequest::with(['foodRequestDonations', 'contributions.partner'])
        ->where('organization_id', $orgId)
        ->orderBy('created_at', 'desc')
        ->get(); // ou ->paginate(10) si tu veux la pagination

    return Inertia::render('FoodRequests/Index', [
        'requests' => $requests,
    ]);
}

public function respond(Request $request)
{
    $request->validate([
        'request_id' => 'required|exists:partner_requests,id',
        'status' => 'required|in:accepted,rejected',
    ]);

    $partnerRequest = PartnerRequest::findOrFail($request->request_id);
    $partnerRequest->status = $request->status;
    $partnerRequest->save();

    return back()->with('success');
}




public function send(Request $request)
{
    $request->validate([
        'partner_id' => 'required|exists:partners,id',
    ]);

    $partner = Partner::find($request->partner_id);
    $organization = auth()->user()->partner; // si partner = organization

    // Crée la demande (food request ou liaison directe selon ton modèle)
    $foodRequest = FoodRequest::create([
        'title' => 'New Request from ' . $organization->name,
        'description' => 'Automatic request sent to partner.',
        'organization_id' => $organization->id,
        'status' => 'pending',
    ]);

    // Notifie le partenaire
    $partner->user->notify(new \App\Notifications\FoodRequestReceived($foodRequest));

    return back()->with('success', 'Request sent to partner.');
}

public function sendPartnerRequest(Request $request)
{
    $request->validate([
        'partner_id' => 'required|exists:partners,id',
    ]);

    // Créer la demande de partenariat
    $partnerRequest = new PartnerRequest(); // Assure-toi que tu as ce modèle
    $partnerRequest->partner_id = $request->partner_id;
    $partnerRequest->user_id = auth()->user()->id; // L'utilisateur actuel qui envoie la demande
    $partnerRequest->status = 'pending'; // Par exemple, le statut initial
    $partnerRequest->save();

    // Envoi une notification si besoin
    $partner = Partner::find($request->partner_id);
    $partner->user->notify(new PartnerRequestReceived($partnerRequest));

    return response()->json(['message' => 'Request sent successfully!']);
}


public function sendRequestToPartner(Request $request)
{
    $partner = Partner::findOrFail($request->partner_id);
    $organization = auth()->user()->partner;

    // (Création de la requête ici...)

$partner->user->notify(new PartnershipRequestReceived($organization));

    return back()->with('success', 'Request sent to partner');
}

    public function publicIndex(Request $request)
{
    $requests = FoodRequest::where('needed_before', '>=', now())
        ->orderBy('needed_before', 'asc')
        ->get();

    return Inertia::render('FoodRequests/PublicIndex', [
        'requests' => $requests,
    ]);
}

    public function create()
    {
        return Inertia::render('FoodRequests/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string',
            'description'   => 'nullable|string',
            'quantity'      => 'nullable|integer|min:1',
            'target_amount' => 'nullable|numeric|min:0',
            'needed_before' => 'nullable|date|after:now',
            'paypal_link' => 'nullable|url',

        ]);

        $request->user()->foodRequests()->create($data);

        return redirect()->route('food-requests.index')
            ->with('success', 'Food request created.');
    }

    public function show(FoodRequest $foodRequest)
    {
        $foodRequest->load(['foodRequestDonations.user', 'contributions.partner']);
        return Inertia::render('FoodRequests/Show', [
            'request' => $foodRequest,
        ]);
    }

    public function respondToRequest(Request $request, FoodRequest $foodRequest)
{
    $data = $request->validate([
        'quantity' => 'required|integer|min:1',
        'available_until' => 'required|date|after:now',
    ]);

    FoodRequestDonation::create([
        'food_request_id' => $foodRequest->id,
        'user_id' => $request->user()->id,
        'quantity' => $data['quantity'],
        'unit' => 'servings',
        'available_until' => $data['available_until'],
    ]);

    return back()->with('success', 'Your donation has been recorded.');
}
public function destroy(FoodRequest $foodRequest)
{
    // Vérifie que l'utilisateur est bien le propriétaire
    if ($foodRequest->organization_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $foodRequest->delete();

    return redirect()->route('food-requests.index')->with('success', 'Request deleted successfully.');
}


}
