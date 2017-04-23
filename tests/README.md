# Predisque tests

* Unit tests are marked with `@group disconnected`
* Functional tests are marked with `@group connected`
* It'd be nice to be able to directly extend Predis tests, but we can't rely on composer with `--prefer-source`. Or
  maybe we can if we have to, but we'd rather not for obvious reasons. So, some duplication in test code is preferred.
