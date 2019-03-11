# Using environment variables

You can use environment variables in your scripts.

## Basic usage

To do this, simply use following syntax :

```yaml
commands:
    "my:script":
        type: php
        script: $a = 1 + ${ENV_VAR}; echo $a;
```

In the case where ENV_VAR environment variable is 3, then the previous script will simply display the value 4.

Many options can also containing environment variables like the 'target' option of the 'docker' runner.

```yaml
commands:
    "my:other:script":
        type: docker
        script: ls -la
        options:
            target: ${TARGET_CONTAINER}
```

Your command will dynamically target a container based on the content of the TARGET_CONTAINER environment variable.

## Disable the interpretation of environment variables

In some cases, you will not want the environment variable to be interpreted by QQ. Maybe because you want it to be interpreted when executed in a particular context, such as a container, or on a remote location.

To do this, just comment on the $ symbol.

```yaml
commands:
    "my:script":
        type: php
        script: echo "The env var syntax is \${ENV_VAR}";
```

This script will display : _The env var syntax is ${ENV_VAR}_

Note that backslash has been removed by QQ.

## Cross Interpretation with the YAML Interpreter

Sometimes you will have to comment a $ symbol while it is in a string surrounded by double quotation marks.

In this type of case, the YAML interpreter will cause you some problems because it will have its own interpretation of the `\$` string.
To avoid any problem, you will need to add a second backslash in front of the $ symbol.

```yaml
commands:
    "my:script":
        type: php
        script: "echo 'The env var syntax is : \\${ENV_VAR}';"
```

This script will display : _The env var syntax is : ${ENV_VAR}_
