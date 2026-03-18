# ERP 2.0 - Agence TikTok Management System

## 🚀 Nouveautés ERP 2.0

### ✨ Améliorations majeures
- **Interface moderne** avec TailwindCSS et Alpine.js
- **Performance optimisée** avec cache et assets minifiés
- **Déploiement automatisé** via GitHub Actions
- **Architecture scalable** pour la croissance

### 📋 Fonctionnalités
- Gestion des créateurs et équipes
- Suivi des performances et matchs
- Système de récompenses
- Messagerie interne
- Formations et quiz
- Rapports automatisés

## 🛠️ Stack Technique
- **Backend**: Laravel 11 + PHP 8.2
- **Frontend**: TailwindCSS + Alpine.js + Vite
- **Database**: MySQL/PostgreSQL
- **Déploiement**: GitHub Actions + Render/DigitalOcean

## 🚀 Déploiement rapide

### Option 1: Render (Recommandé)
1. Fork ce repository
2. Créez un compte sur https://render.com
3. Connectez GitHub et sélectionnez ce repo
4. Configurez les variables d'environnement
5. Déployez !

### Option 2: DigitalOcean App Platform
1. Créez un compte DigitalOcean
2. Importez ce repository
3. Configurez avec les commandes de build

### Option 3: VPS Personnel
```bash
git clone https://github.com/VOTRE_USERNAME/erp2.0.git
cd erp2.0
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## ⚙️ Configuration

### Variables d'environnement requises
```env
APP_NAME="ERP 2.0 - Agence TikTok"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votredomaine.com

DB_CONNECTION=mysql
DB_HOST=votre-db-host
DB_PORT=3306
DB_DATABASE=erp2.0
DB_USERNAME=votre-user
DB_PASSWORD=votre-password

OPENAI_API_KEY=votre-clé-openai
```

## 🔄 CI/CD

Le pipeline GitHub Actions automatise :
- ✅ Tests unitaires
- ✅ Build des assets
- ✅ Optimisation Laravel
- ✅ Package de déploiement
- ✅ Déploiement en production

## 📊 Coûts d'hébergement

- **Render**: Gratuit (limites) → ~$7/mois
- **DigitalOcean**: ~$5/mois minimum
- **VPS**: ~$5-10/mois

## 🤝 Contribuer

1. Fork le projet
2. Créez une branche `feature/nouvelle-fonctionnalite`
3. Committez vos changements
4. Push vers la branche
5. Ouvrez une Pull Request

---

**ERP 2.0** - La solution complète pour la gestion d'agence TikTok 🚀
