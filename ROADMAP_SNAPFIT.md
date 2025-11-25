# 📋 Roadmap Projet SnapFit

## 📌 État actuel du projet
- ✅ Architecture MVC de base
- ✅ Configuration Twig
- ✅ Classes de modèles (Article, Favori, Recherche)
- ✅ Singleton de connexion BD
- ⚠️ DAO partiellement implémentés
- ❌ Pas de point d'entrée (index.php)
- ❌ Pas de routing
- ❌ Pas de templates fonctionnels

---

## 🎯 PHASE 1 : Infrastructure de base (Priorité HAUTE)

### 1.1 Point d'entrée et Routing
- [ ] Créer `public/index.php` comme point d'entrée unique
- [ ] Implémenter un système de routing simple
  ```php
  // Exemple: index.php?controller=article&action=list
  ```
- [ ] Finaliser `ControllerFactory::getController()`
- [ ] Gérer les erreurs 404

### 1.2 Compléter les DAO

#### ArticleDAO
- [ ] Implémenter `find($id)` - Récupérer un article par ID
- [ ] Implémenter `findAll()` - Récupérer tous les articles
- [ ] Implémenter `findBySearch($query)` - Recherche d'articles
- [ ] Implémenter `create(Article $article)` - Créer un article
- [ ] Implémenter `update(Article $article)` - Modifier un article
- [ ] Implémenter `delete($id)` - Supprimer un article

#### FavoriDAO
- [ ] Implémenter `find($id)` - Récupérer un favori
- [ ] Implémenter `findAll()` - Tous les favoris
- [ ] Implémenter `findByUser($userId)` - Favoris d'un utilisateur
- [ ] Implémenter `create(Favori $favori)` - Ajouter aux favoris
- [ ] Implémenter `delete($id)` - Retirer des favoris
- [ ] Implémenter `isInFavorites($userId, $articleId)` - Vérifier si en favoris

#### RechercheDAO
- [ ] Corriger les références aux classes
- [ ] Ajouter méthode `findRecent($userId, $limit = 10)`
- [ ] Ajouter méthode `deleteOldSearches($days = 30)`

### 1.3 Configuration et Sécurité
- [ ] Créer `.env` pour les variables sensibles
- [ ] Installer `vlucas/phpdotenv` via Composer
- [ ] Déplacer les constantes DB dans `.env`
- [ ] Créer `.gitignore` avec :
  ```
  /vendor/
  .env
  .idea/
  composer.lock
  /cache/
  /uploads/
  ```
- [ ] Créer `.env.example` comme modèle

---

## 🎨 PHASE 2 : Interface utilisateur

### 2.1 Structure des templates
- [ ] Créer `templates/base.html.twig` (layout principal)
- [ ] Créer `templates/components/navbar.html.twig`
- [ ] Créer `templates/components/footer.html.twig`
- [ ] Créer `templates/pages/home.html.twig`
- [ ] Créer `templates/pages/search.html.twig`
- [ ] Créer `templates/pages/results.html.twig`
- [ ] Créer `templates/pages/favorites.html.twig`
- [ ] Créer `templates/errors/404.html.twig`
- [ ] Créer `templates/errors/500.html.twig`

### 2.2 Assets et styles
- [ ] Créer structure de dossiers :
  ```
  public/
  ├── index.php
  ├── assets/
  │   ├── css/
  │   │   └── style.css
  │   ├── js/
  │   │   └── app.js
  │   └── images/
  │       └── logo.png
  ```
- [ ] Intégrer Bootstrap 5 ou Tailwind CSS
- [ ] Créer CSS personnalisé pour SnapFit
- [ ] Implémenter design responsive
- [ ] Ajouter favicon

---

## 👤 PHASE 3 : Gestion utilisateurs

### 3.1 Modèle Utilisateur
- [ ] Créer `modeles/utilisateur.class.php`
- [ ] Créer `modeles/utilisateur.dao.php`
- [ ] Créer table SQL `utilisateurs`
- [ ] Implémenter hashage des mots de passe (password_hash)

### 3.2 Authentification
- [ ] Créer `controllers/auth.controller.php`
- [ ] Implémenter inscription
- [ ] Implémenter connexion
- [ ] Implémenter déconnexion
- [ ] Gérer les sessions PHP
- [ ] Créer middleware d'authentification

### 3.3 Templates utilisateur
- [ ] Créer `templates/user/login.html.twig`
- [ ] Créer `templates/user/register.html.twig`
- [ ] Créer `templates/user/profile.html.twig`
- [ ] Créer `templates/user/settings.html.twig`
- [ ] Ajouter formulaire "Mot de passe oublié"

---

## 🔍 PHASE 4 : Fonctionnalités métier

### 4.1 Recherche d'articles
- [ ] Créer `controllers/search.controller.php`
- [ ] Intégrer API de recherche (définir laquelle)
- [ ] Implémenter upload d'images
- [ ] Traitement et validation des images
- [ ] Sauvegarde de l'historique de recherche
- [ ] Affichage des résultats avec pagination

### 4.2 Système de favoris
- [ ] Créer `controllers/favoris.controller.php`
- [ ] Action AJAX pour ajouter/retirer favoris
- [ ] Page de gestion des favoris
- [ ] Export des favoris (CSV/PDF)
- [ ] Partage de liste de favoris

### 4.3 Filtres et tri
- [ ] Filtrer par prix
- [ ] Filtrer par disponibilité
- [ ] Filtrer par site/marque
- [ ] Tri par pertinence/prix/date
- [ ] Sauvegarde des préférences de filtres

---

## 📊 PHASE 5 : Base de données

### 5.1 Créer le script SQL complet
- [ ] Script de création des tables
- [ ] Script de données de test
- [ ] Indexes pour optimisation
- [ ] Contraintes et clés étrangères

### 5.2 Migrations
- [ ] Système de versioning de la BD
- [ ] Scripts de migration up/down

---

## 🚀 PHASE 6 : Optimisations et fonctionnalités avancées

### 6.1 Performance
- [ ] Mise en cache des résultats API
- [ ] Lazy loading des images
- [ ] Minification CSS/JS
- [ ] Compression GZIP
- [ ] CDN pour les assets

### 6.2 Fonctionnalités avancées
- [ ] Notifications par email
- [ ] Alertes de prix
- [ ] Comparateur de prix
- [ ] Recommandations personnalisées
- [ ] API REST pour app mobile

### 6.3 Tests
- [ ] Tests unitaires avec PHPUnit
- [ ] Tests d'intégration
- [ ] Tests de l'interface (Selenium)
- [ ] Tests de charge

---

## 🔒 PHASE 7 : Sécurité

- [ ] Protection CSRF
- [ ] Validation des entrées
- [ ] Prepared statements (déjà OK avec PDO)
- [ ] Rate limiting
- [ ] HTTPS obligatoire
- [ ] Headers de sécurité
- [ ] Audit de sécurité

---

## 📱 PHASE 8 : Déploiement

- [ ] Choisir hébergeur de production
- [ ] Configuration serveur web
- [ ] Pipeline CI/CD
- [ ] Monitoring et logs
- [ ] Backups automatiques
- [ ] Documentation technique

---

## 🎯 Actions immédiates (À faire MAINTENANT)

### Semaine 1
1. [ ] **Créer `public/index.php`** avec routing basique
2. [ ] **Finir `controller_factory.class.php`**
3. [ ] **Implémenter ArticleDAO complet**
4. [ ] **Créer template de base avec navbar**
5. [ ] **Tester connexion BD avec page de test**

### Semaine 2
1. [ ] **Créer système d'authentification**
2. [ ] **Implémenter upload d'images**
3. [ ] **Intégrer Bootstrap/Tailwind**
4. [ ] **Créer page d'accueil**
5. [ ] **Premiers tests de recherche**

---

## 📝 Notes et décisions à prendre

### Questions à résoudre :
- [ ] Quelle API utiliser pour la recherche d'articles ?
- [ ] Stockage des images : local ou cloud (S3, Cloudinary) ?
- [ ] Framework CSS : Bootstrap ou Tailwind ?
- [ ] Besoin d'un framework PHP (Symfony, Laravel) ou rester vanilla ?
- [ ] App mobile native ou PWA ?

### Ressources nécessaires :
- [ ] Accès API de recherche produits
- [ ] Serveur de développement
- [ ] Base de données de test
- [ ] Outils de versioning (Git déjà OK)

---

## 📈 Indicateurs de succès

- [ ] 100% des tests passent
- [ ] Temps de chargement < 3 secondes
- [ ] Score Lighthouse > 90
- [ ] 0 vulnérabilité critique
- [ ] Documentation complète
- [ ] Code review effectuée

---

## 🔄 Changelog

### [Date] - Version initiale
- Création de la roadmap
- Définition des phases
- Priorisation des tâches

---

## 💡 Conseils de développement

1. **Commencer simple** : Une fonctionnalité qui marche vaut mieux qu'une parfaite qui n'existe pas
2. **Tester régulièrement** : Chaque DAO, chaque controller
3. **Commiter souvent** : Petits commits atomiques
4. **Documenter le code** : PHPDoc pour toutes les méthodes
5. **Sécurité d'abord** : Ne jamais faire confiance aux données utilisateur

---

## 🆘 Besoin d'aide ?

- Documentation PHP : https://www.php.net/docs.php
- Documentation Twig : https://twig.symfony.com/doc/
- Documentation PDO : https://www.php.net/manual/en/book.pdo.php
- Bootstrap : https://getbootstrap.com/
- Tailwind CSS : https://tailwindcss.com/

---

**Dernière mise à jour :** [Mettre la date]
**Progression globale :** 15% ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜
