<?php

use Luinuxscl\LnxSam\SamCapability;
use Luinuxscl\LnxSam\SamCapabilityKind;
use Luinuxscl\LnxSam\SamMode;
use Luinuxscl\LnxSam\SamRisk;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeReadTool;
use Luinuxscl\LnxSam\Tests\Fixtures\NotATool;

it('describes a read without a confirmation', function (): void {
    $capability = SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read fake records',
        permission: 'fake.read',
        handlers: [FakeReadTool::class],
    );

    expect($capability->kind)->toBe(SamCapabilityKind::Read)
        ->and($capability->minMode)->toBe(SamMode::ReadOnly)
        ->and($capability->requiresPhrase)->toBeFalse()
        ->and($capability->undoable)->toBeFalse()
        ->and($capability->confirmation())->toBe('none')
        ->and($capability->auditAction)->toBe('read')
        ->and($capability->exposes('sam'))->toBeTrue()
        ->and($capability->exposes('api'))->toBeFalse();
});

it('derives the phrase from the risk of a proposal', function (): void {
    $reversible = SamCapability::propose(
        key: 'fake.update',
        source: 'fake',
        label: 'Update',
        risk: SamRisk::Reversible,
        undoable: true,
    );

    $bulk = SamCapability::propose(
        key: 'fake.bulk',
        source: 'fake',
        label: 'Bulk',
        risk: SamRisk::Bulk,
    );

    $create = SamCapability::propose(
        key: 'fake.create',
        source: 'fake',
        label: 'Create',
        risk: SamRisk::Irreversible,
    );

    expect($reversible->confirmation())->toBe('click')
        ->and($reversible->requiresPhrase)->toBeFalse()
        ->and($bulk->confirmation())->toBe('phrase')
        ->and($create->confirmation())->toBe('phrase')
        ->and($reversible->kind)->toBe(SamCapabilityKind::Propose)
        ->and($reversible->auditAction)->toBe('propose');
});

it('rejects a key without the prefix of its source', function (): void {
    SamCapability::read(key: 'other.read', source: 'fake', label: 'Read');
})->throws(InvalidArgumentException::class, 'prefixed with its source');

it('rejects a reversible proposal without an undo', function (): void {
    SamCapability::propose(
        key: 'fake.update',
        source: 'fake',
        label: 'Update',
        risk: SamRisk::Reversible,
    );
})->throws(InvalidArgumentException::class, 'declares no undo');

it('rejects a read with a risk', function (): void {
    new SamCapability(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        kind: SamCapabilityKind::Read,
        risk: SamRisk::Reversible,
    );
})->throws(InvalidArgumentException::class, 'cannot declare a confirmation risk');

it('rejects an unknown surface', function (): void {
    SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        surfaces: ['cli'],
    );
})->throws(InvalidArgumentException::class, 'unknown surface');

it('rejects an unknown handler', function (): void {
    SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        handlers: ['Luinuxscl\\LnxSam\\Tests\\Fixtures\\Missing'],
    );
})->throws(InvalidArgumentException::class, 'unknown handler');

it('exports a bounded representation', function (): void {
    $capability = SamCapability::propose(
        key: 'fake.archive',
        source: 'fake',
        label: 'Archive',
        risk: SamRisk::Reversible,
        undoable: true,
        permission: 'fake.write',
        surfaces: ['sam', 'mcp', 'api'],
        handlers: [FakeReadTool::class, NotATool::class],
    );

    $array = $capability->toArray();

    expect($array['key'])->toBe('fake.archive')
        ->and($array['kind'])->toBe('propose')
        ->and($array['risk'])->toBe('reversible')
        ->and($array['min_mode'])->toBe('draft')
        ->and($array['requires_phrase'])->toBeFalse()
        ->and($array['surfaces'])->toBe(['sam', 'mcp', 'api'])
        ->and($array['handlers'])->toBe([FakeReadTool::class, NotATool::class]);
});
