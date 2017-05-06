<?php

namespace Predisque\Cluster;

use Predis\Connection\NodeConnectionInterface;
use Predisque\Connection\Parameters;

class RandomStrategy implements StrategyInterface
{
    /**
     * @param array $nodes
     * @return Parameters|null
     */
    public function pickNodeFromHello(array $nodes): ?Parameters
    {
        if (count($nodes) === 0) {
            return null;
        }

        if (count($nodes) === 1) {
            return array_pop($nodes);
        }


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
        if (count($connections) === 1) {
            return array_pop($connections);
        }

        return array_values($connections)[random_int(0, count($connections) - 1)];
    }
}
