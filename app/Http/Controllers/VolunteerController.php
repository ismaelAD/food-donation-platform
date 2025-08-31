<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\User as AppUser;
use App\Notifications\VolunteerDocumentUploaded;
use Illuminate\Support\Carbon;
use App\Models\Volunteer;
use App\Models\Donation;
use Illuminate\Http\Request;
use App\Notifications\VolunteerSignedUp;   
use Inertia\Inertia; 
use App\Notifications\VolunteerConfirmation;


class VolunteerController extends Controller
{

    /**
 * Upload an official document for volunteer verification (from profile)
 */
public function uploadDocument(Request $request)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
    ]);

    $user = $request->user();
    $volunteer = Volunteer::firstOrCreate(['user_id' => $user->id]);

    // store file on public disk
    $path = $request->file('document')->store('volunteer_documents', 'public');

    // update volunteer
    $volunteer->document_path = $path;
    $volunteer->verification_status = 'pending';
    $volunteer->verification_requested_at = Carbon::now();
    $volunteer->verification_note = null;
    $volunteer->save();

    // notify all admins
    $admins = AppUser::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new VolunteerDocumentUploaded($volunteer, $path));
    }

    return back()->with('success', 'Document uploaded and sent for review.');
}

/**
 * Admin: list volunteers pending verification
 */
public function adminPendingVerifications()
{
    $this->authorize('viewAny', Volunteer::class); // or check role admin manually

    $volunteers = Volunteer::with('user')
        ->where('verification_status', 'pending')
        ->orderBy('verification_requested_at', 'desc')
        ->get();

    return Inertia::render('Admin/Volunteers/Verifications', [
        'volunteers' => $volunteers,
    ]);
}

/**
 * Admin: update verification status (approve/decline/suspend)
 */
public function updateVerification(Request $request, $id)
{
    // check admin
    if ($request->user()->role !== 'admin') {
        abort(403);
    }

    $data = $request->validate([
        'status' => 'required|in:approved,declined,suspended',
        'note' => 'nullable|string',
    ]);

    $volunteer = Volunteer::findOrFail($id);
    $volunteer->verification_status = $data['status'];
    $volunteer->verification_note = $data['note'] ?? null;
    $volunteer->save();

    // notify the volunteer user
    $volunteer->user->notify(new \App\Notifications\VolunteerVerificationResult($volunteer, $data['status'], $data['note'] ?? ''));

    return redirect()->back()->with('success', 'Verification status updated.');
}

    public function index()
    {
        $donations = Donation::withCount('volunteers')
        ->where('need_volunteers', '>', 0)
        ->get(); 

        return Inertia::render('MyDonations', [
            'donations' => $donations
        ]);
    }

public function store(Request $request, Donation $donation)
{
    $userId   = auth()->id();
    $existing = Volunteer::where('user_id', $userId)->first();

    if ($existing && (!$existing->phone || !$existing->skills || !$existing->availability)) {
        // Pour Inertia/JS, on renvoie un JSON 422 afin d'attraper le message côté client
        return response()->json([
            'error'   => 'incomplete_profile',
            'message' => 'Please complete your volunteer profile before signing up.',
            'redirect'=> route('volunteer.complete-profile')
        ], 422);
    }

    $data = $request->validate([
        'phone'        => 'nullable|string',
        'skills'       => 'nullable|string',
        'availability' => 'nullable|string',
    ]);

    $volunteer = Volunteer::updateOrCreate(
        ['user_id' => $userId],
        $data
    );

    $donation->volunteers()->syncWithoutDetaching([$volunteer->id]);

    // Notifications
    $donation->user->notify(new VolunteerSignedUp(auth()->user(), $donation));
    auth()->user()->notify(new VolunteerConfirmation($donation));

    // Réponse JSON de succès
    return redirect()->back()->with('success', 'You have volunteered! The donor has been notified.');
}

public function completeProfile()
{
    // Récupère ou crée un profil vide pour l'utilisateur
$volunteer = Volunteer::firstOrCreate(
    ['user_id' => auth()->id()],
    ['phone' => null, 'skills' => null, 'availability' => null]
);

    return Inertia::render('Volunteers/CompleteProfile', [
        'volunteer' => $volunteer
    ]);
}


public function saveProfile(Request $request)
{
    $data = $request->validate([
        'phone' => 'required|string',
        'skills' => 'required|string',
        'availability' => 'required|string',
    ]);

    Volunteer::updateOrCreate(
        ['user_id' => auth()->id()],
        $data
    );

    return redirect()->route('volunteer.needs')
                     ->with('success', 'Profile completed! You can now volunteer.');
}


    public function show($id)
    {
        return Volunteer::with('user')->findOrFail($id);
    }

    public function edit($id = null)
{
    $volunteer = Volunteer::firstOrNew(['user_id' => auth()->id()]);

    return Inertia::render('Volunteers/EditProfile', [
        'volunteer' => $volunteer
    ]);
}


public function update(Request $request, $id)
{
    $data = $request->validate([
        'phone'        => 'nullable|string',
        'skills'       => 'nullable|string',
        'availability' => 'nullable|string',
    ]);

    // updateOrCreate selon ton id
    $volunteer = Volunteer::updateOrCreate(
        ['id' => $id],
        array_merge($data, ['user_id' => auth()->id()])
    );

    return redirect()
        ->route('volunteer.needs')
        ->with('success', 'Profile saved! You can now volunteer.');
}


    public function destroy($id)
    {
        $volunteer = Volunteer::findOrFail($id);
        $volunteer->delete();

        return response()->json(['message' => 'Volunteer deleted successfully.']);
    }

    public function needs()
{
    $userId = auth()->id();
    
    $donations = Donation::withCount('volunteers')
        ->where('need_volunteers', '>', 0)
        ->whereRaw('need_volunteers > (
            SELECT COUNT(*) 
            FROM volunteers 
            INNER JOIN donation_volunteer ON volunteers.id = donation_volunteer.volunteer_id 
            WHERE donations.id = donation_volunteer.donation_id
        )')
        ->with(['volunteers' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->get()
        ->map(function ($donation) use ($userId) {
            $donation->is_volunteered = $donation->volunteers->contains('user_id', $userId);
            return $donation;    
        });

    $hasVolunteerProfile = Volunteer::where('user_id', $userId)->exists();
          $volunteer = null;
    if (auth()->check()) {
        $volunteer = auth()->user()->volunteer()->select('id','verification_status')->first();
    }

    
    return Inertia::render('Volunteers/Needs', [
        'donations' => $donations,
        'hasVolunteerProfile' => $hasVolunteerProfile,
        'volunteer' => $volunteer,
    ]);
}
    
    public function profile(Request $request)
{
    $volunteer = $request->user()->volunteerProfile;
    return Inertia::render('Volunteer/Profile', ['volunteer' => $volunteer]);
}
    

}
