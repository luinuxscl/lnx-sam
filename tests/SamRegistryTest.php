<?php

use Luinuxscl\LnxSam\SamCapability;
use Luinuxscl\LnxSam\SamCapabilityKind;
use Luinuxscl\LnxSam\SamMode;
use Luinuxscl\LnxSam\SamRegistry;
use Luinuxscl\LnxSam\SamRisk;
use Luinuxscl\LnxSam\Tests\Fixtures\CapabilityContributor;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeProposeTool;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeReadTool;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeSamContributor;
use Luinuxscl\LnxSam\Tests\Fixtures\MismatchedSamContributor;

it('registers a contributor whose source matches', function (): void {
    $registry = app(SamRegistry::class);

    $registry->register('fake', FakeSamContributor::class);

    expect($registry->has('fake'))->toBeTrue()
        ->and($registry->contributor('fake'))->toBeInstanceOf(FakeSamContributor::class)
        ->and($registry->contributors())->toHaveCount(1)
        ->and($registry->declaresCapabilities('fake'))->toBeFalse();
});

it('rejects a source that does not match the contributor', function (): void {
    app(SamRegistry::class)->register('fake', MismatchedSamContributor::class);
})->throws(InvalidArgumentException::class, 'does not match');

it('rejects an unknown contributor class', function (): void {
    app(SamRegistry::class)->register('fake', 'Luinuxscl\\LnxSam\\Tests\\Fixtures\\Missing');
})->throws(InvalidArgumentException::class, 'does not exist');

it('rejects an empty source', function (): void {
    app(SamRegistry::class)->register('  ', FakeSamContributor::class);
})->throws(InvalidArgumentException::class, 'non-empty');

it('collects the declared capabilities of a module', function (): void {
    $registry = app(SamRegistry::class);

    $registry->register('fake', CapabilityContributor::class);

    expect($registry->capabilities())->toHaveCount(2)
        ->and($registry->capabilitiesFor('fake'))->toHaveCount(2)
        ->and($registry->capability('fake.read'))->toBeInstanceOf(SamCapability::class)
        ->and($registry->capability('fake.read')->kind)->toBe(SamCapabilityKind::Read)
        ->and($registry->capability('fake.propose')->minMode)->toBe(SamMode::Draft)
        ->and($registry->capability('fake.unknown'))->toBeNull()
        ->and($registry->declaresCapabilities('fake'))->toBeTrue();
});

it('resolves the capability a handler implements', function (): void {
    $registry = app(SamRegistry::class);

    $registry->register('fake', CapabilityContributor::class);

    expect($registry->capabilityForHandler(FakeReadTool::class)?->key)->toBe('fake.read')
        ->and($registry->capabilityForHandler(FakeProposeTool::class)?->key)->toBe('fake.propose')
        ->and($registry->capabilityForHandler('Luinuxscl\\LnxSam\\SamRegistry'))->toBeNull();
});

it('reads risk and mode from the manifest', function (): void {
    CapabilityContributor::$mode = 'override';

    app(SamRegistry::class)->register('fake', CapabilityContributor::class);

    $tool = app(FakeProposeTool::class);

    expect($tool->risk())->toBe(SamRisk::Bulk)
        ->and($tool->minimumMode())->toBe(SamMode::Assisted);
});

it('falls back to the constants of a tool without manifest', function (): void {
    app(SamRegistry::class)->register('fake', FakeSamContributor::class);

    $tool = app(FakeProposeTool::class);

    expect($tool->risk())->toBe(SamRisk::Reversible)
        ->and($tool->minimumMode())->toBe(SamMode::Draft);
});

it('rejects a capability of another source', function (): void {
    CapabilityContributor::$mode = 'foreign';

    app(SamRegistry::class)->register('fake', CapabilityContributor::class);
})->throws(InvalidArgumentException::class, 'declares the source');

it('rejects a duplicated capability key', function (): void {
    CapabilityContributor::$mode = 'duplicate';

    app(SamRegistry::class)->register('fake', CapabilityContributor::class);
})->throws(InvalidArgumentException::class, 'declared twice');

it('replaces the manifest when the same source registers twice', function (): void {
    $registry = app(SamRegistry::class);

    $registry->register('fake', CapabilityContributor::class);
    $registry->register('fake', FakeSamContributor::class);

    expect($registry->capabilities())->toBe([])
        ->and($registry->declaresCapabilities('fake'))->toBeFalse()
        ->and($registry->contributors())->toHaveCount(1);
});

it('flushes every declaration', function (): void {
    $registry = app(SamRegistry::class);

    $registry->register('fake', CapabilityContributor::class);
    $registry->flush();

    expect($registry->contributors())->toBe([])
        ->and($registry->capabilities())->toBe([])
        ->and($registry->profile())->toBeNull()
        ->and($registry->surface())->toBeNull();
});
