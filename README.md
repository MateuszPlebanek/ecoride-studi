# EcoRide - Studi 🚗🌱 Plateforme de covoiturage écologique

Projet de plateforme de covoiturage écologique développé en PHP (MVC).
Ce projet est réalisé dans le cadre du Titre Professionnel DWWM / ECF.


## 🚀 Installation & déploiement en local

### Prérequis
- PHP 8.1+
- MySQL 8+
- Git installé 

## 🧱 Stack technique

- **Langage :** PHP 8
- **Architecture :** MVC (sans framework)
- **Base de données relationnelle :** MySQL
- **Base NoSQL :** MongoDB (prévu pour les parties avis / logs)
- **Front :** HTML5, CSS3, un peu de JavaScript vanilla
- **Serveur de dev :** `php -S`

### Configuration de la base de données

Copier le fichier `.env.example` en `.env.local` :

```bash
cp .env.example .env.local

### 📌 Installation
Cloner ce dépôt :
```bash
git clone https://github.com/MateuszPlebanek/ecoride-studi.git

### ▶️ Lancer l’application en local

Après avoir cloné le dépôt :

```bash
cd ecoride-studi
Importer la base de données MySQL (fichier sql/schema.sql), puis démarrer le serveur PHP intégré :
php -S localhost:8001 -t public