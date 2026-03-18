# Stack technique — Ultra ERP V2

Stack alignée sur la V1 (ultra.phoceenagency.fr) et bonnes pratiques Laravel.

## Backend

- **PHP** 8.2+
- **Laravel** 11
- **Base de données** : MySQL (configurable via `.env`)
- **Sessions** : database
- **File storage** : `storage/app` (public via `php artisan storage:link` pour `public/storage`)
- **Queues** : `sync` par défaut (passer à `database` ou `redis` pour jobs asynchrones)
- **Maatwebsite Excel** : import/export Excel (créateurs, horaires)

## Frontend

- **Vite** 5 — build (CSS + JS)
- **Tailwind CSS** 3.4 — utilitaires + thème Ultra (neon, ultra-bg, polices)
- **Alpine.js** — interactivité (dropdowns, modals, formulaires réactifs)
- **Axios** — requêtes HTTP (CSRF automatique via `bootstrap.js`)
- **Polices** : Poppins, Plus Jakarta Sans, Inter (Google Fonts)

## Thème & design

- **tailwind.config.js** : couleurs `neon` (blue, purple, pink, orange, green), `ultra-bg`, ombres, animations
- **CSS custom** : `resources/css/ultra-premium.css`, `ultra-ui.css` (variables, bottom sheet, composants)
- **Fallback** : si `build/manifest.json` absent, chargement Tailwind CDN + CSS statique (même rendu)

## Build

```bash
npm install
npm run build
```

En dev :

```bash
npm run dev
```

Les assets compilés sont servis par Vite en dev et par `public/build/` en prod.

## Rôles & permissions

- Rôles : Fondateur, Directeur, Sous-directeur, Manageur, Sous-manager, Agent, Ambassadeur, Créateur
- Middleware : `auth`, `role`, `fondateur.only`
- Policies : `User`, `Createur`

## Fonctionnalités techniques

- **Authentification** : login par identifiant + mot de passe (session Laravel)
- **Import Excel** : fondateur uniquement (template téléchargeable)
- **Rafraîchissement liste** : Utilisateurs (polling + fragment HTML)
- **Responsive** : sidebar desktop, bottom nav + menu tiroir mobile

## Évolutions possibles

- **Notifications temps réel** : Laravel Echo + Pusher (ou Soketi)
- **Queues** : `QUEUE_CONNECTION=database` + `php artisan queue:work` pour imports lourds
- **API** : routes `api.php` + sanctum pour app mobile / tierces
