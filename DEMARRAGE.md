# Démarrer l'application ERP Agence TikTok

## 1. Prérequis
- **PHP 8.2+** installé
- **Composer** installé
- **Node.js** et **npm** (pour le frontend)

## 2. Installation (une seule fois)

Ouvrez un terminal dans le dossier du projet puis exécutez :

```bash
cd "/Users/sabri/Documents/ERP Unions V2"

# Dépendances PHP
composer install

# Fichier d'environnement et clé
cp .env.example .env
php artisan key:generate

# Base de données (MySQL/PostgreSQL configuré dans .env)
php artisan migrate
php artisan db:seed

# Dépendances frontend et build
npm install
npm run build
```

## 3. Lancer le serveur

```bash
php artisan serve
```

L’application sera accessible à : **http://localhost:8000**

## 4. Se connecter

- **Fondateur** : `fondateur@agence.local` / `password`
- **Directeur** : `directeur@agence.local` / `password`
- **Créateur** : `createur@agence.local` / `password`
- (voir README.md pour les autres comptes)

---

Si vous préférez le mode développement avec rechargement automatique du CSS/JS :

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Puis ouvrez **http://localhost:8000** dans votre navigateur.
