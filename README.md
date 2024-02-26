# Tutti-frutti
#### Créateurs :
- LAHAIE Thibaut
- REBOUL Nathan
- VALLS Marion

## Mise en place d'une commande Symfony
Il est possible d'ajouter en base de données des musiques liées aux fruits en utilisant la commande suivante :
```bash
$ php bin/console php bin/console updateDatabase --fruit=Banana
```
ou bien 
```bash
$ symfony console updateDatabase --fruit=Banana
```
Le paramètre `--fruit` est facultatif et doit être égal à un des fruits suivants : `Banana`, `Blueberry`, `Strawberry`, `Watermelon`, `Peach`.

Il est aussi possible de l'utiliser sans paramètre pour ajouter toutes les musiques liées aux fruits actuellement en base de données :
```bash
$ php bin/console php bin/console updateDatabase
```
ou bien 
```bash
$ symfony console updateDatabase
```

## Mise en place de la base de données à partir des entités Doctrine
Nous avons utilisé les entités suivantes pour la base de données :
- Fruit
- Musique
- Playlist
- Utilisateur

## Système de connexion et d'inscription sécurisé
Nous avons mis en place un système de connexion et d'inscription sécurisé. Pour cela, nous avons utilisé les composants de sécurité de Symfony. Nous avons également utilisé les composants de validation de Symfony pour valider les données des formulaires.

## Page d'accueil
La page d'accueil est accessible à l'adresse suivante : `http://localhost:8000/`. 
Lorsque l'utilisateur est connecté, il voit en fonction du fruit sélectionné (pastèque par défaut) une musique aléatoire sur le thème du fruit.
Il est possible de changer de fruit en cliquant sur un des fruits affiché en bas de la page.

## Page de recherche
Sur la page de recherche, l'utilisateur connecté pourra voir l'entièreté des musiques enregistrées en base et filtrer ces dernières en fonction du titre, du groupe, d'un label ou du fruit associé.
Il est aussi possible de cliquer sur un bouton afin d'ajouter une musique à la playlist de l'utilisateur.

## Page de playlist
Sur la page de playlist, l'utilisateur connecté pourra voir l'entièreté des musiques qu'il a ajouté à sa playlist. Il pourra voir le détail d'une musique en accédant à une page spécifique ou bien la supprimer de sa playlist.



