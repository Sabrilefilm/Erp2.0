# Déploiement ERP Laravel sur GitHub

## Étapes suivantes :

### 1. Créer le repository GitHub
1. Allez sur https://github.com/new
2. Nommez-le : `erp-unions-agency`
3. Choisissez "Private" ou "Public"
4. Ne cochez pas "Add README"

### 2. Connecter votre repo local
```bash
git remote add origin https://github.com/VOTRE_USERNAME/erp-unions-agency.git
git branch -M main
git push -u origin main
```

### 3. Options d'hébergement (choisissez-en une) :

#### Option A : Render (Recommandé)
1. Créez un compte sur https://render.com
2. Connectez votre compte GitHub
3. Choisissez "New Web Service"
4. Sélectionnez votre repo `erp-unions-agency`
5. Configurez :
   - Environment: PHP
   - Build Command: `composer install && npm install && npm run build`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`
   - Plan: Free (disponible)

#### Option B : Vercel (Frontend uniquement)
1. Créez un compte sur https://vercel.com
2. Connectez GitHub
3. Importez votre projet
4. Vercel va détecter que c'est du PHP et suggérera des alternatives

#### Option C : DigitalOcean App Platform
1. Créez un compte sur https://cloud.digitalocean.com
2. Choisissez "Apps"
3. Connectez GitHub
4. Configurez avec les mêmes commandes que Render

### 4. Variables d'environnement
Dans votre service d'hébergement, ajoutez ces variables :
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_DATABASE=erp_tiktok`
- `DB_USERNAME=votre_user`
- `DB_PASSWORD=votre_password`

## Coûts estimés :
- **Render** : Gratuit (limites) puis ~$7/mois
- **DigitalOcean** : ~$5/mois minimum
- **VPS personnel** : ~$5/mois

Le GitHub Actions que j'ai créé va automatiquement :
- Tester votre code
- Builder les assets
- Créer un package de déploiement

Quelle option d'hébergement préférez-vous ?
