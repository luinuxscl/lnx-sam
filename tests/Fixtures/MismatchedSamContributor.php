<?php

namespace Luinuxscl\LnxSam\Tests\Fixtures;

/**
 * Declares a source the registry did not register.
 */
class MismatchedSamContributor extends FakeSamContributor
{
    public function source(): string
    {
        return 'other';
    }
}
