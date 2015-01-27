# How to contribute

First of all thank you for taking the time to contribute to the Transvision project.

## Getting Started


1. Fork the [Transvision Project][] into your GitHub account.
2. Clone your fork to your machine.
3. Run "start.sh". This process may take some time as it downloads a snapshot of data from Transvision server (~400MB). It will also download Composer, the PHP dependency manager, and install the dependencies needed. A config file located in app/config/config.ini will be created automatically. Once this is done, PHP development server will be launched and you can visit http://localhost:8082/ with your browser.

Note that if you launch start.sh again after the installation, it will not download again all the data, composer and dependencies, it will only launch the development server.

## Code Conventions

Our minimum requirements for PHP is PHP 5.4 (5.4.4 to be precise)

Generally speaking we try to follow the [PSR standards](https://github.com/php-fig/fig-standards/tree/master/accepted).

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

## Contribution tips

* Add Transvision parent project as remote: "git remote add transvision git@github.com:mozfr/transvision.git"
* Update your branch to the last version of Transvision: "git pull transvision master"
* Launch unit tests: "php vendor/atoum/atoum/bin/atoum -d tests/units/"
* Update dependencies with composer: "php composer.phar update" (or "composer update" if installed globally)
* Check our Coding Standards before submitting pull requests.
