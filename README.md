# ERP Agence TikTok

Application web ERP interne pour une agence TikTok. Accès par URL, réservé aux membres, stack Laravel + Blade + Tailwind.

## Stack

- **Backend** : Laravel 11
- **Frontend** : Blade + Tailwind CSS (Vite)
- **Auth** : Sessions Laravel (email + mot de passe)
- **Base** : MySQL ou PostgreSQL
- **Import** : Maatwebsite Excel (.xlsx), réservé au rôle Fondateur

## Installation

1. Cloner le projet et entrer dans le dossier.
2. Copier `.env.example` en `.env` et configurer la base de données.
3. Installer les dépendances et lancer les migrations :

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build
```

4. Lancer le serveur : `php artisan serve` (ou configurer Nginx/Apache).

## Comptes de test (après `db:seed`)

| Rôle         | Email                    | Mot de passe |
|-------------|---------------------------|--------------|
| Fondateur   | fondateur@agence.local    | password     |
| Directeur   | directeur@agence.local   | password     |
| Sous-Manager| sousmanager@agence.local  | password     |
| Agent       | agent@agence.local       | password     |
| Ambassadeur | ambassadeur@agence.local | password     |
| Créateur    | createur@agence.local    | password     |

## Rôles et droits

- **Fondateur** : accès total, import Excel, paramétrage, logs.
- **Directeur / Manager** : voir toutes les équipes (lecture), créer sous-manager/agent, commentaires, stats globales. Pas d’import Excel.
- **Sous-Manager** : son équipe, commentaires, stats de son périmètre.
- **Agent** : ses créateurs, mise à jour statuts/notes, stats individuelles.
- **Ambassadeur** : ses données et créateurs affiliés (lecture).
- **Créateur** : connexion, ses statistiques, statut/missions (lecture).

## Import Excel (Fondateur uniquement)

- Format : `.xlsx`
- Colonnes attendues : `nom`, `email`, `pseudo_tiktok`, `statut`, `equipe`, `agent_email`, `ambassadeur_email`, `notes`, `missions`, `stats_vues`, `stats_followers`, `stats_engagement`
- Un modèle est téléchargeable depuis la page Import (une fois connecté en tant que Fondateur).
- Route protégée par middleware `fondateur.only` : toute tentative non autorisée renvoie 403.

## Responsive

- **Desktop** : sidebar fixe.
- **Mobile / tablette** : menu burger, tableaux scrollables horizontalement, cartes empilées.

## Sécurité

- HTTPS recommandé en production.
- Sessions base de données, expiration configurable.
- Middlewares par rôle, Policies Laravel sur les ressources sensibles.
- Validation serveur systématique, pas de logique métier critique côté frontend.
