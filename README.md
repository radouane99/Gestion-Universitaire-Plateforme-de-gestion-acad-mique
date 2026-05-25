# Gestion Universitaire — Plateforme de gestion académique

Application web Laravel pour la gestion complète d'un établissement universitaire : emplois du temps, notes, absences, salles de classe virtuelles, examens, et communication.

## Fonctionnalités principales

| Module | Description |
|---|---|
| **Emplois du temps** | Planification des cours par groupe, module, professeur et salle |
| **Notes** | Saisie et calcul automatique des notes (CC1, CC2, Examen) par module |
| **Absences** | Suivi des présences, dépôt et validation de justificatifs |
| **Salles virtuelles** | Espace classe avec publications, commentaires et fichiers |
| **Examens** | Planification des examens, convocations, sessions |
| **Réservations** | Réservation de salles avec vérification de conflits |
| **Messagerie** | Conversations directes entre utilisateurs |
| **Notifications** | Notifications académiques en temps réel |
| **Administration** | Gestion complète des utilisateurs, filières, groupes, modules |

## Rôles et Comptes de Démonstration

Le système intègre trois rôles avec les comptes de démonstration pré-générés suivants (mot de passe : `password`) :

- **Admin** — `admin@university.com` : gestion complète de la plateforme
- **Professeur** — `prof@university.com` : saisie des notes/absences, publications dans les classes
- **Étudiant** — `student@university.com` : consultation des notes/absences, dépôt de justificatifs, participation aux classes

## Prérequis

- PHP 8.2+
- Composer
- Node.js 18+ & NPM
- SQLite (par défaut) ou MySQL/MariaDB

## Installation

```bash
# 1. Cloner le projet
git clone <url> && cd <dossier>

# 2. Installer les dépendances
composer install
npm install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données
touch database/database.sqlite    # SQLite uniquement
php artisan migrate --seed

# 5. Compiler les assets
npm run build

# 6. Lancer le serveur
php artisan serve
```

## Migration des justificatifs (une seule fois)

Si d'anciens justificatifs se trouvent dans `public/justifications`, exécutez :

```bash
php scripts/migrate_justifications.php
```

Ce script déplace les fichiers vers `storage/app/justifications` (privé), met à jour la base de données, et supprime le dossier public.

## Architecture sécurité

- **Middleware global** : `SetLocale` uniquement
- **Middleware par route** : `role:admin`, `check.admin`, `protect.sensitive` appliqués via alias dans `routes/web.php`
- **Justificatifs** : stockés sur le disque `local` (privé), téléchargement via route protégée avec vérification admin/propriétaire
- **Notes** : opérations en transaction DB pour garantir l'atomicité
- **Absences** : validation que les `student_id` appartiennent au groupe du schedule
- **Classes virtuelles** : autorisation par rôle (étudiant du groupe OU professeur du module OU admin)

## Stack technique

- **Backend** : Laravel 12
- **Frontend** : Blade + Vite + JavaScript
- **Base de données** : SQLite (dev) / MySQL (prod)
- **Authentification** : Laravel built-in auth

## Licence

Projet académique — Examen final.
