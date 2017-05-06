# Predisque

[![Build Status](https://travis-ci.org/dominics/predisque.svg?branch=master)](https://travis-ci.org/dominics/predisque)

A [Disque](https://github.com/antirez/disque) client for PHP 7.1+ based on (and depending on) Predis. `predis : redis :: predisque : disque`, yeah?

The library is made up of:

 * A set of commands (as in `Predis\Command\Command`) that you can use with Disque, and a server profile for Predis
 * A `Predisque\Client` that's set up to use port 7711 and the Disque server profile by default
 * An aggregate connection (`Predisque\Connection\Aggregate\DisqueCluster`) that handles talking to a Disque cluster as a whole
   * Supports discovery via `HELLO`
   * Supports retrying commands transparently if they fail with a `ConnectionException` (so we handle `-LEAVING` responses by swapping to another node)

Because Predisque is based on a feature-rich Predis core, we get things like pipelining and nice underlying Redis protocol
support "for free". (A Disque client library should probably not contain its own native Redis protocol parser.)

Support for all Disque commands is implemented, including exotic ones like `DEBUG`, `MONITOR`, and `CLUSTER INFO`. See
[TODO.md](TODO.md) for progress on reaching a stable API.

Not yet stable for production use.

## Usage

The PHP namespace is `\Predisque`

To create a client, instantiate a `new \Predisque\Client(string|array $parameters = [], array $options = [])`.

The method signature is similar to a Predis client: the connection parameters (host, port, etc.) come first, and
can be given in array or DSN/URI string notation. Then a set of more general options comes after. So, all of the
following have the same result (a connection to a single Disque server):

```php
$client = new \Predisque\Client();
$client = new \Predisque\Client('tcp://127.0.0.1:7711');
$client = new \Predisque\Client([
    'host' => '127.0.0.1',
    'port' => 7711
]);
```

Connecting to multiple servers is as easy as passing an array of node details:

```php
$client = new \Predisque\Client(['tcp://127.0.0.1:7711', 'tcp://127.0.0.1:7712', 'tcp://127.0.0.1:7713']);
```

### Connection Switching

Only one of these connections will be utilized at a time, but the others will be used if the connection to the first
server fails, or if it gives a `-LEAVING` response. Also, details from the cluster's `HELLO` response will be used to
connect to even further backup nodes (so make sure all nodes are accessible to every client).

You can control this behavior with the `discover` option:

 ```php
 $client = new \Predisque\Client('tcp://127.0.0.1:7711', ['discover' => false]);
 ```

You can also *disable* all Disque-specific connection niceties (and the aggregate connection in general), by passing the
`'cluster' => false` option:

 ```php
 $client = new \Predisque\Client('tcp://127.0.0.1:7711', ['cluster' => false]);
 ```

## Testing

**Warning: the test suite uses the `DEBUG FLUSHALL` command. Do not run the tests against a Disque instance you care
about.** Running the tests will destroy all data on the disque instance they use. (You won't have the tests if
you're just using the client as a dist from composer.)

To execute the test suite, run e.g. `DISQUE_SERVER_PORT=7711 ./vendor/bin/phpunit` - the port must be specified, or the
unhelpful default of 12345 will be used to prevent accidents.
