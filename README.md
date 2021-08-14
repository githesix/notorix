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



<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/githesix/notorix">
    <img src="public/img/sigle.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Notorix</h3>

  <p align="center">
    User management tool for schools
    <br />
    <a href="https://github.com/githesix/notorix"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/githesix/notorix">View Demo</a>
    ·
    <a href="https://github.com/githesix/notorix/issues">Report Bug</a>
    ·
    <a href="https://github.com/githesix/notorix/issues">Request Feature</a>
  </p>
</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Table of Contents</h2></summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">What is Notorix?</a></li>
        <li><a href="#built-with">For who?</a></li>
        <li><a href="#built-with">How does it work?</a></li>
        <li><a href="#built-with">Extensions</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Acknowledgements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://example.com)

### What is Notorix?

Notorix is a user management tool for schools that:

* answers the questions "**who is who**" and "**in relation to whom**"
* informs users about the **legal framework** and general conditions
* **manages** registrations, modifications, deletions, and accesses of user accounts
* **validates** user's e-mail address
* strengthens the **security** of personal data and their operations
* guarantees user consent and **privacy** (RGPD)
* maintains data **sovereignty** within the school
* is **free** and **open source**

### For who?

* school administrators
* teachers
* students
* parents
* school software developers

### How does it work?

* web interface
* relational database
* interactions with multiple platforms via API (GraphQL, LDAP, OAuth)

### Extensions

Notorix is the cornerstone between all the systems with a user base. Extensions make them work together:

* Import population (students, parents, classes) from Siel
* Link with e-mail server and deliver corporate e-mail adresses
* Secure Wi-Fi with personal access (1 account per user)
* Login to multiple platforms with the same account
* ...



<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these simple steps.

### Prerequisites

There're 2 ways to install Notorix:

1. **docker**
2. **composer**

Docker embeds all the necessary components, but it must be reserved to test and development environments.

For production, it is advisable to use a proper web server providing:

* Nginx
* PHP >= 7.3
* Composer
* Nodejs + npm
* MariaDB/MySQL
* Redis

### Installation

#### Docker

1. Clone the repo
   ```sh
   git clone https://github.com/githesix/notorix.git
   ```
2. Edit .env

3. Start the container
   ```sh
   cd notorix
   docker-compose up
   ```

#### Composer

1. Install Notorix
	```sh
	composer require notorix/notorix
	```
2. Generate secure key
	```sh
	cd notorix
	php artisan key:generate
	``` 
3. Edit .env
4. Give the webserver the rights to read and write to storage and cache
    ```sh
     sudo chgrp -R www-data storage bootstrap/cache
     sudo chmod -R ug+rwx storage bootstrap/cache
    ```
5. Edit `/resources/markdown/policy.md` and `/resources/markdown/terms.md` to your liking


<!-- USAGE EXAMPLES -->
## Usage

This part is still being written.

_For more examples, please refer to the [Documentation](https://example.com)_



<!-- ROADMAP -->
## Roadmap

See the [open issues](https://github.com/githesix/notorix/issues) for a list of proposed features (and known issues).



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.



<!-- CONTACT -->
## Contact

Thesis ASBL (NPO) - project_notorix@thesis.nu

Project Link: [https://github.com/githesix/notorix](https://github.com/githesix/notorix)



<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements

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
