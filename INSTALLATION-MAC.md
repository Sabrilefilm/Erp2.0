# Installer PHP et Composer sur Mac

Tu as l’erreur `command not found: php` et `command not found: composer` parce que PHP et Composer ne sont pas installés (ou pas dans le PATH). Suis ces étapes.

---

## Option 1 : Homebrew (recommandé)

### 1. Installer Homebrew (si ce n’est pas déjà fait)

Ouvre le **Terminal** et exécute :

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

À la fin, le script peut te demander d’ajouter Homebrew au PATH (exécute les commandes qu’il affiche, souvent quelque chose comme) :

```bash
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

### 2. Installer PHP

```bash
brew install php
```

### 3. Installer Composer

```bash
brew install composer
```

### 4. Vérifier

Ferme et rouvre le Terminal, puis :

```bash
php -v
composer -v
```

Si les deux commandes affichent une version, c’est bon.

---

## Option 2 : Sans Homebrew (téléchargement manuel)

1. **PHP** : va sur https://www.php.net/downloads et télécharge la version pour macOS, ou utilise un installeur comme **Laravel Herd** (https://herd.laravel.com) qui installe PHP + outils utiles.
2. **Composer** : https://getcomposer.org/download/ — suis les instructions pour macOS (télécharger `composer.phar` et le mettre dans ton PATH).

---

## Ensuite : lancer l’ERP

Une fois PHP et Composer installés, dans le dossier du projet :

```bash
cd "/Users/sabri/Documents/ERP Unions V2"
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Puis ouvre **http://localhost:8000** dans ton navigateur.
