# Transvision

Transvision is a Web application targetting the Mozilla localization community created and maintained by the French Mozilla community (http://www.mozfr.org).

The main purpose of Transvision is to be provide a specialized search engine to find localized strings in Mozilla code repositories for all Mozilla products (Firefox, Thunderbird, Firefox OS, Seamonkey…) and websites (currenty only www.mozilla.org is supported) via a Web interface. There are also side-features such as checks for common typographical errors for some languages, checks for the validity of localized access keys in the UI or comparison views between Mozilla repository channels (Nightly/Aurora/Beta/Release).

Transvision is written in PHP, the string extraction is done with the Silme library (Python) and server install/maintenance scripts are in Bash.

Transvision can be found here:
http://transvision.mozfr.org

Transvision Beta can be found here:
http://transvision-beta.mozfr.org

Transvision was created by Philippe Dessante, from the FrenchMozilla localization team.

Lead developer since version 1.0 : Pascal Chevrel (pascal AT mozilla DOT com).

## Getting Started

The Transvision team uses Git and GitHub for all of our development and issue tracking.
- If you'd like to contribute code back to us, please do so using a [Pull Request][].
- We generate automatic documentation of [Transvision classes][].
- If you want to chat with the team, you can find us on IRC in [![#transvision IRC channel](https://kiwiirc.com/buttons/irc.mozilla.org/transvision.png)](https://kiwiirc.com/client/irc.mozilla.org/?nick=github_vis|?#transvision) (#transvision channel on irc.mozilla.org server).
- If you want to file a bug [Create a new issue on github][] or contact the team.

## Dependencies

- Bash scripting support
- Python
- PHP >= 5.4
- Composer (Dependency Manager for PHP, http://getcomposer.org/)
- mercurial, git, svn to check out data sources
- php5-xsl and GraphViz packages for the generation of documentation with [phpDocumentor][]

## Install

1. Fork the [Transvision Project][] into your github account.
2. Clone your fork in your machine.
3. Copy app/config/config.ini-dist to app/config/config.ini and adapt the variables to your system.
4. Run first "setup.sh", then "glossaire.sh" in Transvision's root folder (this may take some time as it downloads the source code for all Mozilla products).
5. Install Composer (Dependency Manager for PHP, http://getcomposer.org/) and run "php composer.phar install" (or "composer install" if installed globally) inside the web folder.
6. You are set! You can run Transvision in your local machine with "php -S localhost:8080" inside the web folder and visit http://localhost:8080/ in your browser.

## Update glossary

- To update transvision glossary, run "glossaire.sh" in Transvision's root folder.

## Contribution tips

- Add transvision parent project as remote:
"git remote add transvision git@github.com:mozfr/transvision.git"
- Update your branch to the last version of transvision:
"git pull transvision master"
- Launch unit test:
"php vendor/atoum/atoum/bin/atoum -d tests/units/"
- Update dependencies with composer:
"php composer.phar update" (or "composer update" if installed globally)

## Licence:

MPL 2


[Pull Request]: https://help.github.com/articles/using-pull-requests
[Create a new issue on github]: https://github.com/mozfr/transvision/issues
[Transvision classes]: http://transvision-beta.mozfr.org/docs
[Transvision Project]: https://github.com/mozfr/transvision
[phpDocumentor]: http://phpdoc.org/

