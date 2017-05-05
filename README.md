# predisque

An early implementation of Disque client commands for the Predis client.

Already useful if you just need to connect to a single Disque node and interrogate
it, e.g. for monitoring purposes. This is surprisingly hard with some
PHP Disque clients, because they're set up to connect to a random node (from
the `HELLO`).

Also allows for pipelining, etc. No aggregate connection is provided yet.

All commands are implemented, including exotic ones like `DEBUG`, `MONITOR`, and `CLUSTER INFO`. See [TODO.md](TODO.md)
for progress details.

Should be considered alpha code so far; do not use it in production yet.


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

### Connecting to Multiple Servers

WIP

```php
$client = new \Predisque\Client(['tcp://127.0.0.1:7711', 'tcp://127.0.0.1:7712', 'tcp://127.0.0.1:7713']);
```


## Testing

**Warning: the test suite uses the `DEBUG FLUSHALL` command. Do not run the tests against a Disque instance you care
about.**

To execute the test suite, run `./vendor/bin/phpunit`
