<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Braids élégantes', 'name_de' => 'Elegante Braids', 'name_fr' => 'Braids élégantes', 'name_en' => 'Elegant braids', 'description_de' => 'Sorgfältig gearbeitete Braids, abgestimmt auf deine Persönlichkeit und die gewünschte Länge.', 'description_fr' => 'Des tresses soignées, adaptées à votre personnalité et à la longueur souhaitée.', 'description_en' => 'Carefully crafted braids tailored to your personality and desired length.', 'slug' => 'braids-elegantes', 'category' => 'Tresses', 'description' => 'Un style soigné, adapté à votre personnalité et à la longueur souhaitée.', 'price' => 70, 'price_from' => true, 'duration_minutes' => 180, 'at_salon' => true, 'at_home' => true, 'travel_fee' => 10, 'sort_order' => 1],
            ['name' => 'Cornrows', 'name_de' => 'Cornrows', 'name_fr' => 'Cornrows', 'name_en' => 'Cornrows', 'description_de' => 'Präzise Linien und ein klares Finish mit viel Liebe zum Detail.', 'description_fr' => 'Des lignes précises et un rendu net, avec une finition attentive.', 'description_en' => 'Precise lines and a clean look with careful finishing.', 'slug' => 'cornrows', 'category' => 'Tresses', 'description' => 'Des lignes précises et un rendu net, avec une finition attentive.', 'price' => 45, 'price_from' => true, 'duration_minutes' => 120, 'at_salon' => true, 'at_home' => true, 'travel_fee' => 10, 'sort_order' => 2],
            ['name' => 'Pose de perruque', 'name_de' => 'Perückeninstallation', 'name_fr' => 'Pose de perruque', 'name_en' => 'Wig installation', 'description_de' => 'Vorbereitung, Installation und Finish für ein natürliches, harmonisches Ergebnis.', 'description_fr' => 'Préparation, pose et finitions pour un résultat naturel et harmonieux.', 'description_en' => 'Preparation, installation and finishing for a natural, harmonious result.', 'slug' => 'pose-perruque', 'category' => 'Coiffage', 'description' => 'Préparation, pose et finitions pour un résultat naturel et harmonieux.', 'price' => 50, 'price_from' => false, 'duration_minutes' => 90, 'at_salon' => true, 'at_home' => false, 'travel_fee' => 0, 'sort_order' => 3],
            ['name' => 'Vanilles / Twists', 'name_de' => 'Vanilles / Twists', 'name_fr' => 'Vanilles / Twists', 'name_en' => 'Vanilla twists', 'description_de' => 'Eine schützende Frisur mit feinen, sorgfältigen Abschlüssen.', 'description_fr' => 'Une coiffure protectrice avec des finitions délicates.', 'description_en' => 'A protective style with delicate finishing.', 'slug' => 'vanilles-twists', 'category' => 'Tresses', 'description' => 'Une coiffure protectrice avec des finitions délicates.', 'price' => 60, 'price_from' => true, 'duration_minutes' => 150, 'at_salon' => true, 'at_home' => true, 'travel_fee' => 10, 'sort_order' => 4],
            ['name' => 'Coiffure événementielle', 'name_de' => 'Eventfrisur', 'name_fr' => 'Coiffure événementielle', 'name_en' => 'Event hairstyle', 'description_de' => 'Ein Styling für deine besonderen Momente.', 'description_fr' => 'Une mise en beauté pensée pour vos moments importants.', 'description_en' => 'A style designed for your special moments.', 'slug' => 'coiffure-evenementielle', 'category' => 'Événement', 'description' => 'Une mise en beauté pensée pour vos moments importants.', 'price' => 85, 'price_from' => true, 'duration_minutes' => 120, 'at_salon' => true, 'at_home' => true, 'travel_fee' => 15, 'sort_order' => 5],
            ['name' => 'Conseil coiffure', 'name_de' => 'Haarberatung', 'name_fr' => 'Conseil coiffure', 'name_en' => 'Hair consultation', 'description_de' => 'Persönliche Beratung zur passenden Frisur und Pflege.', 'description_fr' => 'Un échange pour choisir le style et l’entretien adaptés.', 'description_en' => 'A consultation to choose the right style and care routine.', 'slug' => 'conseil-coiffure', 'category' => 'Conseil', 'description' => 'Un échange pour choisir le style et l’entretien adaptés.', 'price' => 20, 'price_from' => false, 'duration_minutes' => 30, 'at_salon' => true, 'at_home' => false, 'travel_fee' => 0, 'sort_order' => 6],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
