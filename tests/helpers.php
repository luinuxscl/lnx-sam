<?php

use Luinuxscl\LnxSam\Tests\Fixtures\CapabilityContributor;

/**
 * Clear the runtime between cases.
 */
function resetSamRuntime(): void
{
    CapabilityContributor::$mode = 'ok';

    app()->forgetInstances();
}
