### Overview

Bencode is a library for PHP that implements the [bencode](http://wikipedia.org/wiki/Bencode) encoding algorithm.

### Usage

    bencode(array(1,2,3,4,5));
    bdecode("li1ei2ei3ei4ei5ee");

### Tests

Bencode uses the [PHPUnit](http://www.phpunit.de/) unit testing framework for testing. In order to run the tests, run the following command from the project root directory:

    $ phpunit tests/Bencode.php

### Requirements

Bencode requires PHP version 5 or greater.

### License

Bencode is released under the terms of the MIT license. Please read the LICENSE file for further information.
