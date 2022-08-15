<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/githesix/notorix/blob/master/README.md)
[![fr](https://img.shields.io/badge/lang-fr-blue.svg)](https://github.com/githesix/notorix/blob/master/README.fr.md)

<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/githesix/notorix">
    <img src="public/img/sigle.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Notorix</h3>

  <p align="center">
    Gestionnaire d'utilisateurs pour les écoles
    <br />
    <a href="https://github.com/githesix/notorix"><strong>Documentation »</strong></a>
    <br />
    <br />
    <a href="https://github.com/githesix/notorix">Démo</a>
    ·
    <a href="https://github.com/githesix/notorix/issues">Signaler un bug</a>
    ·
    <a href="https://github.com/githesix/notorix/issues">Suggérer une amélioration</a>
  </p>
</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Table des matières</h2></summary>
  <ol>
    <li>
      <a href="#a-propos-de-ce-projet">À propos de ce projet</a>
      <ul>
        <li><a href="#notorix-c-est-quoi">Notorix, c'est QUOI?</a></li>
        <li><a href="#pour-qui">Pour QUI?</a></li>
        <li><a href="#comment-ca-fonctionne">COMMENT ça fonctionne?</a></li>
        <li><a href="#extensions">Extensions</a></li>
      </ul>
    </li>
    <li>
      <a href="#demarrage-rapide">Démarrage rapide</a>
      <ul>
        <li><a href="#prerequis">Prérequis</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#utilisation">Utilisation</a></li>
    <li><a href="#feuille-de-route">Feuille de route</a></li>
    <li><a href="#contributions">Contributions</a></li>
    <li><a href="#licence">Licence</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#cas-d-ecoles">Cas d'écoles</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## À propos de ce projet

[![Product Name Screen Shot][product-screenshot]](https://example.com)

### Notorix, c'est QUOI?

Notorix est un gestionnaire d'utilisateurs pour les écoles qui:

* répond aux questions «**qui est qui**» et «**par rapport à qui**»
* renseigne ses utilisateurs à propos du **cadre légal** et des conditions générales
* **gère** les enregistrements, les suppressions, les modifications, et les accès aux comptes utilisateurs
* **vérifie** les adresses e-mails inscrites
* renforce la **sécurité** des données personnelles et de leur utilisation
* assure le consentement des utilisateurs et le **respect de leur vie privée** (RGPD)
* garantit la **souveraineté des données** au sein de l'école
* est **gratuit** et **open source**

### Pour QUI?

* personnel administratif scolaire
* enseignants
* élèves
* parents
* développeurs de logiciels de gestion de vie scolaire

### COMMENT ça fonctionne?

* application web
* base de données relationnelle
* interconnexions possibles avec un maximum de plateformes via API (GraphQL, LDAP, OAuth)

### Extensions

Notorix est la pierre angulaire entre la base de données d'utilisateurs et les systèmes informatiques, au travers de ses extensions:

* Importation des utilisateurs à partir des fichiers de population officiels (élèves, parents, classes)
* Liaison avec un serveur e-mail et fourniture d'adresses e-mail professionnelles (xyz@notre-ecole.edu)
* Sécurisation du Wi-Fi avec des accès personnels (un compte par personne)
* Un seul compte pour toutes les plateformes (me connecter avec mon compte Notorix)
* ...



<!-- GETTING STARTED -->
## Démarrage rapide

Pour télécharger et installer Notorix, suivez ces quelques étapes.

### Prérequis

Il y a deux manières d'installer Notorix:

1. **docker**
2. **composer**

Le container Docker rassemble tous les composants prêts à l'emploi, mais doit être limité aux tests et aux environnements de développement.

Pour un usage en production, il est préférable de disposer d'un serveur dédié, équipé de:

* Nginx
* PHP >= 7.3
* Composer
* Nodejs + npm
* MariaDB/MySQL
* Redis

### Installation

#### Docker

1. Clonez le dépôt
   ```sh
   git clone https://github.com/githesix/notorix.git
   ```
2. Éditez .env

3. Démarrez le container
   ```sh
   cd notorix
   docker-compose up
   ```

#### Composer

1. Installez Notorix
	```sh
	composer create-project notorix/notorix
	```
2. Fabriquez une clé de sécurité unique
	```sh
	cd notorix
	php artisan key:generate
	``` 
3. Introduisez les paramètres de configuration dans le fichier .env
4. Accordez au serveur web les droits nécessaires sur les répertoires de stockage et de cache
    ```sh
     sudo chgrp -R www-data storage bootstrap/cache
     sudo chmod -R ug+rwx storage bootstrap/cache
    ```
5. Modifiez `/resources/markdown/policy.md` et `/resources/markdown/terms.md` selon vos préférences
6. Créez un premier utilisateur avec les droits d'administrateur: `php artisan create:adminuser`


<!-- USAGE EXAMPLES -->
## Usage

This part is still being written.

_For more examples, please refer to the [Documentation](https://example.com)_



<!-- ROADMAP -->
## Feuille de route

Voyez [open issues](https://github.com/githesix/notorix/issues) pour une liste de suggestions, de corrections et d'améliorations.



<!-- CONTRIBUTING -->
## Contributions

Les contributions sont la matière première qui rend la communauté open source si enrichissante, si inspirante et créatrice. Toutes vos contributions sont **les bienvenues!**

1. «Forkez» le projet
2. Créez votre fonctionnalité dans une nouvelle branche (`git checkout -b feature/AmazingFeature`)
3. Validez votre code (`git commit -m 'Add some AmazingFeature'`)
4. Soumettez-le dans la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez un «Pull Request»



<!-- LICENSE -->
## Licence

Distribué sous la licence MIT. Cf. `LICENSE` pour plus d'informations.



<!-- CONTACT -->
## Contact

Thesis ASBL (NPO) - project_notorix@thesis.nu

Project Link: [https://github.com/githesix/notorix](https://github.com/githesix/notorix)



<!-- ACKNOWLEDGEMENTS -->
## Cas d'écoles

* [Athénée royal de Chênée (École secondaire de la Fédération Wallonie Bruxelles - Liège - Belgique)](https://archenee.be)





<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/githesix/repo.svg?style=for-the-badge
[contributors-url]: https://github.com/githesix/repo/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/githesix/repo.svg?style=for-the-badge
[forks-url]: https://github.com/githesix/repo/network/members
[stars-shield]: https://img.shields.io/github/stars/githesix/repo.svg?style=for-the-badge
[stars-url]: https://github.com/githesix/repo/stargazers
[issues-shield]: https://img.shields.io/github/issues/githesix/repo.svg?style=for-the-badge
[issues-url]: https://github.com/githesix/repo/issues
[license-shield]: https://img.shields.io/github/license/githesix/repo.svg?style=for-the-badge
[license-url]: https://github.com/githesix/repo/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/githesix
