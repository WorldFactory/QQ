# Use command line arguments

## Basic principe

Arguments are the items you can retrieve from the incoming command line, to send them to the saved script.

Let's take the following example :

```
commands:
    phpv: php -v
```

If you type `qq phpv -f` in your command line, then QQ will execute the following command: `php -v`.

The option you added in your command line has been completely ignored. It's normal !!

QQ has no way of knowing how to handle the extra options and arguments you added when you called a QQ command.
As a result, in principle, he does not deal with it.

It's up to you to tell QQ how to handle the extra arguments.

## Indexed arguments

In fact, QQ will take into account everything to the left of the name of your order.
The Symfony console offers several options common to all commands to control the verbosity of the script, the environment, etc ...
All these options must be placed to the left of the name of your order.

Everything to the right of the name of your order will be called 'argument' in the rest of this tutorial.

```bash
qq -v phpv --option arg1 arg2 --opt=text
```

* Argument #1 : `--option`
* Argument #2 : `arg1`
* Argument #3 : `arg2`
* Argument #4 : `--opt=text`

As you can see, the options and arguments are mixed.
It is the intermediate spaces that distinguish the different arguments.

Internally, we use the term 'token'. Perhaps you will find it more suitable.

To use these arguments inside a QQ command, just insert the code `% index%` inside the definition of your command.

```
commands:
    phpv: php -v %1%
```

And now, if you type `qq phpv -f` in your command line, then QQ will execute the following command: `php -v -f`.

Warning, indexed arguments are mandatory. If you do not provide them when you call the QQ command, it will fail.

## Special arguments

You also have two additional tools to integrate the incoming arguments into your QQ scripts : `%_all%` and `%_left%`.

### Argument %_all%

This key is replaced by all of the arguments to the right of the name of your command.

```
commands:
    phpv: php %_all%
```

```
qq phpv -r file.php
```

Will execute following code : `php -r file.php`

```
qq phpv -v
```

Will execute following code : `php -v`

Note that if there is no argument to the right, then the argument `%_all%` will still be removed from the final command executed by QQ.

```
qq phpv
```

Will execute following code : `php`

### Argument %_left%

This key is replaced by any arguments that have not been used elsewhere yet.

Note that if there is no argument that have not been used elsewhere yet, then the argument `%_left%` will still be removed from the final command executed by QQ.

```
commands:
    com: my-command -f %1% -r %_left%
```

```
qq com riri fifi loulou
```

Will execute following code : `my-command -f riri -r fifi loulou`

## Arguments in Runner options

You can also use the arguments used on the command line and inject them into the options of the Runner that will be used.

```
commands:
    lsl:
        script: ls -l
        options:
            workingDir: %1%
```

With this command, if you type this :

```
qq lsl /home/myname
```

Will execute `ls -l` in your home directory.
