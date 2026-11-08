<?php

namespace Luinuxscl\LnxSam\Tests\Fixtures;

use Luinuxscl\LnxSam\SamCapability;
use Luinuxscl\LnxSam\SamCapabilityProvider;
use Luinuxscl\LnxSam\SamMode;
use Luinuxscl\LnxSam\SamRisk;

/**
 * Contributor that declares a manifest, in one of the states the runtime must
 * read or reject.
 */
class CapabilityContributor extends FakeSamContributor implements SamCapabilityProvider
{
    public static string $mode = 'ok';

    /**
     * @return list<SamCapability>
     */
    public function capabilities(): array
    {
        return match (self::$mode) {
            'foreign' => [
                SamCapability::read('other.read', 'other', 'Foreign capability'),
            ],
            'duplicate' => [
                SamCapability::read('fake.read', 'fake', 'Read'),
                SamCapability::read('fake.read', 'fake', 'Read again'),
            ],
            'missing_handler' => [
                SamCapability::read('fake.read', 'fake', 'Read'),
            ],
            'override' => [
                SamCapability::propose(
                    key: 'fake.propose',
                    source: 'fake',
                    label: 'Propose a bulk change',
                    risk: SamRisk::Bulk,
                    minMode: SamMode::Assisted,
                    handlers: [FakeProposeTool::class],
                ),
            ],
            default => [
                SamCapability::read(
                    key: 'fake.read',
                    source: 'fake',
                    label: 'Read fake records',
                    permission: 'fake.read',
                    handlers: [FakeReadTool::class],
                ),
                SamCapability::propose(
                    key: 'fake.propose',
                    source: 'fake',
                    label: 'Propose a fake change',
                    risk: SamRisk::Reversible,
                    undoable: true,
                    permission: 'fake.write',
                    surfaces: ['sam', 'mcp', 'api', 'ui'],
                    handlers: [FakeProposeTool::class],
                ),
            ],
        };
    }
}
