<?php

use App\Models\Team;
use App\Models\User;

$teams = Team::all();

foreach ($teams as $team) {
    $team->users()->syncWithoutDetaching([$team->user_id => ['role' => 'creator']]);
}
