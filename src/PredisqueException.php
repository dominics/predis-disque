<?php

namespace Predisque;

/**
 * Interface implemented by most exceptions thrown from Predisque
 *
 * Most Predisque exceptions around connection handling and the low-level protocol extend their Predis counterparts.
 * This interface cans help identify the ones thrown from Predisque-specific code.
 */
interface PredisqueException
{
}
