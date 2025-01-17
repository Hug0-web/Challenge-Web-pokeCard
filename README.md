# Noms des partcicipants :

•	Staël Elangmane
•	Hugo Lucas
•	Etienne Rousseau
•	Enzo Laborde

# Les pré-requis pour lancer le projet :
Avant de commencer, assurez-vous que votre environnement respecte les prérequis suivants :
	•	PHP version 8.1 ou supérieure.
	•	Symfony 6.0 ou supérieur.

# Pour l'Installation :

1. Clonez le dépôt du projet à l’aide de Git :
```
git clone https://github.com/your-username/Challenge-Web.git
```
2. Entrez dans le répertoire du projet :
```
cd Challenge-Web
```
3. Installez les dépendances en utilisant Composer :
```
composer install
```
4. Configurez votre fichier .env :
```
DATABASE_URL="mysql://username:password@127.0.0.1:5432/db_name?serverVersion=16&charset=utf8"
```
5. Créez la base de données et les tables en utilisant la commande Doctrine :
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
6. Importez les cartes depuis l’API Pokémon en utilisant la commande Symfony :
```
curl -X POST http://localhost:8000/api/pokemon/import
```
7. Démarrez le serveur Symfony :
```
symfony serve
```
