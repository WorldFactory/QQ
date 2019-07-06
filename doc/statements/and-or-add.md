# Statements AND, OR and ADD

This statements are used to aggregates runner results.

## Statement AND

```
test:
  run:
    if:
      and:
        - php://return true;
        - php://return false;
    then: view://TRUE
    else: view://FALSE
```

In this example, the command will display FALSE.

The AND statement only accepts booleans.
Also, if you aggregate the results of SHELL commands, you will have to transform their returns into booleans.
This can be done with BOOL Runner.

```
test:
  run:
    if:
      and:
        - bool://ping -c 1 192.168.0.1
        - bool://ping -c 1 192.168.0.2
    then: view://The network address 192.168.0.1 and 192.168.0.2 are both active.
    else: view://The network address 192.168.0.1 and 192.168.0.2 are not both active.
```

## Statement OR

```
test:
  run:
    if:
      or:
        - php://return false;
        - php://return true;
    then: view://TRUE
    else: view://FALSE
```

In this example, the command will display TRUE.

## Statement ADD

The ADD declaration is used to aggregate the returns of several commands as a string.

```
test:
  run:
    - set: value
      from:
        add:
          - php://return "Pim, ";
          - php://return "Pam and ";
          - php://return "Poum.";
    - view://%value%
```

This command will display "Pim, Pam and Poum.".

The ADD statement only support string results.
