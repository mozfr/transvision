# Transvision
[![Build Status](https://travis-ci.org/mozfr/transvision.svg?branch=master)](https://travis-ci.org/mozfr/transvision/)

Transvision is a Web application targeting the Mozilla localization community, created and maintained by the French Mozilla community (https://www.mozfr.org).

The main purpose of Transvision is to provide a specialized search engine to find localized strings in Mozilla code repositories for all Mozilla products (Firefox, Firefox for Android, Firefox for iOS, Android projects, Thunderbird, SeaMonkey) and websites (currently only www.mozilla.org is supported) via a Web interface. There are also side-features such as checks for common typographical errors for some languages or validity checks for localized access keys in the UI).

Transvision is written in PHP, the string extraction is done with the compare-locales library (Python) and server install/maintenance scripts are in Bash.

Transvision is available at:
https://transvision.mozfr.org

Transvision Beta is available at:
https://transvision-beta.mozfr.org

Transvision was created by Philippe Dessante, from the French Mozilla localization team.

## Getting Started

The Transvision team uses Git and GitHub for both development and issue tracking.
- If you'd like to contribute code back to us, you can do it using a [Pull Request][].
- We generate automatic documentation of [Transvision classes][].
- If you want to file a bug [Create a new issue on GitHub][] or contact the team.

## Dependencies

* Bash scripting support
* Python 2.7, or 3.5 and later
 * [pip](https://pip.pypa.io/en/stable/installing/) and [virtualenv](https://virtualenv.pypa.io/en/stable/installation/)
* PHP >= 5.6 with the following extensions:
 * php5-intl for the transliteration feature
 * php-mbstring for multibyte string functions
* Composer (Dependency Manager for PHP, https://getcomposer.org/)
* Mercurial, Git to check out data sources (only for a Full installation for production)
* Apache with `mod_rewrite` activated
* [npm][] and eslint for JavaScript files (optional)
```
npm install -g eslint
eslint web/js
```
A PHP script is available in `app/scripts/check_requirements` to check the most important aspects of the local configuration.

## Full Installation (production or heavy development)

1. Fork the [Transvision Project][] into your GitHub account.
2. Clone your fork to your machine.
3. Copy `app/config/config.ini-dist` to `app/config/config.ini` and adapt the variables to your system.
4. Run first `app/scripts/setup.sh`, then `app/scripts/glossaire.sh`. This process will take some time as it downloads several repositories (~2GB of data).
5. Install Composer (Dependency Manager for PHP, https://getcomposer.org/) and run `php composer.phar install` (or "composer install" if installed globally) inside the web folder.
6. You can run Transvision in your local machine either with the ```start.sh``` script or with ```php -S localhost:8082 -t web/ app/inc/router.php``` and opening http://localhost:8082/ with your browser. To bound PHP internal web server to 0.0.0.0 use ```start.sh -remote```: the server will be accessible from other devices on the LAN, or from the host machine in case Transvision is running inside a virtual machine.

## Snapshot installation (regular development)

1. Fork the [Transvision Project][] into your GitHub account.
2. Clone your fork to your machine.
3. Run `./start.sh`. This process may take some time as it downloads a snapshot of data from Transvision server (~400MB). It will also download Composer, the PHP dependency manager, and install the dependencies needed. A config file located in `app/config/config.ini` will be created automatically. Once this is done, PHP development server will be launched and you can visit http://localhost:8082/ with your browser.

Note that if you launch start.sh again after the installation, it will not download again all the data, composer and dependencies, it will only launch the development server.

## Update glossary

- To update Transvision glossary, run `app/scripts/glossaire.sh` (only for full installations) .

## Contribution tips

See [``CONTRIBUTING.md``](CONTRIBUTING.md)

## Licence:

MPL 2

[Pull Request]: https://help.github.com/articles/using-pull-requests
[Create a new issue on github]: https://github.com/mozfr/transvision/issues
[Transvision Project]: https://github.com/mozfr/transvision
[npm]: https://www.npmjs.com
[Coding Standards]:https://github.com/mozfr/transvision/wiki/Code-conventions
