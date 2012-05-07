MongoL
======

Simple and small MongoDB wrapper library for CodeIgniter.  
It works on top of the native PHP MongoDB Driver (http://php.net/mongo).

The idea is to be a as-small-as-possible library used only to ease database
switching and authentication using a CI config file or a DSN.  
In fact, all the parameters in the config file are converted to a single DSN
string.  
Opening a connection using a DSN is the preferred method, since it ensures that
if the connection is dropped, the driver automatically attemp to reconnect and
reauthenticate you.

How to use
----------

### The config file

The config file is very similar to CI's database.php config with the exception
of the naming conventions.

As in CI's native database config file, you have config groups. You can define
as many groups as you need and define one as the default group using
`$mdb['default_group'] = 'my-default-group'` (replacing 'my-default-group' with
the corresponding group name).

Here is a config example:

    :::php
    $mdb['default_group']    = 'mydb';
    
    $mdb['mydb']['host'] = 'localhost';
    $mdb['mydb']['port'] = 27017;
    $mdb['mydb']['user'] = 'db_user';
    $mdb['mydb']['pass'] = 'db_pass';
    $mdb['mydb']['name'] = 'db_name';

_The 'port', 'user' and 'pass' parameters are optional._

### Accessing the database

One interesting feature in MongoL is that it will try to create "shortcuts" to
the selected database by adding two public variables to the CI instance. One
with the same name as the config group, and another one with the generic name
_"mdb"_, both pointing to the same MongoDB instance.  
So if you have a group named 'mydb' you'll be able to do this
`$this->mydb->some_coll->find()` for example, but if the variable name is
already taken, MongoL will **not** overwrite it, instead you can still access
the db through "mdb" like so `$this->mdb->some_coll->find()` or through the
library name variable like this `$this->mongol->mydb->some_coll->find()`.

Remember that the latter is the native way, so if you mess up the database name
the PHP MongoDB driver will try to issue an equivalent to the shell's `use mydb`
selecting a new database without failing.  
There's also been cases where the authentication of the db returned using this
way is dropped when used before the CodeIgniter core class is loaded, if that's
the case you need to get the db with `$this->mongol->get_db()`.

Accessing the db with `$this->mdb` is the preferred way, since you may want to
change the group name or use a different group in development than the one used
in production, making the code useless.  
Despite this recommendation, using the group name may come in handy when having
multiple connections, making it easy to use either one, since `$this->mdb` will
always contain the database loaded with the default group.

### Selecting other databases

You can select (or "use") other databases by calling `Mongol::use_db('otherdb')`
where 'otherdb' is the group name that holds the config group as defined in your
mongodb.php config.  
If you need to connect to a database that is not defined in your config file,
you can pass a DSN string instead of a group name, MongoL will recognize it and
try to connect to it, for example:
`Mongol::use_db("mongo://user:pass@localhost/otherdb")`

If the `$group` parameter you passed `Mongol::use_db` corresponds to a valid
group name, MongoL will try to create a shortcut in CI instance with this name.
But if the parameter is a DSN there's no group name, so the database name will
be used instead, so using the example above, this database will be available
through `$this->otherdb`.  
`Mongol::use_db` will always return the new instance as well. But if you don't
want it to create the shortcuts, you can pass TRUE as the second parameter:

    :::php
    $m = Mongol::use_db('otherdb', TRUE);
    $otherdb = $m->get_db();

_`Mongol::use_db` will never overwrite the original connection and shortcuts._

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
        
        php tools/spark install -v1.0.1 MongoL
        
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

