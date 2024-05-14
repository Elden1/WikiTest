# Application de gestion de disponibilités d'un véhicule en location sous Symfony 

## Description

Il s'agit d'une application web permettant de créé un véhicule et de lui attacher une marque, un modèle ainsi qu'un prix, une date de début de location ainsi que de fin. 

Il permet aussi de filtré les entrées par rapport à leur dates de locations, mais aussi au prix maximum (si il est indiqué !).

## Language, framework
PHP/Symfony
Composer
Doctrine (ORM)

## Base de donnée

Construit sur PostgreSQL à la base, je n'ai pas trouvé comment transferer une base de donnée peuplée d'entités, donc il entrer quelques voitures manuellement. Il faudra au préalable télécharger et configurer PostgreSQL puis le mettre en route (vérifier dans Gestionnaire des taches =>  Services), vous pouvez mettre le mot de passe que vous souhaitez mais pensez à modifier le .env.

Pensez à modifier le .env avec vos identifiants (DATABASE_URL="postgresql://```utilisateur(par défault = postgres)```:```mot_de_passe```@localhost:5432/voiture?serverVersion=16&charset=utf8").

## Lancement
J'utilise la commande php -S localhost:8000 -t public donc le serveur local de PHP, j'ai tester avec symfony server:start. 

## Fonctionnement

le fichier Controller\HomeController.php va gérer dans les différentes routes, le rendement des pages ainsi que le traitement des différents formulaires, les templates permettent un rendu propre et contrôlé des informations tout en gardant la structure de base de templates\base.html.twig. 

Les fichiers Entity\Voiture.php et Repository\VoitureRepository.php vont gérer la création et l'inscription/modification/suppression des entrées dans la base de donnée, mais aussi la récupérations des voitures suivant les requirements inclus.

le css est géré sous public\css\styles.css.