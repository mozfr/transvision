# How to contribute

First of all thank you for taking the time to contribute to the Transvision project.

## Code Conventions

Our minimum requirements for PHP is PHP 5.6 (5.6.19 to be precise as our production server is under a Debian Stable distro)

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

- Add Transvision parent project as a remote repository called 'upstream' (this is a one time operation):
```bash
git remote add upstream git@github.com:mozfr/transvision.git
```
- Update your master branch to the latest version of Transvision every time you want to do some dev work:
```bash
git checkout master
git pull upstream master
```
Then switch to a new branch where you will work on the patch you want to propose:
```bash
git checkout -b my_new_branch
```
- Launch PHP-cs-fixer, unit and functional tests:
```bash
start.sh -tests
```
- Update dependencies with composer:
```bash
php composer.phar update
```
or, if Composer is installed globally:
```bash
composer update
```
- Check our [Coding Standards](https://github.com/mozfr/transvision/wiki/Code-conventions) before submitting pull requests.
