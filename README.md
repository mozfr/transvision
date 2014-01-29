Transvision
===========

Transvision is a Web application targetting the Mozilla localization community created and maintained by the French Mozilla community (http://www.mozfr.org).

The main purpose of Transvision is to be able to quickly find strings in Mozilla code repositories for Firefox, Thunderbird, Seamonkey, Firefox OS and Chatzilla via a Web interface. There are also side-features such as checks for common typography errors for some languages, checks for the validity of localized access keys in the UI or comparison views between Mozilla repository channels (Nightly/Aurora/Beta/Release).

Transvision is written in PHP, the string extraction is done with the Silme library (Python) and server install/maintenance scripts are in Bash.

Transvision can be found here:
http://transvision.mozfr.org

Transvision Beta can be found here:
http://transvision-beta.mozfr.org

Transvision was created by Philippe Dessante, from the FrenchMozilla team.

Lead developer since version 1.0 : Pascal Chevrel (pascalc AT mozfr DOT org).

## Getting Started

The Transvision team uses Git and GitHub for all of our development and issue tracking.
If you'd like to contribute code back to us, please do so using a [Pull Request][].
If you get stuck and need help, you can find our team on our irc channel [#transvision][] on irc.mozilla.org server.
If you want to file a bug [add a new issue on github][] or contact the team. 

[Pull Request]: https://help.github.com/articles/using-pull-requests
[#transvision]: irc://irc.mozilla.org/transvision
[add a new issue on github]: https://github.com/mozfr/transvision/issues

Dependencies
------------
- Bash scripting support
- Phyton
- Php >= 5.4
- Composer (Dependency Manager for PHP, http://getcomposer.org/)

Install
-------
1. Fork the [Transvision Project][] into your github acoount.
2. Clone your fork in your machine.
3. Copy web/inc/config.ini-dist to web/inc/config.ini and adapt the variables to your system.
4. Run first "setup.sh", then "glossaire.sh" in Transvision's root folder (this may take some time).
5. Install Composer (Dependency Manager for PHP, http://getcomposer.org/) and run "php composer.phar install" (or "composer install" if installed globally) inside the web folder.
6. You are set! You can run transvision in your local machine with "php -S localhost:8080 inc/router.php" inside the web folder and visit http://localhost:8080/ in your browser.

[Transvision Project]: https://github.com/mozfr/transvision

Update glossary
---------------
- To update transvision glossary, run "glossaire.sh" in Transvision's root folder.

Contribution tips
-----------------
- Add transvision parent project as remote: 
"git remote add transvision git@github.com:mozfr/transvision.git"
- Update your branch to the last version of transvision:
"git pull transvision master"
- Launch unit test:
"php vendor/atoum/atoum/bin/atoum -d tests/units/"
- Update dependencies with composer:
"php composer.phar update" (or "composer update" if installed globally)

Licence:
-------
MPL 2
