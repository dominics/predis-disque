<?php

namespace Predisque\Connection;

use Predisque\PredisqueException;

class ConnectionException extends \Predis\Connection\ConnectionException implements PredisqueException
{
}
