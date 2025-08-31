<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LandingpageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FoodRequestController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DonationMapController;
use App\Http\Controllers\PartnerRequestController;
use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlaceRequestController;
use App\Http\Controllers\PhotoController;
use App\Models\PlacePhoto;

// Route model binding pour les photos
Route::bind('photo', function ($value) {
    return PlacePhoto::findOrFail($value);
});

// Votre route de suppression
Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

Route::get('/', [LandingpageController::class, 'index']);

// Gardez les autres routes
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard2', [LandingpageController::class, 'index']);
   



Route::middleware('auth')->group(function () {
        Route::get('/my-places', [PlaceController::class, 'myPlaces'])->name('places.my');

    Route::get('/places', [PlaceController::class, 'index'])->name('places.index'); // voir tous les lieux
    Route::get('/places/create', [PlaceController::class, 'create'])->name('places.create');
    Route::post('/places', [PlaceController::class, 'store']); // poster un lieu
    Route::get('/places/{place}', [PlaceController::class, 'show']); // voir un lieu
    Route::post('/places/{place}/request', [PlaceRequestController::class, 'store']); // demander à utiliser
 // Place requests
 // routes/web.php
     Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy');

Route::delete('/places/{place}/photos/{photo}', [PlaceController::class, 'deletePhoto']);
Route::get('/places/{place}/edit', [PlaceController::class, 'edit'])->name('places.edit');
    Route::put('/places/{place}', [PlaceController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy');

    Route::get('/verifications', [\App\Http\Controllers\Admin\UserVerificationController::class, 'index'])->name('admin.verifications.index');
    Route::post('/verifications/{user}', [\App\Http\Controllers\Admin\UserVerificationController::class, 'update'])->name('admin.verifications.update');
    Route::get('/admin/volunteers/verifications', [\App\Http\Controllers\VolunteerController::class, 'adminPendingVerifications'])->name('admin.volunteers.verifications');
    Route::patch('/admin/volunteers/{id}/verify', [\App\Http\Controllers\VolunteerController::class, 'updateVerification'])->name('admin.volunteers.updateVerification');
    Route::post('/volunteer/upload-document', [\App\Http\Controllers\VolunteerController::class, 'uploadDocument'])->name('volunteer.upload-document');
    Route::post('/admin/request-document', [DocumentRequestController::class, 'store'])
    ->middleware(['auth']); // remplace par ton middleware admin si nécessaire
     Route::post('/admin/partners/{partner}/request-document', [PartnerController::class, 'requestDocument'])
    ->middleware('auth');

     // Admin generates request (protected by auth + admin middleware)
Route::post('/admin/partners/{partner}/request-document', [PartnerController::class, 'requestDocument'])
    ->middleware(['auth', 'can:admin-action']); // adapte le middleware

// Public upload page (the link contains token as query param)
Route::get('/documents/upload', function () {
    return inertia('Documents/Upload'); // Vue page reads token from querystring
});

// Upload handler — accepts token in querystring (no auth required)
Route::post('/documents/upload', [DocumentController::class, 'uploadWithToken'])->name('documents.upload');
     // web.php
Route::post('/documents/upload', [\App\Http\Controllers\DocumentController::class, 'upload'])
    ->middleware('auth');
    Route::get('/documents/upload-request/{token}', [DocumentController::class, 'showUploadForm'])
    ->name('documents.upload-form');
    Route::post('/documents/upload-request/{token}', [DocumentController::class, 'uploadWithToken'])
    ->name('documents.upload');

Route::get('/users/{id}/upload-request-document', [UploadController::class, 'showUserUpload']);
    Route::post('/users/{id}/upload-request-document', [UploadController::class, 'uploadUserDocument']);
    
    Route::get('/partners/{id}/upload-request-document', [UploadController::class, 'showPartnerUpload']);
    Route::post('/partners/{id}/upload-request-document', [UploadController::class, 'uploadPartnerDocument']);
    
    // Pour les admins
    Route::middleware(['admin'])->group(function () {
        Route::get('/documents/download/{type}/{id}', [UploadController::class, 'downloadDocument']);
        Route::delete('/documents/{type}/{id}', [UploadController::class, 'deleteDocument']);
    });
    Route::post('/admin/request-documents', [AdminController::class, 'requestDocuments'])->name('admin.request-documents');
     Route::post('/users/{user}/upload-request-document', [UserController::class, 'uploadRequestDocument']);
     Route::post('/partners/{partner}/upload-request-document', [PartnerController::class, 'uploadRequestDocument']);

    Route::get('/admin/notifications', function() {
    $notifications = auth()->user()->notifications
        ->where('type', 'App\\Notifications\\DocumentUploaded') // uniquement documents
        ->map(function ($notif) {
            return [
                'id' => $notif->id,
                'user_name' => $notif->data['user_name'],
                'document_path' => $notif->data['document_path'],
                'created_at' => $notif->created_at->toDateTimeString(),
            ];
        });

    return $notifications;
});


    Route::post('/profile/update-name-privacy', [ProfileController::class, 'updateNamePrivacy'])->name('profile.updatePrivacy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/preferences', [UserPreferenceController::class, 'index'])->name('preferences');
    Route::post(  '/preferences',           [UserPreferenceController::class, 'store'])->name('preferences.store');
    Route::patch( '/preferences/{id}',      [UserPreferenceController::class, 'update'])->name('preferences.update');
    Route::delete('/preferences/{id}',      [UserPreferenceController::class, 'destroy'])->name('preferences.destroy');
    Route::get('/donations/create', function () {
        return Inertia::render('Donations/CreateDonation');
    });
//Route::get('/documents/upload', [DocumentController::class, 'create'])->name('documents.upload');
Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/distributions',[DistributionController::class,'index'])->name('distributions');
    Route::get('/pickups',     [PickupController::class,      'index'])->name('pickups');
    Route::get('/volunteers',  [VolunteerController::class,   'index'])->name('volunteers');
    Route::get('/partners',    [PartnerController::class,     'index'])->name('partners'); 
    Route::post('/volunteer/{donation}', [VolunteerController::class, 'store'])->name('volunteer.store');
    Route::get('/donations/{id}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/pickups/create', [PickupController::class, 'create'])->middleware('auth');
    Route::post('/pickups',       [PickupController::class, 'store'])->middleware('auth');
    Route::get('/mail-test', function() { Mail::raw('Test depuis Mailtrap', function ($message) {
        $message->to('test@example.com')->subject('Test Mailtrap');
    });
    return 'Mail sent!';
    });
    Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('donations.mine');
    Route::get('/donations/{id}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::put('/donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::get('/volunteer/needs', [VolunteerController::class, 'needs'])->name('volunteer.needs');
    Route::middleware('auth')->group(function () {
    Route::delete('/admin/users/{user}', fn($user) => \App\Models\User::findOrFail($user)->delete());
    Route::delete('/partners/{partner}', fn($partner) => \App\Models\Partner::findOrFail($partner)->delete());
    Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
    Route::get('/profile/donor', [ProfileController::class, 'donorProfile'])->name('profile.donor');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
     Route::patch('/partners/{partner}/status', [PartnerController::class, 'updateStatus']);
     Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
     Route::get('/volunteer/complete-profile', [App\Http\Controllers\VolunteerController::class, 'completeProfile'])->name('volunteer.complete-profile');
     Route::post('/volunteer/complete-profile', [App\Http\Controllers\VolunteerController::class, 'saveProfile'])->name('volunteer.save-profile');
     Route::get('/volunteer/{id}/edit',[VolunteerController::class, 'edit'])->name('volunteer.edit');
     Route::patch('/volunteer/{id}',[VolunteerController::class, 'update'])->name('volunteer.update');
     Route::post('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');
     Route::get('/food-requests',          [FoodRequestController::class, 'index'])->name('food-requests.index');
     Route::get('/food-requests/create',   [FoodRequestController::class, 'create'])->name('food-requests.create');
     Route::post('/food-requests',         [FoodRequestController::class, 'store'])->name('food-requests.store');
     Route::get('/food-requests/{foodRequest}', [FoodRequestController::class, 'show'])->name('food-requests.show');
     Route::post('/food-requests/{foodRequest}/contribute',[ContributionController::class, 'store'])->name('contributions.store')->middleware('auth');
     //Route::post('/food-requests/{foodRequest}/respond',[DonationController::class, 'respondToRequest'])->name('food-requests.respond')->middleware('auth');  
     Route::get('/requests',[FoodRequestController::class, 'publicIndex'])->name('requests.index');     
     Route::post('/food-requests/{foodRequest}/respond', [FoodRequestController::class, 'respondToRequest'])->name('food-requests.respond');
     Route::delete('/food-requests/{foodRequest}', [FoodRequestController::class, 'destroy'])->name('food-requests.destroy');
    Route::get('/donations-map', [DonationMapController::class, 'index'])->name('donations.map');
    Route::get('/api/donations/{donation}', [DonationMapController::class, 'show'])->name('api.donations.show');
    Route::post('/donations-map/update-location', [DonationMapController::class, 'updateLocation'])->name('donations.map.updateLocation');
    Route::post('/partners/respond', [PartnerController::class, 'respond'])->middleware('auth');
     Route::post('/food-requests/send', [FoodRequestController::class, 'send']);
          Route::post('/partner-requests/send', [PartnerRequestController::class, 'sendPartnerRequest']);
          Route::post('/food-requests/respond', [FoodRequestController::class, 'respond']);
     Route::delete('/notifications/{notification_id}', [NotificationController::class, 'destroy']);
     Route::post('/profile/update-image', [ProfileController::class, 'updateImage'])->name('profile.update-image');
     Route::get  ('/sponsorships/create', [SponsorshipController::class, 'create'])
         ->name('sponsorships.create');
    Route::post ('/sponsorships',        [SponsorshipController::class, 'store'])
         ->name('sponsorships.store');
         Route::get('/sponsors', [DashboardController::class, 'getSponsors']);
             Route::get('/sponsorships/create', [SponsorshipController::class, 'create'])
         ->name('sponsorships.create');
    Route::post('/sponsorships',      [SponsorshipController::class, 'store'])
         ->name('sponsorships.store');  


    // ADMIN : liste des demandes et actions
    Route::get  ('/admin/sponsorships',       [SponsorshipController::class, 'index'])
         ->name('admin.sponsorships.index');
    Route::post ('/admin/sponsorships/{id}',  [SponsorshipController::class, 'update'])
         ->name('admin.sponsorships.update');

    //–– Admin ––//
    // Tableau de bord admin
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])
         ->name('admin.dashboard');
    // Liste des utilisateurs
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'users'])
         ->name('admin.users');
    // Liste de toutes les donations (admin view)
    Route::get('/admin/donations', [App\Http\Controllers\AdminController::class, 'donations'])
         ->name('admin.donations');
    // Liste de tous les partenaires
    Route::get('/admin/partners', [App\Http\Controllers\AdminController::class, 'partners'])
         ->name('admin.partners');

    //–– Partner ––//
    // Profil du partner connecté
    Route::get('/partner/profile', [App\Http\Controllers\PartnerController::class, 'profile'])
         ->name('partner.profile');
     // Profile for volunteer
    Route::get('/volunteer/profile', [App\http\Controllers\VolunteerController::class, 'profile'])
         ->name('volunteer.profile');       
    // Historique des donations du partner
    Route::get('/partner/history', [App\Http\Controllers\PartnerController::class, 'history'])
         ->name('partner.history');

    //–– Donor ––//
    // (tu as déjà /my-donations → donations.mine)

    //–– Receiver ––//
    // Liste publique des donations à réclamer
    Route::get('/donations', [App\Http\Controllers\DonationController::class, 'index'])
         ->name('donations.index');
    // Cette route existe déjà plus bas pour le détail
    // Route::get('/donations/{id}', [DonationController::class, 'show'])->name('donations.show');
});




});

require __DIR__.'/auth.php';
