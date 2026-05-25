# Rapport de revision du projet Laravel

Date de revision: 25/05/2026  
Projet: Gestion universitaire / examens / absences / reservations / classroom  
Stack observee: Laravel 13, PHP requis ^8.3, Breeze, Sanctum, Vite, Tailwind, Alpine, DomPDF, QR Code, Excel/PhpSpreadsheet.

## 1. Resume executif

Le projet est riche fonctionnellement et couvre plusieurs modules importants: authentification, roles admin/professeur/etudiant, gestion des utilisateurs, emplois du temps, notes, absences, demandes administratives, reservations de salles, messagerie, classroom, examens, convocations et exports PDF/CSV.

Le frontend compile correctement avec Vite. Par contre, la verification backend est bloquee par la configuration PHP locale: l'extension `mbstring` manque pour PHPUnit, et le driver PDO MySQL manque pour les commandes de migration. La revue statique montre aussi quelques risques critiques a corriger avant livraison: routes de maintenance publiques, migrations dupliquees, secrets dans `.env`, autorisations metier incompletes dans certains controllers, et fichiers uploades directement dans `public`.

Priorite globale: le projet est presentable comme prototype avance, mais il faut securiser et stabiliser avant une mise en production ou une soutenance technique exigeante.

## 2. Perimetre analyse

Elements parcourus:

- 87 fichiers dans `app`.
- 126 vues Blade dans `resources/views`.
- 46 migrations dans `database/migrations`.
- 193 routes Laravel listees par `artisan route:list`.
- Controllers principaux: admin, professor, student, API, exams, schedules, absences, grades, classroom, reservations, messages.
- Configuration: `.env`, `composer.json`, `package.json`, routes web/API, middleware role.

Commandes executees:

- `npm run build`: succes.
- `php artisan route:list`: succes avec PHP 8.5.4 de XAMPP.
- `php artisan test`: echec, extension PHP `mbstring` manquante.
- `php artisan migrate:status`: echec, driver PDO MySQL manquant.
- `git status`: impossible, le dossier n'est pas un repository Git.

## 3. Points forts

### Architecture fonctionnelle

Le projet est bien separe en domaines:

- `Admin`: utilisateurs, groupes, modules, salles, filieres, examens, reservations, messages, logs.
- `Professor`: notes, absences, disponibilites, reservations, textbook.
- `Student`: dashboard, notes, absences, demandes, convocations.
- `Shared`: classroom, chat, profil, notifications.
- `API`: login Sanctum, modules, notes, absences, schedules.

### Utilisation correcte de Laravel

Plusieurs bonnes pratiques sont deja presentes:

- Middleware de roles utilise sur les groupes admin/professor/student.
- Validation serveur presente dans la majorite des actions.
- Relations Eloquent utilisees avec `with(...)` dans plusieurs endroits.
- Notifications Laravel exploitees pour demandes, absences, reservations et classroom.
- Exports PDF avec DomPDF.
- Auth Breeze et Sanctum deja en place.
- Build frontend Vite fonctionnel.

### Fonctionnalites avancees

Le module examens est ambitieux:

- Generation automatique de planning d'examens.
- Verification des conflits de salle/professeur.
- Generation de convocations.
- QR/reference de convocation.
- Feuilles de presence.
- Envoi d'emails.

Ce sont de bonnes fonctionnalites pour un projet final.

## 4. Problemes critiques a corriger

### 4.1 Routes dangereuses publiques

Fichier: `routes/web.php`

Routes detectees:

- Ligne 363: `/run-migrations`
- Ligne 372: `/test-api-suite`
- Ligne 376: `/populate-filieres`

Risque:

- `/run-migrations` execute `Artisan::call('migrate')` depuis une requete HTTP publique.
- `/populate-filieres` modifie massivement les donnees: filieres, groupes, modules, annees, semestres.
- `/test-api-suite` expose une page de test en production si elle reste active.

Impact:

- Un visiteur non authentifie pourrait declencher des changements de base de donnees.
- Risque fort de corruption ou d'exposition d'informations.

Correction recommandee:

- Supprimer ces routes avant livraison.
- Ou les mettre derriere `auth`, `role:admin`, `APP_ENV=local`, et une commande artisan dediee.
- Ne jamais exposer une migration via GET public.

Priorite: critique.

### 4.2 Secrets et debug dans `.env`

Fichier: `.env`

Constats:

- `APP_DEBUG=true`.
- Configuration SMTP presente avec un mot de passe reel.
- `.env` est bien ignore dans `.gitignore`, mais le fichier existe localement avec un secret.

Risque:

- En production, `APP_DEBUG=true` peut afficher stack traces, chemins serveur, variables et details sensibles.
- Un secret email local peut fuiter si le dossier est zippe/envoye tel quel.

Correction recommandee:

- Mettre `APP_DEBUG=false` en production.
- Regenerer le mot de passe SMTP expose localement si ce projet a ete partage.
- Ne jamais inclure `.env` dans un rendu ou archive de soutenance.
- Fournir uniquement `.env.example`.

Priorite: critique.

### 4.3 Migrations dupliquees pour les memes tables

Fichiers concernes:

- `database/migrations/2026_05_17_003743_create_modules_table.php`
- `database/migrations/2026_05_24_021200_create_modules_table.php`
- `database/migrations/2026_05_17_003752_create_rooms_table.php`
- `database/migrations/2026_05_24_021000_create_rooms_table.php`

Constat:

- Deux migrations creent `modules`.
- Deux migrations creent `rooms`.
- Les schemas ne sont pas identiques: par exemple `modules` contient `coefficient` dans une migration, mais `duration_minutes` dans une autre.

Risque:

- `php artisan migrate:fresh` ou nouvelle installation peut echouer avec "table already exists".
- La structure DB peut varier selon l'historique de migration.
- Les models/controllers peuvent attendre des colonnes qui n'existent pas selon l'ordre d'installation.

Correction recommandee:

- Garder une seule migration de creation par table.
- Transformer les migrations plus recentes en `Schema::table(...)` pour ajouter les colonnes.
- Verifier que `modules` contient bien toutes les colonnes attendues: `code`, `name`, `coefficient`, `duration_minutes`, `filiere_id`, `semester_id`.
- Verifier que `rooms` contient bien `name`, `capacity`, `type`.

Priorite: critique.

### 4.4 Backend non verifiable localement

Commandes:

- `php artisan test`: echec car `mbstring` manque.
- `php artisan migrate:status`: echec car le driver PDO MySQL manque.

Risque:

- Impossible de valider automatiquement les tests.
- Impossible de confirmer l'etat des migrations avec la configuration actuelle.
- Toute correction backend reste moins fiable tant que l'environnement PHP n'est pas complet.

Correction recommandee:

- Activer `extension=mbstring` dans `php.ini`.
- Activer `extension=pdo_mysql` dans `php.ini`.
- Redemarrer le terminal/serveur.
- Relancer:
  - `php artisan migrate:status`
  - `php artisan test`

Priorite: critique.

## 5. Problemes de securite et autorisations

### 5.1 Autorisations metier incompletes pour absences

Fichier: `app/Http/Controllers/AbsenceController.php`

Constats:

- Ligne 23: `createForm($schedule_id)` charge n'importe quel schedule par ID.
- Ligne 29: `store(...)` accepte `schedule_id` depuis la requete.
- Il n'y a pas de verification explicite que le professeur authentifie enseigne vraiment ce schedule.

Risque:

- Un professeur pourrait acceder ou soumettre des absences pour un schedule d'un autre professeur si l'ID est connu.

Correction recommandee:

- Dans `createForm` et `store`, verifier:
  - `Auth::user()->professor->id === $schedule->professor_id`
- Ajouter une Policy ou Gate pour `Schedule`.

Priorite: haute.

### 5.2 Autorisations metier incompletes pour notes

Fichier: `app/Http/Controllers/GradeController.php`

Constats:

- Ligne 25: `editGroup($group_id, $module_id)` charge groupe/module sans verifier que le professeur les enseigne.
- Ligne 37: `store(...)` accepte des notes pour `module_id` et `student_id`.
- La validation verifie l'existence des IDs, mais pas le droit du professeur sur ce groupe/module.

Risque:

- Un professeur pourrait modifier les notes d'un module ou d'un groupe qu'il n'enseigne pas.

Correction recommandee:

- Verifier l'existence d'un `Schedule` avec:
  - `professor_id = Auth::user()->professor->id`
  - `group_id`
  - `module_id`
- Verifier que chaque `student_id` appartient bien au groupe concerne.

Priorite: haute.

### 5.3 Classroom: creation/commentaire pas assez verrouilles

Fichier: `app/Http/Controllers/ClassroomController.php`

Constats:

- Ligne 84: `showClassroom(...)` fait une verification d'acces.
- Ligne 114: `storePost(...)` ne reutilise pas explicitement cette verification avant creation.
- Ligne 155: `storeComment(...)` permet de commenter un post sans revalider que l'utilisateur appartient au classroom du post.

Risque:

- Un utilisateur authentifie pourrait poster/commenter via requete manuelle si les IDs sont connus.

Correction recommandee:

- Extraire une methode privee `authorizeClassroomAccess($groupId, $moduleId)`.
- L'appeler dans `showClassroom`, `storePost`, et `storeComment`.
- Pour `storePost`, restreindre la publication aux professeurs qui enseignent la classe et aux admins, si c'est la regle voulue.

Priorite: haute.

### 5.4 Upload des justificatifs directement dans `public`

Fichier: `app/Http/Controllers/AbsenceController.php`

Constats:

- Ligne 85: validation MIME limitee a `pdf,jpg,jpeg,png`.
- Ligne 93: upload avec `move(public_path('justifications'), $filename)`.

Risque:

- Fichiers justificatifs accessibles directement par URL publique.
- Donnees medicales/personnelles exposees si le chemin est devine.
- Controle d'acces difficile.

Correction recommandee:

- Stocker dans `storage/app/private/justifications`.
- Servir les fichiers via une route protegee par `auth` + role admin + proprietaire etudiant.
- Utiliser `Storage::putFileAs(...)`.

Priorite: haute.

## 6. Qualite du code et maintenabilite

### 6.1 `routes/web.php` est trop volumineux

Le fichier contient beaucoup de logique directe:

- Exports CSV inline.
- Endpoints API internes inline.
- Routes de maintenance.
- Logique de peuplement de donnees.

Probleme:

- Difficile a maintenir.
- Difficile a tester.
- Risque d'oublier des middlewares.

Recommandation:

- Deplacer les exports vers `Admin\ExportController` uniquement.
- Deplacer les endpoints JSON vers controllers dedies.
- Transformer `/populate-filieres` en Seeder ou commande Artisan.
- Supprimer les doublons de routes export deja presents plus bas.

Priorite: moyenne/haute.

### 6.2 Duplications dans les routes d'exports

Fichier: `routes/web.php`

Constat:

- Les exports `students`, `grades`, `absences` existent une premiere fois en closures.
- Ils existent aussi via `Admin\ExportController`.

Risque:

- Maintenance double.
- Incoherence possible entre deux implementations.

Correction recommandee:

- Garder seulement `Admin\ExportController`.

Priorite: moyenne.

### 6.3 Encodage casse dans plusieurs fichiers

Exemples observes:

- `GÃ©nie`, `FiliÃ¨re`, `RÃ©servation`, `Ã‰tudiant`, `âœ…`, `âŒ`.

Risque:

- Interface peu professionnelle.
- PDF/emails peuvent afficher des caracteres corrompus.
- Probleme visible pendant soutenance.

Correction recommandee:

- Convertir tous les fichiers source en UTF-8 sans double-encodage.
- Corriger les textes dans controllers, routes, views et seeders.
- Revalider PDF et emails apres correction.

Priorite: moyenne/haute.

### 6.4 Pas de repository Git detecte

Constat:

- `git status` retourne: not a git repository.

Risque:

- Pas d'historique.
- Pas de rollback simple.
- Difficulte a livrer proprement.

Correction recommandee:

- Initialiser Git:
  - `git init`
  - verifier `.gitignore`
  - premier commit propre sans `.env`, `vendor`, `node_modules`.

Priorite: moyenne.

## 7. Base de donnees et modelisation

### Points positifs

- Les entites principales sont presentes: `User`, `Role`, `Student`, `Professor`, `Group`, `Module`, `Room`, `Schedule`, `Grade`, `Absence`, `Reservation`, `Exam`, `Convocation`, `Conversation`, `Message`.
- Les relations Eloquent sont globalement exploitees.
- Les logs d'activite sont utilises dans beaucoup d'actions.

### Risques

- Les migrations dupliquees sont le risque numero un.
- Certaines migrations recentes semblent recreer des tables au lieu de les modifier.
- Les contraintes metier importantes doivent etre imposees aussi cote DB quand possible:
  - unicite note par `student_id + module_id`.
  - unicite convocation par `exam_id + student_id`.
  - index sur `date`, `room_id`, `professor_id`, `group_id`.

Recommandation:

- Ajouter des indexes sur les colonnes utilisees dans les recherches de conflits:
  - `schedules(room_id, date, start_time, end_time)`
  - `schedules(professor_id, date, start_time, end_time)`
  - `reservations(room_id, start_time, end_time, status)`
  - `exams(room_id, date, start_time)`
  - `exams(group_id, date, start_time)`

Priorite: moyenne.

## 8. Frontend et UX

### Verification

`npm run build` reussit:

- Vite build OK.
- CSS et JS generes dans `public/build`.

### Points positifs

- Nombreuses vues par role.
- Dashboard admin/professor/student.
- Pages CRUD completes.
- Calendar, classroom et messages enrichissent l'experience.

### Points a corriger

- Encodage casse visible dans les textes.
- Certaines pages peuvent devenir lourdes car beaucoup de `get()` sans pagination.
- Les listes admin devraient utiliser pagination/search sur:
  - utilisateurs
  - absences
  - logs
  - messages
  - schedules
  - requests

Priorite: moyenne.

## 9. API

Fichier: `routes/api.php` et `app/Http/Controllers/Api/AcademicApiController.php`

Points positifs:

- Authentification Sanctum presente.
- Donnees filtrees par role pour notes, absences et schedules.
- Tokens precedents supprimes au login pour eviter l'accumulation.

Points a ameliorer:

- Ajouter rate limiting strict sur `/api/login`.
- Ajouter pagination pour `modules` et schedules si la base grandit.
- Ajouter tests API pour:
  - login valide/invalide
  - etudiant ne voit que ses notes
  - professeur ne voit que son schedule
  - admin voit schedule global

Priorite: moyenne.

## 10. Tests recommandes

Tests prioritaires a ajouter apres correction de l'environnement PHP:

1. Test middleware role:
   - admin peut acceder `/admin/dashboard`
   - professeur refuse `/admin/dashboard`
   - etudiant refuse `/professor/grades`

2. Test securite absences:
   - un professeur ne peut pas ouvrir un schedule qui n'est pas a lui.
   - un professeur ne peut pas enregistrer une feuille d'absence pour un autre professeur.

3. Test securite notes:
   - un professeur ne peut pas modifier les notes d'un module/groupe qu'il n'enseigne pas.

4. Test classroom:
   - etudiant hors groupe refuse.
   - commentaire refuse si l'utilisateur n'a pas acces au classroom.

5. Test migrations:
   - `php artisan migrate:fresh --seed` doit passer sur une base vide.

6. Test upload:
   - justificatif accepte seulement formats attendus.
   - justificatif accessible uniquement par utilisateur autorise.

## 11. Plan de correction prioritaire

### Sprint 1: securite bloquante

- Supprimer/proteger `/run-migrations`, `/populate-filieres`, `/test-api-suite`.
- Mettre `APP_DEBUG=false` pour environnement non-local.
- Retirer/regenerer les secrets SMTP partages.
- Corriger autorisations dans `AbsenceController`, `GradeController`, `ClassroomController`.
- Deplacer les justificatifs hors de `public`.

### Sprint 2: stabilite DB

- Fusionner/corriger migrations dupliquees `modules` et `rooms`.
- Tester `migrate:fresh --seed` sur base vide.
- Ajouter indexes et contraintes d'unicite.

### Sprint 3: qualite et soutenance

- Corriger l'encodage UTF-8 des textes.
- Nettoyer `routes/web.php`.
- Supprimer les exports inline dupliques.
- Ajouter pagination dans les grandes listes.
- Ajouter tests Feature pour roles et workflows principaux.

### Sprint 4: finition

- Initialiser Git.
- Nettoyer fichiers generes non necessaires.
- Preparer `.env.example`.
- Ajouter une section README: installation, comptes demo, commandes de test, captures principales.

## 12. Verdict final

Le projet a une bonne base et beaucoup de fonctionnalites utiles pour un examen final. Il montre un effort reel sur les roles, les workflows universitaires, les examens et les documents PDF.

Avant livraison, les corrections les plus importantes sont la securite des routes publiques, la coherence des migrations, la configuration PHP/MySQL, et les controles d'autorisation metier. Une fois ces points corriges, le projet peut devenir solide et beaucoup plus defendable techniquement.
