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


Install
-------
1. Copy web/inc/config.ini-dist to web/inc/config.ini and adapt the variables to your sistem.
2. Run setup.sh in Transvision's root folder.
3. Install Composer (Dependency Manager for PHP, http://getcomposer.org/) and run "php composer.phar install" (or "composer install" if installed globally) inside the web folder.

Update
-------
1. Run glossaire.sh in Transvision's root folder.

Licence:
-------
MPL 2/GPL 2.0/LGPL 2.1
