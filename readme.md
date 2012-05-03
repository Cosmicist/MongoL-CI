MongoL
======

Simple and small MongoDB wrapper library for CodeIgniter.  
It works on top of the PHP native Mongo driver (http://php.net/mongo).

The idea is to be a small-as-possible library used only to ease database
switching and authentication using a CI config file or a DSN.  
In fact, all the parameters in the config file are converted to a single string
DSN. Connecting this way ensures that if the connection is dropped PHP will try
to reconnect automatically, this is why using a DSN is recommended.

How to use
----------

### The config file

The config file is very similar to CI's config/database.php with the exception
of the naming conventions.

As in CI's native database config file, you have config groups. You can define
as many groups as you need and define one as the default group using
`$mdb['default_group'] = 'my-default-group'`, replacing 'my-default-group' with
the corresponding group name.

### Accessing the database

One interesting feature in MongoL is that it will try to create "shortcuts" to
the selected database by adding two a public variables to the CI instance. One
with the same name as the config group, and another one with the generic name
_"mdb"_, both pointing to the same MongoDB instance.  
So if you have a group named 'mydb' you'll be able to do this
`$this->mydb->some_coll->findAll()` for example, but if the variable name is
already taken, MongoL will **not** overwrite it, instead you can still access
it through "mdb" like so `$this->mdb->some_coll->findAll()` or through the
library name variable like this `$this->mongol->mydb->some_coll->findAll()`.

Remember that the latter is the native way, so if you mess up the database name
the PHP MongoDB driver will try to issue an equivalent to the shell's `use mydb`
selecting a new database without failing, so the former two methods are
preferred.

### Selecting other databases

You can select (or "use") other databases by calling `Mongol::use_db('otherdb')`
where $group will hold the config group as defined in your mongodb.php config.  
If you need to connect to a database that is not defined in your config file,
you can pass a DSN string instead of a group name, MongoL will recognize it and
try to connect to it, for example:
`$m = Mongol::use_db("mongo://user:pass@localhost/otherdb")`

_Note that `Mongol::use_db` method will always return a new MongoL instance_
_with a new connection, the original will not be removed nor closed._

Requirements
------------

* **CodeIgniter 2+**
* **PHP 5.1+**
* **MongoDB PHP Driver** [More info](http://php.net/mongo)
* **MongoDB 2.0.4** It may work with older versions...

Installation
------------

### Spark Installation

1.  Download and install Sparks into your project from http://getsparks.org/.
2.  Open the terminal, go to your CI+Sparks project root and type  
    
    `php tools/spark install -v1.0.0 MongoL`
    
Copyright & License
-------------------

MongoL is licensed under MIT License

Copyright (c) 2012 Luciano Longo

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

