<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Donation;   
use App\Models\Partner; 
use Inertia\Inertia;



class AdminController extends Controller
{
public function index()
{
    // Récupère simplement tous les utilisateurs, sans relation roles()
    $users     = User::all();
    $donations = Donation::with('user')
                            ->orderBy('created_at', 'desc')
                            ->get();
    $partners  = Partner::withCount('donations')->get();

    return Inertia::render('Admin/Dashboard', [
        'users'     => $users,
        'donations' => $donations,
        'partners'  => $partners,
    ]);
}


}
