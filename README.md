ernilambar/log-command
======================

Debug log helpers.



Quick links: [Using](#using) | [Installing](#installing)

## Using

This package implements the following commands:

### wp log clear

Clear debug log content.

~~~
wp log clear 
~~~

**EXAMPLES**

    # Clear log.
    $ wp log clear
    Success: Debug log content cleared successfully.



### wp log delete

Delete debug log file.

~~~
wp log delete 
~~~

**EXAMPLES**

    # Delete log file.
    $ wp log delete
    Success: Debug log file deleted successfully.



### wp log path

Gets the path to the debug log file.

~~~
wp log path 
~~~

**EXAMPLES**

    # Get path.
    $ wp log path
    /Users/johndoe/Sites/staging/app/public/wp-content

## Installing

Installing this package requires WP-CLI v2.11 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install the latest stable version of this package with:

```bash
wp package install ernilambar/log-command:@stable
```

To install the latest development version of this package, use the following command instead:

```bash
wp package install ernilambar/log-command:dev-master
```


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
