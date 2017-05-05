<?php

namespace Predisque\Cluster;

use Predis\Connection\NodeConnectionInterface;

class RandomStrategy implements StrategyInterface
{
    /**
     * @param array $nodes
     * @return NodeConnectionInterface
     */
    public function selectInitialNode(array $nodes): NodeConnectionInterface
    {
        return $nodes[random_int(0, count($nodes) - 1)];
    }
}
