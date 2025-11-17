<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollectionFactory extends Factory {
    protected $model = Collection::class;

    public function definition() {
        return [
            'creator_id' => User::factory(), // Associa automaticamente un nuovo utente
            'owner_id' => null, // Sarà impostato manualmente
            'epp_project_id' => null, // Updated from epp_id to epp_project_id
            'collection_name' => $this->faker->word . "'s Collection",
            'description' => $this->faker->sentence,
            'type' => 'image', // Default: standard collection
            'status' => 'draft', // Default: draft
            'is_published' => false,
            'featured_in_guest' => false, // Default: non featured
            'featured_position' => null, // Default: posizione automatica
            'position' => 1,
            'EGI_number' => $this->faker->randomDigit,
            'floor_price' => $this->faker->randomFloat(2, 0, 100), // Prezzo minimo casuale
        ];
    }
}
