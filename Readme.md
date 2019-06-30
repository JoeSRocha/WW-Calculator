# WW Calculator
A calculation plugin for DFS outcomes. Leverages `PHPUnit Framework` and `SPL Autoloader`. Developed for internal usage.


## Getting Started
 To test you will add new `assertions` to `tests/` folder. This may be used in tandem with DFS data validator (request access via Author).


## Installation
To setup locally or `Continuous Testing`, follow the WP [guide](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/#setup). For local, you will find the script `bin/install-wp-tests.sh`. This will help you create your local database for testing.


## Customize
To add new classes, you will need to follow the SPL Autoloaders pattern. Do not require/include classes directory.
```
$ cd classes/
$ touch class-<class-name>-.php
```


## Author
Joe S. Rocha<br>
AdroitGraphics.com
