<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeaderSetting;
use App\Models\HeroSlide;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\ContactSetting;
use App\Models\FooterSetting;
use App\Models\LegalPage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive onepage data.
     */
    public function run(): void
    {
        // ===== HEADER SETTINGS =====
        HeaderSetting::create([
            'logo_text' => 'TrackPro',
            'navigation_items' => json_encode([
                ['name' => 'Accueil', 'link' => '#hero'],
                ['name' => 'Plateforme', 'link' => '#about'],
                ['name' => 'Fonctionnalités', 'link' => '#services'],
                ['name' => 'Contact', 'link' => '#contact']
            ]),
            'cta_button_text' => 'Réserver',
            'cta_button_link' => '#contact'
        ]);

        // ===== HERO SLIDES =====
        // Main Hero Slide
        HeroSlide::create([
            'title' => 'Transformez votre Force Commerciale en Avantage Concurrentiel',
            'subtitle' => 'Suivi Commercial Temps Réel',
            'description' => 'Plateforme de supervision commerciale temps réel qui transforme les données terrain en insights actionnables. Visualisez, analysez et optimisez vos équipes commerciales avec une précision inégalée.',
            'badge_icon' => '📍',
            'badge_text' => 'Suivi Commercial Temps Réel',
            'stats' => json_encode([
                ['number' => '96%', 'label' => 'Réduction temps de réaction'],
                ['number' => '25%', 'label' => 'Augmentation productivité'],
                ['number' => '85%', 'label' => 'Couverture géographique']
            ]),
            'actions' => json_encode([
                ['type' => 'primary', 'icon' => '🚀', 'text' => 'Commencer', 'link' => '#contact'],
                ['type' => 'secondary', 'icon' => '📊', 'text' => 'Voir la démo', 'link' => '#demo']
            ]),
            'features' => json_encode([
                ['icon' => '🗺️', 'text' => 'Géolocalisation Live'],
                ['icon' => '📈', 'text' => 'Analytics Temps Réel'],
                ['icon' => '⚡', 'text' => 'Alertes Intelligentes']
            ]),
            'is_active' => true,
            'order' => 1
        ]);

        // Additional Hero Slides for variety
        HeroSlide::create([
            'title' => 'Optimisez vos Performances Commerciales',
            'subtitle' => 'Analytics Avancés',
            'description' => 'Transformez vos données commerciales en insights stratégiques. Prenez des décisions éclairées grâce à nos tableaux de bord intelligents et nos rapports automatisés.',
            'badge_icon' => '📊',
            'badge_text' => 'Analytics Temps Réel',
            'stats' => json_encode([
                ['number' => '300%', 'label' => 'ROI Amélioré'],
                ['number' => '45%', 'label' => 'Temps Économisé'],
                ['number' => '92%', 'label' => 'Précision Données']
            ]),
            'actions' => json_encode([
                ['type' => 'primary', 'icon' => '📈', 'text' => 'Découvrir', 'link' => '#about'],
                ['type' => 'secondary', 'icon' => '🎯', 'text' => 'Essai Gratuit', 'link' => '#contact']
            ]),
            'features' => json_encode([
                ['icon' => '📊', 'text' => 'Tableaux de Bord'],
                ['icon' => '🤖', 'text' => 'IA Prédictive'],
                ['icon' => '📱', 'text' => 'Mobile First']
            ]),
            'is_active' => true,
            'order' => 2
        ]);

        HeroSlide::create([
            'title' => 'Révolutionnez votre Gestion Terrain',
            'subtitle' => 'Supervision Intelligente',
            'description' => 'Supervisez vos équipes terrain avec une précision inégalée. Géolocalisation, planification automatique et optimisation des tournées pour maximiser votre efficacité.',
            'badge_icon' => '🎯',
            'badge_text' => 'Gestion Terrain Pro',
            'stats' => json_encode([
                ['number' => '60%', 'label' => 'Réduction Coûts'],
                ['number' => '150+', 'label' => 'Clients Actifs'],
                ['number' => '24/7', 'label' => 'Support Expert']
            ]),
            'actions' => json_encode([
                ['type' => 'primary', 'icon' => '🚀', 'text' => 'Démarrer', 'link' => '#contact'],
                ['type' => 'secondary', 'icon' => '📞', 'text' => 'Nous Appeler', 'link' => 'tel:+212522123456']
            ]),
            'features' => json_encode([
                ['icon' => '🗺️', 'text' => 'Cartographie Avancée'],
                ['icon' => '⏰', 'text' => 'Planification Auto'],
                ['icon' => '📋', 'text' => 'Rapports Détaillés']
            ]),
            'is_active' => true,
            'order' => 3
        ]);

        HeroSlide::create([
            'title' => 'Automatisez vos Processus Commerciaux',
            'subtitle' => 'Workflow Intelligent',
            'description' => 'Simplifiez vos opérations avec des workflows automatisés. De la prospection à la facturation, optimisez chaque étape de votre cycle commercial pour une efficacité maximale.',
            'badge_icon' => '⚙️',
            'badge_text' => 'Automatisation Pro',
            'stats' => json_encode([
                ['number' => '80%', 'label' => 'Tâches Automatisées'],
                ['number' => '3x', 'label' => 'Vitesse Traitement'],
                ['number' => '99.9%', 'label' => 'Disponibilité']
            ]),
            'actions' => json_encode([
                ['type' => 'primary', 'icon' => '⚡', 'text' => 'Automatiser', 'link' => '#services'],
                ['type' => 'secondary', 'icon' => '🎥', 'text' => 'Voir Vidéo', 'link' => '#video']
            ]),
            'features' => json_encode([
                ['icon' => '🔄', 'text' => 'Workflows Personnalisés'],
                ['icon' => '📧', 'text' => 'Notifications Auto'],
                ['icon' => '🔗', 'text' => 'Intégrations API']
            ]),
            'is_active' => true,
            'order' => 4
        ]);

        HeroSlide::create([
            'title' => 'Sécurisez et Contrôlez vos Données',
            'subtitle' => 'Sécurité Enterprise',
            'description' => 'Protection maximale de vos données sensibles avec chiffrement de niveau bancaire, sauvegardes automatiques et conformité RGPD. Votre tranquillité d\'esprit est notre priorité.',
            'badge_icon' => '🔒',
            'badge_text' => 'Sécurité Maximale',
            'stats' => json_encode([
                ['number' => '256-bit', 'label' => 'Chiffrement SSL'],
                ['number' => '100%', 'label' => 'Conformité RGPD'],
                ['number' => '0', 'label' => 'Faille Sécurité']
            ]),
            'actions' => json_encode([
                ['type' => 'primary', 'icon' => '🛡️', 'text' => 'Sécuriser', 'link' => '#contact'],
                ['type' => 'secondary', 'icon' => '📋', 'text' => 'Audit Gratuit', 'link' => '#audit']
            ]),
            'features' => json_encode([
                ['icon' => '🔐', 'text' => 'Authentification 2FA'],
                ['icon' => '💾', 'text' => 'Backup Automatique'],
                ['icon' => '🏛️', 'text' => 'Conformité Légale']
            ]),
            'is_active' => true,
            'order' => 5
        ]);

        // ===== ABOUT SECTIONS =====
        AboutSection::create([
            'title' => 'À Propos de TrackPro',
            'description' => 'TrackPro révolutionne le suivi commercial avec une plateforme SaaS temps réel qui transforme vos équipes terrain en force de vente ultra-performante. Notre solution combine géolocalisation précise, analytics avancés et supervision intelligente pour maximiser votre ROI commercial.',
            'image' => null,
            'features' => json_encode([
                ['id' => 1, 'number' => '96%', 'label' => 'Réduction Temps Réaction'],
                ['id' => 2, 'number' => '+25%', 'label' => 'Productivité Commerciale'],
                ['id' => 3, 'number' => '85%', 'label' => 'Couverture Géographique']
            ]),
            'button_text' => 'En Savoir Plus',
            'button_link' => '#services',
            'is_active' => true
        ]);

        // Alternative About Section
        AboutSection::create([
            'title' => 'Innovation & Excellence',
            'description' => 'Depuis notre création, nous nous engageons à fournir des solutions technologiques innovantes qui transforment la façon dont les entreprises gèrent leurs opérations commerciales. Notre expertise combine développement logiciel de pointe et compréhension approfondie des enjeux métier.',
            'image' => null,
            'features' => json_encode([
                ['id' => 1, 'number' => '500+', 'label' => 'Clients Satisfaits'],
                ['id' => 2, 'number' => '50+', 'label' => 'Projets Réalisés'],
                ['id' => 3, 'number' => '24/7', 'label' => 'Support Client'],
                ['id' => 4, 'number' => '99%', 'label' => 'Taux de Satisfaction']
            ]),
            'button_text' => 'Nos Réalisations',
            'button_link' => '#portfolio',
            'is_active' => false
        ]);

        // ===== SERVICES =====
        $services = [
            [
                'title' => 'Suivi GPS Avancé',
                'description' => 'Localisez vos véhicules et équipements en temps réel avec une précision GPS de haute qualité. Surveillance continue, historique des déplacements et alertes géographiques.',
                'icon' => 'gps',
                'image' => null,
                'features' => json_encode(['Géolocalisation temps réel', 'Historique complet', 'Alertes zones', 'Rapports détaillés']),
                'button_text' => 'Découvrir',
                'button_link' => '#contact',
                'price' => 199.99,
                'order' => 1,
                'is_active' => true
            ],
            [
                'title' => 'Gestion de Flotte',
                'description' => 'Optimisez la gestion de votre flotte avec des outils de planification et de maintenance. Réduisez vos coûts opérationnels et améliorez l\'efficacité.',
                'icon' => 'fleet',
                'image' => null,
                'features' => json_encode(['Planification routes', 'Maintenance préventive', 'Gestion carburant', 'Optimisation coûts']),
                'button_text' => 'En Savoir Plus',
                'button_link' => '#demo',
                'price' => 299.99,
                'order' => 2,
                'is_active' => true
            ],
            [
                'title' => 'Alertes Intelligentes',
                'description' => 'Recevez des notifications instantanées pour les événements importants et les anomalies. Système d\'alertes personnalisables et notifications multi-canaux.',
                'icon' => 'alerts',
                'image' => null,
                'features' => json_encode(['Alertes personnalisées', 'Notifications SMS/Email', 'Escalade automatique', 'Tableau de bord']),
                'button_text' => 'Configurer',
                'button_link' => '#setup',
                'price' => 149.99,
                'order' => 3,
                'is_active' => true
            ],
            [
                'title' => 'Rapports Automatisés',
                'description' => 'Générez des rapports détaillés sur l\'utilisation, les performances et les coûts. Analytics avancés et tableaux de bord personnalisables.',
                'icon' => 'reports',
                'image' => null,
                'features' => json_encode(['Rapports automatiques', 'Analytics avancés', 'Export multi-formats', 'Tableaux de bord']),
                'button_text' => 'Voir Démo',
                'button_link' => '#demo',
                'price' => 249.99,
                'order' => 4,
                'is_active' => true
            ],
            [
                'title' => 'Optimisation Itinéraires',
                'description' => 'Calculez les meilleurs itinéraires pour réduire les coûts et améliorer l\'efficacité. Algorithmes d\'optimisation avancés et planification intelligente.',
                'icon' => 'routes',
                'image' => null,
                'features' => json_encode(['Optimisation automatique', 'Planification multi-critères', 'Économies carburant', 'ROI mesurable']),
                'button_text' => 'Optimiser',
                'button_link' => '#optimize',
                'price' => 179.99,
                'order' => 5,
                'is_active' => true
            ],
            [
                'title' => 'Analytics Prédictifs',
                'description' => 'Anticipez les tendances et optimisez vos décisions grâce à l\'intelligence artificielle. Prédictions précises et recommandations personnalisées.',
                'icon' => 'analytics',
                'image' => null,
                'features' => json_encode(['IA prédictive', 'Machine Learning', 'Recommandations', 'Analyses prédictives']),
                'button_text' => 'Consulter',
                'button_link' => '#contact',
                'price' => 329.99,
                'order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // ===== CONTACT SETTINGS =====
        ContactSetting::create([
            'title' => 'Contactez-Nous',
            'description' => 'Prêt à démarrer votre prochain projet ? Discutons de la façon dont nous pouvons vous aider à atteindre vos objectifs commerciaux avec TrackPro.',
            'email' => 'hello@trackpro.com',
            'phone' => '+212 5 22 12 34 56',
            'address' => "123 Rue des Affaires\nQuartier Maarif\nCasablanca, Maroc 20000",
            'whatsapp' => '+212 6 12 34 56 78',
            'social_links' => json_encode([
                ['platform' => 'Facebook', 'url' => 'https://facebook.com/trackpro', 'icon' => 'facebook'],
                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/trackpro', 'icon' => 'linkedin'],
                ['platform' => 'Twitter', 'url' => 'https://twitter.com/trackpro', 'icon' => 'twitter'],
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/trackpro', 'icon' => 'instagram']
            ]),
            'form_settings' => json_encode([
                'submit_text' => 'Envoyer le Message',
                'success_message' => 'Votre message a été envoyé avec succès !',
                'fields' => [
                    ['name' => 'name', 'type' => 'text', 'placeholder' => 'Votre nom complet', 'required' => true],
                    ['name' => 'email', 'type' => 'email', 'placeholder' => 'Votre adresse email', 'required' => true],
                    ['name' => 'phone', 'type' => 'tel', 'placeholder' => 'Votre numéro de téléphone', 'required' => false],
                    ['name' => 'subject', 'type' => 'text', 'placeholder' => 'Sujet de votre message', 'required' => true],
                    ['name' => 'message', 'type' => 'textarea', 'placeholder' => 'Votre message détaillé', 'required' => true]
                ]
            ]),
            'is_active' => true
        ]);

        // ===== FOOTER SETTINGS =====
        FooterSetting::create([
            'company_name' => 'TrackPro',
            'description' => 'Solutions innovantes de suivi commercial et gestion de flotte pour les entreprises modernes. Nous transformons vos données terrain en avantage concurrentiel avec une technologie de pointe et un support expert 24/7.',
            'logo_image' => null,
            'quick_links' => json_encode([
                ['name' => 'Accueil', 'href' => '#hero'],
                ['name' => 'À Propos', 'href' => '#about'],
                ['name' => 'Fonctionnalités', 'href' => '#services'],
                ['name' => 'Contact', 'href' => '#contact'],
                ['name' => 'Blog', 'href' => '/blog'],
                ['name' => 'Support', 'href' => '/support']
            ]),
            'social_links' => json_encode([
                ['name' => 'Facebook', 'href' => 'https://facebook.com/trackpro', 'ariaLabel' => 'Suivez-nous sur Facebook'],
                ['name' => 'Twitter', 'href' => 'https://twitter.com/trackpro', 'ariaLabel' => 'Suivez-nous sur Twitter'],
                ['name' => 'LinkedIn', 'href' => 'https://linkedin.com/company/trackpro', 'ariaLabel' => 'Connectez-vous sur LinkedIn'],
                ['name' => 'Instagram', 'href' => 'https://instagram.com/trackpro', 'ariaLabel' => 'Suivez-nous sur Instagram'],
                ['name' => 'YouTube', 'href' => 'https://youtube.com/trackpro', 'ariaLabel' => 'Abonnez-vous à notre chaîne YouTube']
            ]),
            'copyright_text' => '© 2024 TrackPro. Tous droits réservés. Développé avec ❤️ au Maroc.',
            'email' => 'hello@trackpro.com',
            'phone' => '+212 5 22 12 34 56',
            'address' => '123 Rue des Affaires, Quartier Maarif, Casablanca, Maroc 20000',
            'is_active' => true
        ]);

        // ===== EMAIL TEMPLATES =====
        $this->call(EmailTemplateSeeder::class);

        // ===== ABOUT SECTION SEEDER =====
        $this->call(AboutSectionSeeder::class);

        // ===== LEGAL PAGES =====
        LegalPage::create([
            'title' => 'Politique de Confidentialité',
            'slug' => 'politique-confidentialite',
            'content' => '<h1>Politique de Confidentialité</h1><p>Nous respectons votre vie privée. Cette politique explique comment nous collectons, utilisons et protégeons vos données.</p>',
            'is_active' => true,
        ]);
        LegalPage::create([
            'title' => "Conditions d'utilisation",
            'slug' => 'conditions-utilisation',
            'content' => "<h1>Conditions d'utilisation</h1><p>En utilisant notre site et nos services, vous acceptez ces conditions et vous engagez à les respecter.</p>",
            'is_active' => true,
        ]);

        // ===== ADMIN USER SEEDER =====
        $this->call(AdminUserSeeder::class);
    }
}
