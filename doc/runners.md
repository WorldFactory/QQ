# How to work with Runners

Runners are the programs used to execute the scripts contained in your commands.

The default Runner is the ShellRunner.
It allows you to execute a command as you type it in a command line.

But there are many other Runners.
Some allow to execute PHP code, or to execute a command directly in a Docker container.

## Basic usage

Take the following command example :

```yaml
commands:
    "my:script":
        script: ls -l
```

By default, this command will use the ShellRunner to execute the script it contains.

The following example is exactly the same, except that the type of Runner to use is explicitly specified.

```yaml
commands:
    "my:script":
        type: shell
        script: ls -l
```

The PHP Runner allows executing php code.

```yaml
commands:
    "my:script":
        type: php
        script: $a = 1 + 2; echo $a;
```

This example will display the value 3.

## List of Runners available.

To get list of available Runners, use the QQ commands : `qq runner:list`

```
> qq runner:list
List of currently activated QQ Runners :
+--------+----------+------------------------------------------------------+
| Name   | Aliases  | Description                                          |
+--------+----------+------------------------------------------------------+
| shell  | sh, bash | Run script in CLI.                                   |
| qq     | command  | Run QQ sub-command.                                  |
| exec   |          | Run script in CLI with 'passthru' PHP function.      |
| php    |          | Execute PHP code with 'eval' function.               |
| inc    |          | Include target PHP file.                             |
| file   | child    | Save script in file and run it.                      |
| docker |          | Run script in target Docker container.               |
| view   |          | Show script in a frame. Useful to display a message. |
| null   |          | Do nothing. Displays only the script.                |
+--------+----------+------------------------------------------------------+
```

## Runner options

Some Runners offer the use of options to control how they will execute your script.

Sometimes, some of these options are even required.

To get all the help available on a particular Runner, use the following command : `qq runner:help docker`

```
> qq runner:help docker
                                           
  Help available on the 'docker' Runner :  
                                           
+-------------+-----------------------------------------------+
| Name        | docker                                        |
| Aliases     |                                               |
| Description | Run script in target Docker container.        |
| Class       | WorldFactory\QQ\Services\Runners\DockerRunner |
| Service     | qq.runner.docker                              |
+-------------+-----------------------------------------------+
Long desription :
The specified script is executed directly in the targeted container.
You can specify a particular user or internal directory.
You can also inject environment variables when running the script.
+------------+--------+----------+--------------------+-----------------------------------------------------+
| Option     | Type   | Required | Default            | Description                                         |
+------------+--------+----------+--------------------+-----------------------------------------------------+
| target     | string | yes      |                    | The targeted container.                             |
| user       | string | no       |                    | The user with whom the script is to be executed.    |
| env        | string | no       |                    | Environment variables that must be injected.        |
| workingDir | string | no       |                    | The internal working directory that should be used. |
| flags      | array  | no       | A non-scalar value | The flags to activate when running the script.      |
+------------+--------+----------+--------------------+-----------------------------------------------------+
```

The ShellRunner has an option to specify the working directory when running the script.

You can use this option as follows :

```yaml
commands:
    "my:script":
        type: shell
        script: ls -l
        options:
            workingDir: ./repository/project
```

## Multi-scripts commands

A command composed of multiple scripts can also specify options globally, using the 'options' key at the root of the command definition, or individually for each script, using the following syntax :

```yaml
commands:
    "my:script1":
        type: shell
        script:
            - ls -l
            - rm -rf ./var/cache
        options:
            workingDir: ./repository/project
    "my:script2":
        type: shell
        script:
            - ls -l
            - { script: "rm -rf *", options: { workingDir: "./repository/project/var/cache" } }
        options:
            workingDir: ./repository/project
```

The two previous scripts are completely identical.
Note in the second example how the options are inherited between the different levels of the command.

Thus, the workingDir are not chained, however, the last overload the previous one.

In practice, the second example does not have much interest.
It is only intended to illustrate how you can specify different options for each script of your command.