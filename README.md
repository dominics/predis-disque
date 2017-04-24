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

## Testing

**Warning: the test suite uses the `DEBUG FLUSHALL` command. Do not run the tests against a Disque instance you care
about.**

`./vendor/bin/phpunit`
