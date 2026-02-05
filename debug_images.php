<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Pick a user
$user = User::where('email', 'fabio@example.com')->first() ?? User::find(1);
if (!$user) {
    // Create temp user if none exists (unlikely in dev)
    $user = User::factory()->create();
}
Auth::login($user);

echo "User: " . $user->name . " (ID: " . $user->id . ")\n";

// Create a dummy image
Storage::fake('public');
$dummyFile = UploadedFile::fake()->image('avatar_test.jpg');

// 1. Upload Media
echo "Uploading dummy avatar...\n";
$media = $user->addMedia($dummyFile)->toMediaCollection('profile_images');
echo "Media Created: " . $media->id . " (" . $media->file_name . ")\n";

// 2. Set as Current
echo "Setting as Current Profile Image...\n";
$user->setCurrentProfileImage($media);
$user->refresh();

echo "profile_photo_path: " . $user->profile_photo_path . "\n";
echo "profile_photo_url: " . $user->profile_photo_url . "\n";
echo "Media URL: " . $media->getUrl() . "\n";

if ($user->profile_photo_path === $media->file_name) {
    echo "Warning: Path stores only filename. If 'getProfilePhotoUrlAttribute' is redundant, this breaks.\n";
}

// 3. Test Banner
echo "-----------------------------------\n";
echo "Uploading dummy banner...\n";
$dummyBanner = UploadedFile::fake()->image('banner_test.jpg');
$bannerMedia = $user->addMedia($dummyBanner)->toMediaCollection('creator_banners');

echo "Setting as Current Creator Banner...\n";
$user->setCurrentCreatorBanner($bannerMedia);
$user->refresh();

$currentBanner = $user->getCurrentCreatorBanner();
if ($currentBanner) {
    echo "Current Banner ID: " . $currentBanner->id . "\n";
    echo "Current Banner URL: " . $currentBanner->getUrl() . "\n";
} else {
    echo "ERROR: Current Banner NOT set.\n";
}
