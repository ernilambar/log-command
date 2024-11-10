ernilambar/log-command
======================

Debug log helpers. Note: By default "reverse chronological" order is used.



Quick links: [Using](#using) | [Installing](#installing)

## Using

This package implements the following commands:

### wp log clear

Clears debug log content.

~~~
wp log clear 
~~~

**EXAMPLES**

    # Clear log.
    $ wp log clear
    Success: Debug log content cleared successfully.



### wp log count

Gets number of entries.

~~~
wp log count 
~~~

**EXAMPLES**

    # Get log entries count.
    $ wp log count
    4



### wp log delete

Deletes debug log file.

~~~
wp log delete 
~~~

**EXAMPLES**

    # Delete log file.
    $ wp log delete
    Success: Debug log file deleted successfully.



### wp log file

Gets the log file.

~~~
wp log file 
~~~

**EXAMPLES**

    # Get log file.
    $ wp log file
    /Users/johndoe/Sites/staging/app/public/wp-content/debug.log



### wp log get

Gets log entries.

~~~
wp log get [<number>] [--all] [--chronological]
~~~

**OPTIONS**

	[<number>]
		Number of entries.

	[--all]
		If set, all entries are displayed.

	[--chronological]
		If set, chronological order is used.

**EXAMPLES**

    # Get 2 recent entries.
    $ wp log get 2
    [09-Nov-2024 06:01:29 UTC] Automatic updates starting...
    [09-Nov-2024 06:01:31 UTC] Automatic updates complete.



### wp log list

Lists log entries.

~~~
wp log list [--field=<field>] [--fields=<fields>] [--format=<format>] [--chronological]
~~~

**OPTIONS**

	[--field=<field>]
		Returns the value of a single field.

	[--fields=<fields>]
		Limit the output to specific fields.

	[--format=<format>]
		Render output in a particular format.
		---
		default: table
		options:
		  - table
		  - csv
		  - json
		  - yaml
		---

	[--chronological]
		If set, chronological order is used.

**AVAILABLE FIELDS**

These fields will be displayed by default for each entry:

* log_date
* excerpt

These fields are optionally available:

* date
* time_ago
* description

**EXAMPLES**

    # List entries.
    $ wp log list --format=csv
    date,excerpt
    "09-Nov-2024 06:01:31 UTC","Automatic updates complete."
    "09-Nov-2024 06:01:29 UTC","Automatic updates starting..."
    ...



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
wp package install ernilambar/log-command:dev-main
```


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
