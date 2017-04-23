# predis-disque

A partial implementation of Disque client commands for the Predis client. 

Useful if you just need to connect to a single Disque node and interrogate
it, e.g. for monitoring purposes. This is surprisingly hard with some
PHP Disque clients, because they're set up to connect to a random node (from
the `HELLO`).

Also allows for pipelining, etc. No aggregate connection is provided yet.

The command set is incomplete.

A work in progress.

Obviously, everything about this implementation that is good is due to
@nrk/Daniele Alessandri - and everything about it that is bad is due
to @dominics :)
