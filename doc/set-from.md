# Variables manipulation

The statement set / from retrieves the result of a command and stores it inside a parameter.

## Basic usage

```
commands:
    run:
        - { set: var, from: php://return "Hello World !!"; }
        - php://var_dump($var);
```

In this command, the output of the PHP command `return "Hello World !!";` will be stored in the `var` parameter before being displayed by the PHPRunner via the var_dump instruction.


## Management of the results of the Runners

All Runners can return a result. But not necessarily in their initial state.

Sometimes you have to configure them first.

The ShellRunner, for example, allocates a TTY to execute the script given to it. Unfortunately, this TTY does not recover the result of the script.

So, in order to retrieve the result of a command executed with the ShellRunner, you will need to disable the TTY option as follows :

```
commands:
    run:
        - { set: var, from: ls -la, options: { tty: false } }
        - php://var_dump($var);
```

Some runners have a particular function as to the result they provide.

The BoolRunner, for example, executes a shell command and returns a Boolean if the command has successfully resolved, that is, if it returns an error code to zero.

```
commands:
    run:
        - { set: var, from: bool://ls /nonexistent/directory }
        - php://var_dump($var);
```

In this example, executing the `ls /nonexistent/directory` command will not generate a blocking error, but will simply return the value `false`, which will then be stored in the `var` parameter.