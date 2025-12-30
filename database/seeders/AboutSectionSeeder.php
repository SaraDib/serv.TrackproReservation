<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSection;

class AboutSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicate seeding
        if (AboutSection::query()->exists()) {
            return;
        }

        AboutSection::create([
            'title' => 'À Propos de TrackPro',
            'description' => "TrackPro révolutionne le suivi commercial avec une plateforme SaaS temps réel qui transforme vos équipes terrain en force de vente ultra-performante.",
            'image' => null,
            'features' => [
                [ 'id' => 1, 'number' => '96%',  'label' => 'Réduction Temps Réaction' ],
                [ 'id' => 2, 'number' => '+25%', 'label' => 'Productivité Commerciale' ],
                [ 'id' => 3, 'number' => '85%',  'label' => 'Couverture Géographique' ],
            ],
            'button_text' => 'En Savoir Plus',
            'button_link' => '#services',
            'is_active' => true,
        ]);
    }
}