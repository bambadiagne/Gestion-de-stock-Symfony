# Projet gestion de stock avec Symfony
## Autres outils: AJAX(pour le non rechargement des pages et la recuperation de données) ,ChartJS(Pour les graphiques)
Pour executer le projet suivez les etapes suivantes:

1)Installer les dependances avec la commmande  `composer install`
2) Creer la base de donnees avec la commande `php bin/console doctrine:database:create`
3)Puis faites les migrations  `php bin/console make:migration`
4)Executez les migrations  `php bin/console doctrine:migrations:migrate`
5)Enfin executer la commande `php bin/console server:run` pour demarrer le serveur(l'application)
**NB: Nous n'avons pas utilisé de fixtures ni la librarie Faker les donnees generees proviennent d'un generateur dont le site est https://www.mockaroo.com/** 
