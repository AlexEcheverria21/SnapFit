# 📸 SnapFit - Project Title

> Courte description du projet (1-2 lignes).

## 📑 Table des Matières
- [Contexte du Projet](#contexte-du-projet)
- [Fonctionnalités](#fonctionnalités)
- [Architecture Technique](#architecture-technique)
- [Modélisation des Données](#modélisation-des-données)
- [Technologies](#technologies)
- [Installation](#installation)
- [Utilisation](#utilisation)

---

## 🌍 Contexte du Projet
*   **Objectif** : (Ex: Lutter contre la Fast Fashion via l'analyse d'images).
*   **Public Visé** : (Consommateurs éco-responsables).

## 🚀 Fonctionnalités
*   **Authentification** : (Inscription, Connexion sécurisée...).
*   **Scan & Analyse** : (Recherche par image via API).
*   **Eco-Score** : (Detection Scam vs Eco).
*   **Espace Membre** : (Historique, Favoris).

## 🏗️ Architecture Technique
*   **Pattern** : MVC (Modèle - Vue - Contrôleur).
*   **Arborescence** :
    *   `controllers/` : Logique métier.
    *   `modeles/` : Accès données (DAO).
    *   `views/` : Interface utilisateur (Twig).
    *   `public/` : Assets (CSS/JS).

## 📊 Modélisation des Données (Diagramme de Classes)
*   *(Insérer ici l'image du diagramme UML ou le lien)*
*   **Entités Principales** : `Utilisateur`, `Article`, `Domaine`, `Favori`.

## 🛠️ Technologies
*   **Backend** : PHP 8+, POO.
*   **Frontend** : HTML5, CSS3, Twig (Moteur de templates), Bootstrap.
*   **Base de Données** : MySQL / MariaDB.
*   **APIs Externes** : SerpAPI (Google Lens).
*   **Outils** : Git, Composer.

## ⚙️ Installation
1.  **Prérequis** : (XAMPP, Composer...).
2.  **Clonage** : `git clone ...`
3.  **Base de Données** :
    *   Importer [sql/structure_v3_final.sql](cci:7://file:///c:/Users/GYMPt/OneDrive/Desktop/snapgit/SnapFit/sql/structure_v3_final.sql:0:0-0:0).
    *   Configurer `config/config.yaml`.
4.  **Dépendances** : `composer install`.

## 🎮 Utilisation
*   **Compte Admin** : (Login/Mdp par défaut).
*   **Scénario typique** : (Uploader une image -> Voir les résultats -> Ajouter aux favoris).

---
*Projet réalisé par [Noms de l'équipe] dans le cadre de [Nom du Module/Formation].*