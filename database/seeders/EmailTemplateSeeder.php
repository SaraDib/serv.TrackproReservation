<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Bienvenue - Template Standard',
                'type' => 'welcome',
                'subject' => 'Bienvenue chez {{company_name}} !',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #007bff; }
        .logo { font-size: 28px; font-weight: bold; color: #007bff; }
        .content { padding: 30px 0; }
        .welcome-message { font-size: 24px; color: #007bff; margin-bottom: 20px; }
        .button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        <div class="content">
            <h2 class="welcome-message">Bienvenue {{user_name}} !</h2>
            <p>Nous sommes ravis de vous accueillir dans notre communauté. Votre compte a été créé avec succès.</p>
            <p>Voici vos informations de connexion :</p>
            <ul>
                <li><strong>Email :</strong> {{user_email}}</li>
                <li><strong>Date de création :</strong> {{creation_date}}</li>
            </ul>
            <p>Pour commencer à utiliser nos services, cliquez sur le bouton ci-dessous :</p>
            <a href="{{login_url}}" class="button">Accéder à mon compte</a>
            <p>Si vous avez des questions, n\'hésitez pas à nous contacter à {{support_email}}.</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
            <p>{{company_address}}</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Bienvenue {{user_name}} !

Nous sommes ravis de vous accueillir chez {{company_name}}. Votre compte a été créé avec succès.

Informations de connexion :
- Email : {{user_email}}
- Date de création : {{creation_date}}

Pour accéder à votre compte : {{login_url}}

Pour toute question, contactez-nous : {{support_email}}

{{company_name}} - {{company_address}}
© {{current_year}} Tous droits réservés.',
                'variables' => json_encode([
                    'company_name', 'user_name', 'user_email', 'creation_date', 
                    'login_url', 'support_email', 'current_year', 'company_address'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Réinitialisation Mot de Passe - Template Standard',
                'type' => 'password_reset',
                'subject' => 'Réinitialisation de votre mot de passe - {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation Mot de Passe</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #dc3545; }
        .logo { font-size: 28px; font-weight: bold; color: #dc3545; }
        .content { padding: 30px 0; }
        .alert { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 30px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .security-note { background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        <div class="content">
            <h2>Réinitialisation de votre mot de passe</h2>
            <p>Bonjour {{user_name}},</p>
            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
            
            <div class="alert">
                <strong>⚠️ Important :</strong> Si vous n\'avez pas demandé cette réinitialisation, ignorez cet email.
            </div>
            
            <p>Pour créer un nouveau mot de passe, cliquez sur le bouton ci-dessous :</p>
            <a href="{{reset_url}}" class="button">Réinitialiser mon mot de passe</a>
            
            <div class="security-note">
                <h4>🔒 Note de sécurité :</h4>
                <ul>
                    <li>Ce lien expire dans {{expiry_time}}</li>
                    <li>Le lien ne peut être utilisé qu\'une seule fois</li>
                    <li>Votre nouveau mot de passe doit contenir au moins 8 caractères</li>
                </ul>
            </div>
            
            <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
            <p style="word-break: break-all; color: #007bff;">{{reset_url}}</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
            <p>Pour votre sécurité, ne partagez jamais ce lien.</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Réinitialisation de votre mot de passe - {{company_name}}

Bonjour {{user_name}},

Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.

⚠️ IMPORTANT : Si vous n\'avez pas demandé cette réinitialisation, ignorez cet email.

Pour créer un nouveau mot de passe, utilisez ce lien : {{reset_url}}

🔒 Note de sécurité :
- Ce lien expire dans {{expiry_time}}
- Le lien ne peut être utilisé qu\'une seule fois
- Votre nouveau mot de passe doit contenir au moins 8 caractères

{{company_name}}
© {{current_year}} Tous droits réservés.
Pour votre sécurité, ne partagez jamais ce lien.',
                'variables' => json_encode([
                    'company_name', 'user_name', 'reset_url', 'expiry_time', 'current_year'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Confirmation Réservation - Template Standard',
                'type' => 'reservation_confirmation',
                'subject' => 'Confirmation de votre réservation #{{reservation_id}} - {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Réservation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #28a745; }
        .logo { font-size: 28px; font-weight: bold; color: #28a745; }
        .content { padding: 30px 0; }
        .success-badge { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0; }
        .reservation-details { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee; }
        .button { display: inline-block; padding: 12px 30px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .next-steps { background-color: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        <div class="content">
            <div class="success-badge">
                <h2>✅ Réservation Confirmée !</h2>
                <p>Numéro de réservation : <strong>#{{reservation_id}}</strong></p>
            </div>
            
            <p>Bonjour {{contact_person}},</p>
            <p>Nous avons bien reçu votre demande de réservation. Voici les détails :</p>
            
            <div class="reservation-details">
                <h3>📋 Détails de la réservation</h3>
                <div class="detail-row">
                    <span><strong>Entreprise :</strong></span>
                    <span>{{company_name_client}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Contact :</strong></span>
                    <span>{{contact_person}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Email :</strong></span>
                    <span>{{email}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Téléphone :</strong></span>
                    <span>{{phone}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Service :</strong></span>
                    <span>{{service_type}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Budget :</strong></span>
                    <span>{{budget_range}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Délai :</strong></span>
                    <span>{{timeline}}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Date de réservation :</strong></span>
                    <span>{{reservation_date}}</span>
                </div>
            </div>
            
            <div class="next-steps">
                <h4>🚀 Prochaines étapes :</h4>
                <ol>
                    <li>Notre équipe va analyser votre demande dans les 24h</li>
                    <li>Vous recevrez un devis détaillé sous 48h</li>
                    <li>Un rendez-vous sera planifié pour discuter du projet</li>
                </ol>
            </div>
            
            <p>Vous pouvez suivre l\'état de votre réservation en cliquant sur le bouton ci-dessous :</p>
            <a href="{{tracking_url}}" class="button">Suivre ma réservation</a>
            
            <p>Pour toute question, contactez-nous à {{support_email}} ou au {{support_phone}}.</p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
            <p>{{company_address}}</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Confirmation de votre réservation #{{reservation_id}} - {{company_name}}

✅ RÉSERVATION CONFIRMÉE !

Bonjour {{contact_person}},

Nous avons bien reçu votre demande de réservation.

📋 DÉTAILS DE LA RÉSERVATION :
- Numéro : #{{reservation_id}}
- Entreprise : {{company_name_client}}
- Contact : {{contact_person}}
- Email : {{email}}
- Téléphone : {{phone}}
- Service : {{service_type}}
- Budget : {{budget_range}}
- Délai : {{timeline}}
- Date : {{reservation_date}}

🚀 PROCHAINES ÉTAPES :
1. Notre équipe va analyser votre demande dans les 24h
2. Vous recevrez un devis détaillé sous 48h
3. Un rendez-vous sera planifié pour discuter du projet

Suivre votre réservation : {{tracking_url}}

Contact : {{support_email}} | {{support_phone}}

{{company_name}} - {{company_address}}
© {{current_year}} Tous droits réservés.',
                'variables' => json_encode([
                    'company_name', 'reservation_id', 'contact_person', 'company_name_client',
                    'email', 'phone', 'service_type', 'budget_range', 'timeline',
                    'reservation_date', 'tracking_url', 'support_email', 'support_phone',
                    'current_year', 'company_address'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Mise à jour Réservation - Template Standard',
                'type' => 'reservation_update',
                'subject' => 'Mise à jour de votre réservation #{{reservation_id}} - {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour Réservation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #ffc107; }
        .logo { font-size: 28px; font-weight: bold; color: #ffc107; }
        .content { padding: 30px 0; }
        .update-badge { background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0; }
        .status-update { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .button { display: inline-block; padding: 12px 30px; background-color: #ffc107; color: #212529; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .timeline { background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        <div class="content">
            <div class="update-badge">
                <h2>🔄 Mise à jour de votre réservation</h2>
                <p>Réservation : <strong>#{{reservation_id}}</strong></p>
            </div>
            
            <p>Bonjour {{contact_person}},</p>
            <p>Nous vous informons d\'une mise à jour concernant votre réservation.</p>
            
            <div class="status-update">
                <h3>📊 Nouveau statut</h3>
                <p><strong>Statut précédent :</strong> {{previous_status}}</p>
                <p><strong>Nouveau statut :</strong> <span style="color: #28a745; font-weight: bold;">{{current_status}}</span></p>
                <p><strong>Date de mise à jour :</strong> {{update_date}}</p>
            </div>
            
            <div class="timeline">
                <h4>📅 Informations sur le projet :</h4>
                <p><strong>Service :</strong> {{service_type}}</p>
                <p><strong>Délai estimé :</strong> {{timeline}}</p>
                <p><strong>Prochaine étape :</strong> {{next_step}}</p>
            </div>
            
            <p><strong>Message de l\'équipe :</strong></p>
            <p style="font-style: italic; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                "{{update_message}}"
            </p>
            
            <p>Vous pouvez consulter tous les détails de votre réservation :</p>
            <a href="{{tracking_url}}" class="button">Voir ma réservation</a>
            
            <p>Pour toute question, notre équipe reste à votre disposition :</p>
            <ul>
                <li>📧 Email : {{support_email}}</li>
                <li>📞 Téléphone : {{support_phone}}</li>
                <li>💬 Chat en ligne : {{chat_url}}</li>
            </ul>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
            <p>{{company_address}}</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Mise à jour de votre réservation #{{reservation_id}} - {{company_name}}

🔄 MISE À JOUR DE VOTRE RÉSERVATION

Bonjour {{contact_person}},

Nous vous informons d\'une mise à jour concernant votre réservation.

📊 NOUVEAU STATUT :
- Statut précédent : {{previous_status}}
- Nouveau statut : {{current_status}}
- Date de mise à jour : {{update_date}}

📅 INFORMATIONS PROJET :
- Service : {{service_type}}
- Délai estimé : {{timeline}}
- Prochaine étape : {{next_step}}

MESSAGE DE L\'ÉQUIPE :
"{{update_message}}"

Consulter votre réservation : {{tracking_url}}

CONTACT :
📧 {{support_email}}
📞 {{support_phone}}
💬 {{chat_url}}

{{company_name}} - {{company_address}}
© {{current_year}} Tous droits réservés.',
                'variables' => json_encode([
                    'company_name', 'reservation_id', 'contact_person', 'previous_status',
                    'current_status', 'update_date', 'service_type', 'timeline', 'next_step',
                    'update_message', 'tracking_url', 'support_email', 'support_phone',
                    'chat_url', 'current_year', 'company_address'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Newsletter - Template Standard',
                'type' => 'newsletter',
                'subject' => '📰 {{newsletter_title}} - {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 30px 20px; border-radius: 10px 10px 0 0; }
        .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .newsletter-date { font-size: 14px; opacity: 0.9; }
        .content { padding: 30px 20px; }
        .article { margin: 30px 0; padding: 20px; border-left: 4px solid #667eea; background-color: #f8f9fa; }
        .article-title { color: #667eea; font-size: 20px; margin-bottom: 10px; }
        .article-meta { color: #666; font-size: 12px; margin-bottom: 15px; }
        .button { display: inline-block; padding: 12px 25px; background-color: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .stats-section { background-color: #e7f3ff; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #667eea; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; border-radius: 0 0 10px 10px; }
        .social-links { margin: 15px 0; }
        .social-links a { display: inline-block; margin: 0 10px; padding: 8px 12px; background-color: #667eea; color: white; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
            <div class="newsletter-date">Newsletter du {{newsletter_date}}</div>
        </div>
        <div class="content">
            <h1>{{newsletter_title}}</h1>
            <p>Bonjour {{subscriber_name}},</p>
            <p>{{newsletter_intro}}</p>
            
            <div class="article">
                <h3 class="article-title">{{article_1_title}}</h3>
                <div class="article-meta">📅 {{article_1_date}} | 👤 {{article_1_author}}</div>
                <p>{{article_1_excerpt}}</p>
                <a href="{{article_1_url}}" class="button">Lire la suite</a>
            </div>
            
            <div class="article">
                <h3 class="article-title">{{article_2_title}}</h3>
                <div class="article-meta">📅 {{article_2_date}} | 👤 {{article_2_author}}</div>
                <p>{{article_2_excerpt}}</p>
                <a href="{{article_2_url}}" class="button">Lire la suite</a>
            </div>
            
            <div class="stats-section">
                <h3>📊 Nos derniers chiffres</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{stat_1_number}}</div>
                        <div>{{stat_1_label}}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{stat_2_number}}</div>
                        <div>{{stat_2_label}}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{stat_3_number}}</div>
                        <div>{{stat_3_label}}</div>
                    </div>
                </div>
            </div>
            
            <div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h3>🎯 Offre spéciale abonnés</h3>
                <p>{{special_offer_text}}</p>
                <a href="{{special_offer_url}}" class="button" style="background-color: #ffc107; color: #212529;">Profiter de l\'offre</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>{{company_name}}</strong></p>
            <p>{{company_address}}</p>
            <div class="social-links">
                <a href="{{facebook_url}}">Facebook</a>
                <a href="{{twitter_url}}">Twitter</a>
                <a href="{{linkedin_url}}">LinkedIn</a>
            </div>
            <p>
                <a href="{{unsubscribe_url}}" style="color: #666;">Se désabonner</a> | 
                <a href="{{preferences_url}}" style="color: #666;">Gérer mes préférences</a>
            </p>
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => '📰 {{newsletter_title}} - {{company_name}}

Newsletter du {{newsletter_date}}

Bonjour {{subscriber_name}},

{{newsletter_intro}}

📰 ARTICLES DE LA SEMAINE :

1. {{article_1_title}}
   Par {{article_1_author}} - {{article_1_date}}
   {{article_1_excerpt}}
   Lire : {{article_1_url}}

2. {{article_2_title}}
   Par {{article_2_author}} - {{article_2_date}}
   {{article_2_excerpt}}
   Lire : {{article_2_url}}

📊 NOS DERNIERS CHIFFRES :
- {{stat_1_label}} : {{stat_1_number}}
- {{stat_2_label}} : {{stat_2_number}}
- {{stat_3_label}} : {{stat_3_number}}

🎯 OFFRE SPÉCIALE ABONNÉS :
{{special_offer_text}}
Profiter : {{special_offer_url}}

{{company_name}} - {{company_address}}

Réseaux sociaux :
Facebook : {{facebook_url}}
Twitter : {{twitter_url}}
LinkedIn : {{linkedin_url}}

Se désabonner : {{unsubscribe_url}}
Préférences : {{preferences_url}}

© {{current_year}} Tous droits réservés.',
                'variables' => json_encode([
                    'company_name', 'newsletter_date', 'newsletter_title', 'subscriber_name',
                    'newsletter_intro', 'article_1_title', 'article_1_date', 'article_1_author',
                    'article_1_excerpt', 'article_1_url', 'article_2_title', 'article_2_date',
                    'article_2_author', 'article_2_excerpt', 'article_2_url', 'stat_1_number',
                    'stat_1_label', 'stat_2_number', 'stat_2_label', 'stat_3_number', 'stat_3_label',
                    'special_offer_text', 'special_offer_url', 'company_address', 'facebook_url',
                    'twitter_url', 'linkedin_url', 'unsubscribe_url', 'preferences_url', 'current_year'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Promotion - Template Standard',
                'type' => 'promotional',
                'subject' => '🎉 {{promotion_title}} - Offre limitée {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion Spéciale</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white; text-align: center; padding: 30px 20px; border-radius: 10px 10px 0 0; position: relative; }
        .header::before { content: "🎉"; position: absolute; top: 10px; left: 20px; font-size: 24px; }
        .header::after { content: "🎉"; position: absolute; top: 10px; right: 20px; font-size: 24px; }
        .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .promo-badge { background-color: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; display: inline-block; margin-top: 10px; }
        .content { padding: 30px 20px; }
        .offer-highlight { background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); padding: 25px; border-radius: 10px; text-align: center; margin: 20px 0; }
        .discount-badge { font-size: 36px; font-weight: bold; color: #d63031; margin: 10px 0; }
        .countdown { background-color: #2d3436; color: white; padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0; }
        .countdown-item { display: inline-block; margin: 0 10px; }
        .countdown-number { font-size: 24px; font-weight: bold; display: block; }
        .countdown-label { font-size: 12px; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #00b894 0%, #00a085 100%); color: white; text-decoration: none; border-radius: 25px; margin: 20px 0; font-weight: bold; font-size: 18px; box-shadow: 0 4px 15px rgba(0,184,148,0.3); }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .feature-item { background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; }
        .footer { background-color: #2d3436; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .urgency { background-color: #ff7675; color: white; padding: 10px; text-align: center; font-weight: bold; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="urgency">⏰ OFFRE LIMITÉE - Plus que {{days_left}} jours !</div>
        <div class="header">
            <div class="logo">{{company_name}}</div>
            <h1>{{promotion_title}}</h1>
            <div class="promo-badge">Offre exclusive</div>
        </div>
        <div class="content">
            <p>Bonjour {{customer_name}},</p>
            <p>{{promotion_intro}}</p>
            
            <div class="offer-highlight">
                <h2>🔥 OFFRE EXCEPTIONNELLE 🔥</h2>
                <div class="discount-badge">{{discount_percentage}}% DE RÉDUCTION</div>
                <p><strong>{{offer_description}}</strong></p>
                <p>Prix habituel : <span style="text-decoration: line-through;">{{original_price}}€</span></p>
                <p style="font-size: 24px; color: #00b894;"><strong>Prix promo : {{promo_price}}€</strong></p>
                <p>Économisez : <strong>{{savings_amount}}€</strong></p>
            </div>
            
            <div class="countdown">
                <h3>⏳ Temps restant pour profiter de cette offre :</h3>
                <div class="countdown-item">
                    <span class="countdown-number">{{days_left}}</span>
                    <span class="countdown-label">JOURS</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number">{{hours_left}}</span>
                    <span class="countdown-label">HEURES</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number">{{minutes_left}}</span>
                    <span class="countdown-label">MINUTES</span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{cta_url}}" class="button">{{cta_text}}</a>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <h4>✅ {{feature_1_title}}</h4>
                    <p>{{feature_1_description}}</p>
                </div>
                <div class="feature-item">
                    <h4>✅ {{feature_2_title}}</h4>
                    <p>{{feature_2_description}}</p>
                </div>
                <div class="feature-item">
                    <h4>✅ {{feature_3_title}}</h4>
                    <p>{{feature_3_description}}</p>
                </div>
            </div>
            
            <div style="background-color: #e17055; color: white; padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0;">
                <h3>⚡ BONUS EXCLUSIF ⚡</h3>
                <p>{{bonus_offer}}</p>
            </div>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h4>📞 Besoin d\'aide ?</h4>
                <p>Notre équipe est disponible :</p>
                <ul>
                    <li>📧 Email : {{support_email}}</li>
                    <li>📞 Téléphone : {{support_phone}}</li>
                    <li>💬 Chat : {{chat_url}}</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p><strong>{{company_name}}</strong></p>
            <p>{{company_address}}</p>
            <p style="font-size: 12px; margin-top: 15px;">
                Cette offre est valable jusqu\'au {{expiry_date}}. 
                <a href="{{terms_url}}" style="color: #74b9ff;">Conditions générales</a> | 
                <a href="{{unsubscribe_url}}" style="color: #74b9ff;">Se désabonner</a>
            </p>
            <p>&copy; {{current_year}} {{company_name}}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => '🎉 {{promotion_title}} - Offre limitée {{company_name}}

⏰ OFFRE LIMITÉE - Plus que {{days_left}} jours !

Bonjour {{customer_name}},

{{promotion_intro}}

🔥 OFFRE EXCEPTIONNELLE 🔥
{{discount_percentage}}% DE RÉDUCTION

{{offer_description}}

Prix habituel : {{original_price}}€
Prix promo : {{promo_price}}€
Économisez : {{savings_amount}}€

⏳ TEMPS RESTANT :
{{days_left}} jours, {{hours_left}} heures, {{minutes_left}} minutes

{{cta_text}} : {{cta_url}}

✅ CE QUE VOUS OBTENEZ :
- {{feature_1_title}} : {{feature_1_description}}
- {{feature_2_title}} : {{feature_2_description}}
- {{feature_3_title}} : {{feature_3_description}}

⚡ BONUS EXCLUSIF :
{{bonus_offer}}

📞 BESOIN D\'AIDE ?
📧 {{support_email}}
📞 {{support_phone}}
💬 {{chat_url}}

{{company_name}} - {{company_address}}

Offre valable jusqu\'au {{expiry_date}}
Conditions : {{terms_url}}
Se désabonner : {{unsubscribe_url}}

© {{current_year}} Tous droits réservés.',
                'variables' => json_encode([
                    'company_name', 'promotion_title', 'customer_name', 'promotion_intro',
                    'discount_percentage', 'offer_description', 'original_price', 'promo_price',
                    'savings_amount', 'days_left', 'hours_left', 'minutes_left', 'cta_url',
                    'cta_text', 'feature_1_title', 'feature_1_description', 'feature_2_title',
                    'feature_2_description', 'feature_3_title', 'feature_3_description',
                    'bonus_offer', 'support_email', 'support_phone', 'chat_url',
                    'company_address', 'expiry_date', 'terms_url', 'unsubscribe_url', 'current_year'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Contact - Confirmation',
                'type' => 'contact_confirmation',
                'subject' => 'Merci pour votre message - {{company_name}}',
                'html_content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réception</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; text-align: center; padding: 30px 20px; border-radius: 10px 10px 0 0; }
        .content { padding: 30px 20px; }
        .summary { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .footer { background-color: #2d3436; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; padding: 12px 24px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px; margin-top: 10px; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{company_name}}</h1>
            <h2>Nous avons bien reçu votre message</h2>
        </div>
        <div class="content">
            <p>Bonjour {{contact_name}},</p>
            <p>Merci de nous avoir contactés. Voici un récapitulatif de votre demande :</p>
            <div class="summary">
                <p><strong>Sujet :</strong> {{subject}}</p>
                <p><strong>Votre email :</strong> {{contact_email}}</p>
                <p><strong>Message :</strong></p>
                <p>{{message}}</p>
            </div>
            <p>Notre équipe vous répondra dès que possible. Si nécessaire, vous pouvez répondre directement à cet email pour compléter vos informations.</p>
        </div>
        <div class="footer">
            <p>&copy; {{company_name}}</p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Bonjour {{contact_name}},\n\nNous avons bien reçu votre message envoyé à {{company_name}}.\n\nSujet : {{subject}}\nEmail : {{contact_email}}\n\nMessage :\n{{message}}\n\nNotre équipe vous répondra dès que possible. Merci pour votre confiance.\n\n— {{company_name}}',
                'variables' => json_encode([
                    'company_name', 'contact_name', 'contact_email', 'subject', 'message'
                ]),
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}