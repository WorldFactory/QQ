# Recursive script configuration form

Until now, you have seen the basic form of script configuration.

It's time to introduce the recursive form.

## Introduction

So far, you have already encountered 4 forms of script configuration.

```yaml
commands:
    # Minimalist form
    "my:minimalist:script": php://echo 1 + 2;

    # Basic form
    "my:normal:script":
        type: php
        run: echo 1 + 2;

    # Aggregated form
    "my:composed:script":
        type: php
        run:
            - $a = 1; echo $a;
            - $a = 2; echo $a;
            - $a = 3; echo $a;

    # Multilines form
    "my:multilines:script":
        type: php
        run: |
            $a = 1;
            $b = 2;
            echo $a + $b;
```

There is a 5th, which will allow you to create complex trees composed of dozens and dozens of scripts. Finally if you wish.

```yaml
commands:
    # Recursive form
    "my:recursive:script":
        type: php
        run:
            type: qq
            run: phpv

    # Another form of recursive mode
    "my:clone:script":
        type: php
        run: { type: qq, run: phpv }
```

This command will simply execute the QQ command named 'phpv'.

Specifying the 'qq' type will overload the first type that was specified at the root, namely the 'php' type.

In fact, the 'script' parameter can receive an associative array, containing all the elements to define a script.

Presented like that, it does not seem very interesting. But coupled with the fact that the 'script' parameter also accepts the tables, you will quickly understand that nothing is impossible. ;)

Here is a complex example :

```yaml
commands:
    "my:script": # by default, this command will use the 'shell' runner.
        run:
            - cd .. && ls -la # the ls command will be executed in the parent of the current directory.
            - php://echo "Hello world !!";
            - { type: qq, run: phpv }
            -
                - cd ~
                - ls -la # the ls command will be executed in the current directory, not in your home directory.
                - |
                    php://for($c = 1; $c <= 10; $c ++) {
                        echo $c . PHP_EOL;
                    }
                - cd ~ && ls -la # the ls command will be executed in your home directory.
            - sleep 3 # Waiting for 3 seconds
            - ls -la # the ls command will be executed in the current directory.
            - { run: ls -la, options: { workingDir: "~" } } # the ls command will be executed in your home directory.
```

We've mixed shell commands, php commands and even a QQ command in the same script !!

You will also have noticed that we can configure in detail the runners of each command via the 'options' parameter.

It's up to you to check the options available for each runner.

Obviously, the sub-tables should not seem very useful at the moment. And in the previous example, effectively, they had no interest. But we will see later that they take all their interest with the conditional instructions ...

## Inheritance

The 'type' or 'options' settings are passed on to their children by inheritance.

So you can off and already use the tables to factorize the configuration they have in common.

```yaml
commands:
    "my:script":
        type: qq
        run:
            - phpv
            -
                type: shell
                run:
                    - sleep 3
                    - ls -la
                options:
                    workingDir: "~" # NOTE : Tilde is in quotation marks because in Yaml, it represents the null value.
```

In this example, we have chosen the QQ runner.

Normally, this runner should be used for all the commands that follow.

But from the second line, we redefine the runner to 'shell', and it's the latter that will be used for the 'sleep' and 'ls -la' commands.

Also note that the 'workingDir' option will apply for both shell commands.
