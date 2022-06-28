# Interface de recherche des données citoyennes avec administration
Projet de réutilisation de données délibération issu du projet 'Data search engine de Gironde Numérique disponible ici : https://gitlab.adullact.net/gironde-numerique/data-search-engine

## Description
Cette application constitue un moteur de recherche de données publiques pour une collectivité. Elle s'accompagne d'une interface d'administration des documents constituant le jeu des données publiques associées.
Elle s'installe pour chaque collectivité permettant de cloisonner les fichiers des documents de celle-ci. Il est aussi possible d'installer une seule instance de cette interface pour plusieurs collectivités, auquel cas le numéro SIREN ne doit pas être renseigné dans le fichier de propriétés.

## Pré-requis
- Serveur Apache avec PHP > 7.2
- Extension Apache Solr
- Module Apache header et deflate
- Serveur Solr

## Lancement en local (Windows)
- Pour obtenir les dépendances du projet, lancer dans un terminal la commande (composer doit préalablement être installé) : `composer install`
- Générer l'autoload des classes du projet avec la commande : `composer dump-autoload -o`
- Paramétrer ensuite le fichier de propriétés *src/inc.config.php* pour le brancher sur un solr existant
- Ajouter le fichier [dll solr](https://pecl.php.net/package/solr/2.5.1/windows) dans votre repertoire C:\php-7.3\ext. Attention prendre le fichier dll avec la bonne version.
- Modifer votre C:\php-7.3\php.ini pour ajouter la ligne "extension=solr"
- Lancer le seveur de dev php avec la ligne suivante "php -S localhost:8000 -t C:\chemin\vers\opendata-marqueblanche"

## Installation
- Pour obtenir les dépendances du projet, lancer dans un terminal la commande (composer doit préalablement être installé) : `composer install`
- Générer l'autoload des classes du projet avec la commande : `composer dump-autoload -o`
- Configurer le fichier */resources/javascript/properties.js*
- Paramétrer ensuite le fichier de propriétés *src/inc.config.php*
- Déployer l'application sur un nom de domaine du type http://data.[nom_de_domaine]
- Pour une installation avec serveur LDAP, supprimer la classe *src/Controller/Action/AuthenticationActionWithoutLDAP.php*
- Pour une installation sans serveur LDAP, supprimer la classe *src/Controller/Action/AuthenticationAction.php* pour la remplacer par la classe *src/Controller/Action/AuthenticationActionWithoutLDAP.php* (renommer ce fichier en *AuthenticationAction.php*)





## Utilisation
- L'index permet de parcourir le répertoire */OpenData* du serveur en question.
- De rechercher des documents par le biais du moteur de recherche.

## Administration
- Les documents sont administrables (suppression uniquement) par le biais de l'interface d'administration accessible au lien http://data.[nom_de_domaine]/administration
- Ajout de documents (pouvant être composés de plusieurs fichiers) à partir de l'administration
