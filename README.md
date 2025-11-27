# 🛒 Mini-Projet API Shop – Symfony

Ce dépôt est un petit projet de démonstration construit avec Symfony.  
Il expose une API REST sécurisée permettant de gérer :

- Des **utilisateurs** (signup / login via JWT)  
- Des **produits**  
- Un **panier d’achat (ShoppingBag)**  
- Une **documentation Swagger/OpenAPI** générée automatiquement  
- Une **suite de tests automatisés** (PHPUnit)

Ce projet sert d'exemple pour découvrir Symfony, son écosystème moderne,  
et la création d'API robustes et bien structurées.


---

## 🚀 Technologies utilisées

### 🔹 Backend
- Symfony 7
- PHP 8.2+
- Doctrine ORM
- Symfony Validator
- Symfony Serializer (Groupes)
- Security + JWT (lexik/jwt-authentication-bundle)

### 🔹 Authentification
- JWT Token
- Middlewares de sécurité

### 🔹 Base de données
- SQLite (simple et portable)
- Doctrine Migrations

### 🔹 Documentation API
- NelmioApiDocBundle  
- Annotations OpenAPI (`#[OA\...]`)

### 🔹 Tests
- PHPUnit
- WebTestCase
- Base de tests isolée avec fixtures automatiques


---

## 📦 Fonctionnalités

### 🧩 1. Authentification
| Méthode | Route | Description |
|--------|--------|-------------|
| POST | `/api/account` | Création d’un compte |
| POST | `/api/token` | Récupération d’un token JWT |

### 🛍️ 2. Produits
| Méthode | Route | Description |
|--------|--------|-------------|
| GET | `/api/products` | Liste des produits |
| GET | `/api/products/{id}` | Voir un produit |
| POST | `/api/products` | Créer un produit |
| PUT | `/api/products/{id}` | Mettre à jour un produit |
| DELETE | `/api/products/{id}` | Supprimer un produit |

Un **DTO ProductInput** est utilisé pour valider les données entrantes.

### 👜 3. Panier d'achat (ShoppingBag)
| Méthode | Route | Description |
|--------|--------|-------------|
| GET | `/api/bag` | Voir le panier |
| POST | `/api/bag/add/{productId}` | Ajouter un produit |
| DELETE | `/api/bag/{id}` | Supprimer un panier |
| DELETE | `/api/bag/product/{productId}` | Retirer un produit du bag |


---

## 📖 Documentation API

Accessible via :

```
http://localhost/api/doc
```

OU

```
http://127.0.0.1:8000/api/doc
```

Toutes les routes annotées avec `#[OA\...]` sont automatiquement affichées  
dans une interface Swagger moderne.


---

## 🏗️ Arborescence (simplifiée)

```
src/
│
├── Controller/
│ ├── Api/
│ │ ├── ProductController.php
│ │ ├── ShoppingBagController.php
│ └── AuthController.php
│
├── Dto/
│ └── ProductInput.php
│
├── Entity/
│ ├── Product.php
│ ├── ShoppingBag.php
│ └── User.php
│
├── Repository/
│ ├── ProductRepository.php
│ ├── ShoppingBagRepository.php
│ └── UserRepository.php
│
tests/
│
├── Helper/
│ ├── DatabaseTestCase.php
│ └── AuthTestTrait.php
│
└── Controller/
├── ProductControllerTest.php
├── ShoppingBagControllerTest.php
└── AuthControllerTest.php
```



---

## Pré - Requis

- PHP 8.5 installé avec les extensions suivantes :

```
extension=curl
extension=intl
extension=mbstring
extension=openssl
extension=pdo_sqlite
extension=sodium
```

 - Composer  
 - Symfony
 - Git  

## 🏁 Installation

### 1️⃣ Cloner le projet

```sh
git clone <repo>
cd projet
```

### 2️⃣ Installer les dépendances

```sh
composer install
```
### 3️⃣ Générer les clés JWT

```sh
php bin/console lexik:jwt:generate-keypair
```
*Vérifier la passphrase dans .env et lexik_jwt_authentication.yaml.*

### 4️⃣ Créer la base SQLite

```sh
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5️⃣ Lancer le serveur

```sh
symfony serve
```

### 🧪 Lancement des tests

```sh
php bin/phpunit
```
La base de tests SQLite est automatiquement réinitialisée avant chaque test.


## 🎯 Objectif du projet

Ce mini-projet démontre :     

- la construction d'une API REST complète  
- une architecture propre (DTO, sérialisation, validation)  
- la sécurité JWT  
- la documentation Swagger  
- des tests fonctionnels API  
- un système de panier simple et efficace  


