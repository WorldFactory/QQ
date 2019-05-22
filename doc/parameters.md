# Use parameters

## Basic principle

You can save certain values in parameters so you can reuse them later in your orders.

```
parameters:
    param1: /home/my-name

commands:
    my-command:
        run: ls -la %param1%
```

In this example, the string %param1% will be replaced by the content of the parameter 'param1'.

So if you type `qq my-command` in your command line, then the` ls -la / home / my-name` command will be executed.

## Into Runner options

You can also use the parameters inside the Runners options.

```
parameters:
    param1: /home/my-name

commands:
    my-command:
        run: ls -la
        options:
            workingDir: %param1% 
```

This last command will work exactly like the previous one.
