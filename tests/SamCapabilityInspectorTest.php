<?php

use Luinuxscl\LnxSam\SamCapability;
use Luinuxscl\LnxSam\SamCapabilityInspector;
use Luinuxscl\LnxSam\SamRisk;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeProposeTool;
use Luinuxscl\LnxSam\Tests\Fixtures\FakeReadTool;
use Luinuxscl\LnxSam\Tests\Fixtures\NotATool;

it('identifies the transport of a handler', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    expect($inspector->typeOf(FakeReadTool::class))->toBe(SamCapabilityInspector::TYPE_SAM_READ)
        ->and($inspector->typeOf(FakeProposeTool::class))->toBe(SamCapabilityInspector::TYPE_SAM_PROPOSE)
        ->and($inspector->typeOf(NotATool::class))->toBe(SamCapabilityInspector::TYPE_OTHER);
});

it('reports no problem for a consistent manifest', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    $read = SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        handlers: [FakeReadTool::class],
    );

    $propose = SamCapability::propose(
        key: 'fake.propose',
        source: 'fake',
        label: 'Propose',
        risk: SamRisk::Reversible,
        undoable: true,
        handlers: [FakeProposeTool::class],
    );

    expect($inspector->problems($read))->toBe([])
        ->and($inspector->problems($propose))->toBe([]);
});

it('reports a capability without a handler', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    $capability = SamCapability::read(key: 'fake.read', source: 'fake', label: 'Read');

    expect($inspector->problems($capability))->toContain('declares no handler');
});

it('reports a read without a read handler', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    $capability = SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        handlers: [NotATool::class],
    );

    $problems = $inspector->problems($capability);

    expect($problems)->toContain('exposes [sam] without a [sam read] handler')
        ->and(implode(' ', $problems))->toContain('is not a Sam or MCP tool');
});

it('reports a proposal that declares a read tool', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    $capability = SamCapability::propose(
        key: 'fake.propose',
        source: 'fake',
        label: 'Propose',
        risk: SamRisk::Irreversible,
        handlers: [FakeReadTool::class],
    );

    expect($inspector->problems($capability))->toContain(
        'the proposal declares the read tool ['.FakeReadTool::class.']'
    );
});

it('describes each handler with its class and transport', function (): void {
    $inspector = app(SamCapabilityInspector::class);

    $capability = SamCapability::read(
        key: 'fake.read',
        source: 'fake',
        label: 'Read',
        handlers: [FakeReadTool::class, NotATool::class],
    );

    expect($inspector->handlers($capability))->toBe([
        ['class' => FakeReadTool::class, 'type' => SamCapabilityInspector::TYPE_SAM_READ],
        ['class' => NotATool::class, 'type' => SamCapabilityInspector::TYPE_OTHER],
    ]);
});
