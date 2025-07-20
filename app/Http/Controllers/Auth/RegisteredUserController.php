<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        // 1) Validate everything
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|confirmed|min:8',
            'role'            => 'required|in:receiver,organization,partner',

            // only required for org or partner
            'partner_name'    => 'required_if:role,organization,partner|string',
            'contact_email'   => 'required_if:role,organization,partner|email',
            'contact_phone'   => 'required_if:role,organization,partner|string',
            'address'         => 'required_if:role,organization,partner|string',
            'type'            => 'required_if:role,organization,partner|string',

            // file uploads
            'profile_image'   => 'nullable|image|max:2048',
            'org_document'    => 'nullable|mimes:pdf,jpg,png|max:4096',
        ]);

        // 2) Store files (if any)
        $profilePath = null;
        if ($request->hasFile('profile_image')) {
            $profilePath = $request->file('profile_image')
                                 ->store('profiles', 'public');
        }

        $docPath = null;
        if ($request->hasFile('org_document')) {
            $docPath = $request->file('org_document')
                               ->store('documents', 'public');
        }

        // 3) Create the User
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => $data['role'],
            'profile_image' => $profilePath,
        ]);

        event(new Registered($user));

        // 4) If partner or org, create the Partner row
        if (in_array($data['role'], ['organization', 'partner'])) {
            Partner::create([
                'user_id'       => $user->id,             // <— link back to users.id
                'name'          => $data['partner_name'],
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'address'       => $data['address'],
                'type'          => $data['type'],
                'status'        => 'pending',
                'document_path' => $docPath,
                'level'         => 1,
                'role'          => $user->role,
            ]);
        }

        // 5) Log them in & redirect
        auth()->login($user);

        return redirect()->route('dashboard');
    }
}
