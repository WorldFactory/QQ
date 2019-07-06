# Statement WHILE / DO

This statement allow you to execute a script as long as another script return True.

## Usage

By default, the 'while' key uses the Expr Runner.

So you can directly use PHP expressions to determine if the loop should continue.

```
commands:
  test:
    run:
      - set: var
        from: json://1
      - while: $var < 10
        do:
          - set: var
            from: php://return ++$var;
          - php://echo '$var = ' . $var . PHP_EOL;
      - view://Fin du while.
```

For the purposes of this example, we did not use a fluctuating exterior element to determine when the loop should end.

So we have based the end of our loop on the value of a variable that we make ourselves fluctuate.
