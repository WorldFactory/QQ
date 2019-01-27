# QQ
QQ is a small software to save shortcuts of the commands you use the most during your developments.

## Disclamer

Warning, QQ is not a library, QQ is a software in its own right. It can not be installed directly in your project, it must be installed at a lower level of your application.

Its purpose is to be the keystone of your development environment.

## Documentation

* Introduction (this file)
    * Installation
    * Basic use
    * Extend
* [How to create a script ?](./doc/scripts.md)
* [Use command line arguments](./doc/arguments.md)

## Installation

Simply add QQ with Composer.

```composer require worldfactory/qq``` 

On *Windows*, the shortcut `qq.bat` will be automatically used.

However, on *Linux*, you will need to add the command `alias qq=./qq.sh` in your `.bashrc` file (or other) to make it easier for you to use `qq`.

## Basic use

You will find at the root of your project a directory 'config' containing several files, including the file 'commands.yml' :

```
imports:
    - { resource: vendor/worldfactory/qq/resources/libs/docker.yml }
    - { resource: vendor/worldfactory/qq/resources/libs/composer.yml }
    - { resource: vendor/worldfactory/qq/resources/libs/symfony.yml }

commands:
    phpv:
        script: php -v
        shortDescription: Display PHP version.
```

`phpv` is a simple command to display your php cli version.

Test it by typing `qq phpv` in your console.

Type `qq list` to get all available commands.

## Extend

You can add as many commands as you want as a result of `phpv`.

Use `script` parameters to indicate the cli command to execute.

Add a description with the `shortDescription` parameter.

## Coming soon

* Extends documentation
* More information about development environment with QQ.
* Many other improvements.
  
 [Trello development tab.](https://trello.com/b/IQ62jazu) (in french)
 