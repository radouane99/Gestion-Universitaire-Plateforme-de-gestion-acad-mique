<div align="center">
  <h1>🎓 Gestion Universitaire (UPF)</h1>
  <p>Une plateforme moderne et complète de gestion académique construite avec Laravel.</p>
</div>

---

## 📖 Présentation du Projet

**Gestion Universitaire** est une application web performante conçue pour digitaliser et simplifier la gestion quotidienne d'un établissement d'enseignement supérieur (type Université ou École d'Ingénieurs). 

Elle connecte l'administration, le corps professoral et les étudiants via des espaces de travail unifiés et sécurisés. L'objectif est d'automatiser les processus lourds (notes, absences, emplois du temps) tout en offrant une expérience utilisateur fluide et moderne.

---

## ✨ Fonctionnalités Détaillées

### 👮 Espace Administration
- **Gestion des Utilisateurs :** Création, modification et import (CSV) d'étudiants, professeurs et membres du staff.
- **Ingénierie Pédagogique :** Configuration des filières, niveaux, groupes, et modules d'enseignement.
- **Logistique :** Gestion du parc des salles (Amphis, Salles de TP/TD) et validation des réservations.
- **Emplois du temps :** Interface de conception des plannings (affectation des profs, salles, et modules aux groupes).
- **Examens :** Planification des épreuves, assignation des salles et génération en masse des convocations PDF.
- **Guichet Étudiant :** Traitement des demandes administratives (Attestations de scolarité, relevés de notes) et validation des justificatifs d'absence.

### 👨‍🏫 Espace Professeur
- **Saisie des Notes :** Interface sécurisée pour la saisie des notes (CC1, CC2, Examen) avec calcul automatique de la moyenne.
- **Suivi des Absences :** Appel en classe et enregistrement direct des absences.
- **Classes Virtuelles :** Espaces collaboratifs par groupe pour publier des annonces, des supports de cours et échanger avec les étudiants.
- **Réservations :** Demandes de réservation de salles pour des séances de rattrapage ou des examens.
- **Tableau de Bord :** Statistiques de réussite, taux d'assiduité, et top étudiants.

### 👨‍🎓 Espace Étudiant
- **Suivi Pédagogique :** Consultation en temps réel des notes et des moyennes par module.
- **Emploi du temps :** Planning hebdomadaire personnalisé avec informations sur les salles et les enseignants.
- **Assiduité :** Historique des absences et module de téléversement des justificatifs médicaux.
- **Classes Virtuelles :** Accès aux supports de cours partagés par les professeurs et espace de discussion (commentaires).
- **Démarches :** Demandes de documents administratifs en un clic.

---

## 🏗️ Architecture du Projet

Le projet repose sur l'architecture robuste de **Laravel 12** en suivant scrupuleusement le design pattern **MVC (Modèle-Vue-Contrôleur)**.

### Stack Technique
- **Backend :** PHP 8.2+, Laravel 12
- **Frontend :** Moteur de templates Blade, Tailwind CSS (Design System), Alpine.js (Interactivité)
- **Base de données :** SQLite (pour le développement local) / Compatible MySQL & PostgreSQL (Production)
- **Outils :** Vite (Compilation des assets), FullCalendar (Plannings)

### Structure de la Base de Données (Relations clés)
- `Users` -> `Roles` (1:N)
- `Students` -> `Groups` (N:1), `Users` (1:1)
- `Professors` -> `Users` (1:1)
- `Modules`, `Groups` -> `Filieres` (N:1)
- `Schedules` -> `Groups`, `Modules`, `Rooms`, `Professors` (Table Pivot)
- `Grades` -> `Students`, `Modules` (Table de jointure complexe)

### Sécurité & Flux
- **Authentification :** Gestion des sessions via Laravel Breeze/Auth.
- **Autorisation (Middlewares) :**
  - Accès protégé par des alias de routes (`role:admin`, `role:professor`, `role:student`).
  - Politiques d'accès strictes au niveau des contrôleurs (ex: un professeur ne modifie que les notes de ses propres modules/groupes).
- **Protection des données :** Les justificatifs et documents sensibles sont stockés dans `storage/app/private` et servis via des routes contrôlées (aucun accès public direct).
- **Transactions :** Utilisation intensive de `DB::transaction()` lors des opérations groupées (ex: saisie de notes) pour garantir l'intégrité de la base.

### API REST
Une API JSON est disponible pour une potentielle application mobile :
- Génération et validation de jetons via Laravel Sanctum.
- Endpoints : Authentification, consultation de l'emploi du temps (`/api/schedule`), des notes (`/api/grades`) et des absences (`/api/absences`).

---

## 🚀 Installation & Déploiement

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- Node.js (v18+) & NPM

### Instructions pas-à-pas

1. **Cloner le projet**
   ```bash
   git clone https://github.com/radouane99/Gestion-Universitaire-Plateforme-de-gestion-acad-mique.git
   cd Gestion-Universitaire-Plateforme-de-gestion-acad-mique
   ```

2. **Installer les dépendances PHP et JS**
   ```bash
   composer install
   npm install
   ```

3. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *(Si vous utilisez MySQL, modifiez les accès `DB_*` dans le fichier `.env`)*.

4. **Initialisation de la base de données**
   ```bash
   # Création du fichier SQLite (si environnement de dev par défaut)
   touch database/database.sqlite
   
   # Lancement des migrations avec insertion des données de démonstration
   php artisan migrate --seed
   ```

5. **Compilation des assets**
   ```bash
   npm run build
   ```

6. **Démarrer le serveur local**
   ```bash
   php artisan serve
   ```
   *Accédez à l'application via `http://localhost:8000`*.

---

## 🔑 Rôles et Comptes de Démonstration

L'application est pré-configurée avec des données réalistes grâce aux seeders. Voici les comptes par défaut pour tester les différents rôles (le mot de passe est identique pour tous) :

| Rôle | Adresse Email | Mot de passe |
|---|---|---|
| **Administrateur** | `admin@university.com` | `password` |
| **Professeur** | `prof@university.com` | `password` |
| **Étudiant** | `student@university.com` | `password` |

*(Note : D'autres comptes sont générés aléatoirement, vérifiez le fichier `DatabaseSeeder.php` pour plus de détails).*

---

## 📄 Licence
Ce projet a été développé dans le cadre d'un examen final académique. Usage pédagogique privilégié.
