# How to create a script ?

The most important file for managing your scripts is the 'commands.yml' file located in the 'config' directory.

This file include three sections : `imports`, `parameters` and `commands`.

```
imports:
    - { resource: path/to/another/library.yml }

parameters:
    param_1: First value

commands:
    phpv:
        script: php -v
        shortDescription: Show the PHP version.
```

All your commands should be placed in the 'commands' section.

We will see later the sections 'imports' and 'parameters'.

## The 'commands' section.

```
phpv:
    script: php -v
    shortDescription: Show the PHP version.
```

In this example, `phpv` is the name of the command.

`script` is the code that will be executed in a console.

`shortDescription` is... a short description of the command.
This text will be displayed each time you use the command.

### Minimalist shape

Only the name and the `script` parameter are required for a command to be used.

So, a ultra-minimaliste config mode is available.

```
phpv: php -v
```
Only the name and the script !!

This ultra-minimaliste config mode is also available with multi-line scripts.

Nevertheless, keep in mind that adding documentation is never a waste of time.

### All options

* script : [*Required*] This is the command, as you would type it in a command line.

* type : [*Optional*] Set the type of Runner that will be used to execute the command. By default, the Runner 'bash' will be used. (We'll see what are the 'runners' later)

* shortDescription : [*Optional*] A brief description of the command. This description will be displayed each time the command is used.

* longDescription : [*Optional*] A long description of the command. This description will be displayed only if you ask for help on this command. (Type `qq help the-name-of-the-command`)

* aliases : [*Optional*] An array of aliases that can be used to call this command, replacing its name.

## Multi-line script

A script can be composed of several lines.

```
commands:
    myscript:
        script:
            - ls 
            - sleep 5
            - ls
        shortDescription: A multi-line command.
```

This command list all files of the current directory, pauses for 5 seconds, then displays the list of files in the current directory again.

## Namespacing

The script name is normaly a single string, but in fact, we can use `namespaces` to avoid name collisions.

```
commands:
    "php:version":
        script: php -v
        shortDescription: Show the PHP version.
        aliases: [phpv]
```

In this example, we renamed the 'phpv' command to 'php: version', creating the 'php' namespace.

We have also defined an alias named 'phpv' to still have a short name to call the command.

You can therefore call this command in several ways:
* `qq php:version`
* `qq phpv`
* `qq p:v`

This third form uses the internal match engine of the Symfony console. This does not work every time, but in case of ambiguity, the console will show you the list of corresponding commands.

### Tips

* Try to group your commands into themes with a namespace for each one.

* Set short aliases for the commands you use the most.

* Do not use aliases for commands that are only used from other commands.
