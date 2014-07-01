# Transvision

Transvision is a Web application targeting the Mozilla localization community, created and maintained by the French Mozilla community (http://www.mozfr.org).

The main purpose of Transvision is to provide a specialized search engine to find localized strings in Mozilla code repositories for all Mozilla products (Firefox, Thunderbird, Firefox OS, Seamonkey…) and websites (currenty only www.mozilla.org is supported) via a Web interface. There are also side-features such as checks for common typographical errors for some languages, validity checks for localized access keys in the UI, or comparison views between Mozilla repository channels (Nightly/Aurora/Beta/Release).

Transvision is written in PHP, the string extraction is done with the Silme library (Python) and server install/maintenance scripts are in Bash.

Transvision is available at:
http://transvision.mozfr.org

Transvision Beta is available at:
http://transvision-beta.mozfr.org

Transvision was created by Philippe Dessante, from the French Mozilla localization team.

Lead developer since version 1.0 : Pascal Chevrel (pascal AT mozilla DOT com).

## Getting Started

The Transvision team uses Git and GitHub for both development and issue tracking.
- If you'd like to contribute code back to us, you can do it using a [Pull Request][].
- We generate automatic documentation of [Transvision classes][].
- If you want to chat with the team, you can find us on IRC in [![#transvision IRC channel](https://kiwiirc.com/buttons/irc.mozilla.org/transvision.png)](https://kiwiirc.com/client/irc.mozilla.org/?nick=github_vis|?#transvision) (#transvision channel on irc.mozilla.org server).
- If you want to file a bug [Create a new issue on github][] or contact the team.

## Dependencies

- Bash scripting support
- Python
- PHP >= 5.4
- Composer (Dependency Manager for PHP, http://getcomposer.org/)
- mercurial, git, svn to check out data sources
- php5-xsl and GraphViz packages for generating the documentation with [phpDocumentor][]

## Full Installation (production or heavy development)

1. Fork the [Transvision Project][] into your GitHub account.
2. Clone your fork to your machine.
3. Copy app/config/config.ini-dist to app/config/config.ini and adapt the variables to your system.
4. Run first "app/scripts/setup.sh", then "app/scripts/glossaire.sh". This process will take some time as it downloads the source code for all Mozilla products (~20GB of data).
5. Install Composer (Dependency Manager for PHP, http://getcomposer.org/) and run "php composer.phar install" (or "composer install" if installed globally) inside the web folder.
6. You are set! You can run Transvision in your local machine with "php -S localhost:8080" inside the web/ folder and opening http://localhost:8080/ with your browser.

## Snapshot installation (regular development)

1. Fork the [Transvision Project][] into your GitHub account.
2. Clone your fork to your machine.
3. Copy app/config/config.ini-dev to app/config/config.ini and adapt the variables to your system.
4. Run "app/scripts/dev-setup.sh". This process may take some time as it downloads a snapshot of data from Transvision server (~400MB). It will also download Composer, the PHP dependency manager, and install the dependencies needed.
5. You are set! You can run Transvision in your local machine with "php -S localhost:8082 -t web/ app/inc/router.php" in your install folder and opening http://localhost:8082/ with your browser.

## Update glossary

- To update Transvision glossary, run "app/scripts/glossaire.sh" (only for full installations) .

## Contribution tips

- Add Transvision parent project as remote:
"git remote add transvision git@github.com:mozfr/transvision.git"
- Update your branch to the last version of Transvision:
"git pull transvision master"
- Launch unit tests:
"php vendor/atoum/atoum/bin/atoum -d tests/units/"
- Update dependencies with composer:
"php composer.phar update" (or "composer update" if installed globally)
- Check our [Coding Standards][] before submitting pull requests.

## Licence:

MPL 2


[Pull Request]: https://help.github.com/articles/using-pull-requests
[Create a new issue on github]: https://github.com/mozfr/transvision/issues
[Transvision classes]: http://transvision-beta.mozfr.org/docs
[Transvision Project]: https://github.com/mozfr/transvision
[phpDocumentor]: http://phpdoc.org/
[Coding Standards]:https://github.com/mozfr/transvision/wiki/Code-conventions
