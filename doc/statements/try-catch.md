# Statement TRY / CATCH

This Statement allows you to catch errors occurring in one or more commands and possibly react in the appropriate way.

## Usage

```
commands:
    test-try:
        run:
            - try: ls /directory-that-does-not-exist
              catch: view://Folder 'directory-that-does-not-exist' not found.
            - try: ls /tmp
              catch: view://Folder 'tmp' not found.
```

In this example, the Try statement is used twice.

The first time, we try to display the list of files from a directory that does not exist. An error is generated. This is caught by the Try block which executes the associated Catch block.

The second time, we display the list of files in the / tmp directory (on a Linux system). This generates no errors and the associated Catch block is not executed.

## Without catching errors

```
commands:
    test-try:
        run:
            - try: ls /directory-that-does-not-exist
            - view://End of the script.
```

In this second example, the Try statement is used without an associated Catch block.

The command 'ls' can therefore generate an error without disturbing the continuation of the script.