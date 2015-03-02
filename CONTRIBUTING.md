# How to contribute

First of all thank you for taking the time to contribute to the Transvision project.

## Code Conventions

Our minimum requirements for PHP is PHP 5.4 (5.4.36 to be precise as our production server is under a Debian Stable distro)

Generally speaking we try to follow the [PSR standards](https://github.com/php-fig/fig-standards/tree/master/accepted) and also adopt a few coding rules from the Symfony framework.

A few key points to take into account:

* Indentation is 4 spaces, never use tabs;
* Remove spaces at end of lines or blank lines;
* Variable names use underscore notation $locale_code;
* Use meaningful variable names;
* All classes must be namespaced;
* Method and function names use camelCase: getLanguage();
* Add a single space after each comma delimiter;
* Add a single space around operators (==, &&, ...);
* Add a comma after each array item in a multi-line array, even after the last one;
* Add a blank line before return statements, unless the return is alone inside a statement-group (like an if statement);
* Use braces to indicate control structure body regardless of the number of statements it contains;

Please use [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) before submitting your code for review. This tool will reformat all of your code to follow our coding standards.

The rules we follow are formalized in a .php_cs file at the root of the project. in order to use our ruleset, you have to:

1. Be at the root of the project.
2. Type ```./vendor/bin/php-cs-fixer fix```.

php-cs-fixer will tell you if it changed some of the files in the repository, don't forget to 'git add' and commit them.


## Contribution tips

* Add Transvision parent project as remote: "git remote add transvision git@github.com:mozfr/transvision.git"
* Update your branch to the last version of Transvision: "git pull transvision master"
* Launch unit tests: "php vendor/atoum/atoum/bin/atoum -d tests/units/"
* Update dependencies with composer: "php composer.phar update" (or "composer update" if installed globally)
* Check our Coding Standards before submitting pull requests.
