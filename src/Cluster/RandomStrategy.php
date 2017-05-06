<?php

namespace Predisque\Cluster;

use Predis\Connection\NodeConnectionInterface;
use Predisque\Connection\Parameters;

class RandomStrategy implements StrategyInterface
{
    /**
     * @param array $nodes
     * @return Parameters
     */
    public function pickNodeFromHello(array $nodes): Parameters
    {
        // Don't allow nodes with priority higher than 99 (LEAVING)
        $nodes = array_filter($nodes, function ($node) {
            return (int)$node[3] <= 99;
        });

        $node = array_values($nodes)[random_int(0, count($nodes) - 1)];

        return new Parameters([
            'host' => $node[1],
            'port' => $node[2],
        ]);
    }

    /**
     * @param array $connections
     * @return NodeConnectionInterface
     */
    public function pickInitialConnection(array $connections): NodeConnectionInterface
    {
        return array_values($connections)[random_int(0, count($connections) - 1)];
    }
}
