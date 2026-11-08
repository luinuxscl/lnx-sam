<?php

namespace Luinuxscl\LnxSam\Tests\Fixtures;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Luinuxscl\LnxSam\Tools\SamProposeTool;

class FakeProposeTool extends SamProposeTool
{
    public const TOOL_NAME = 'fake_propose';

    public function description(): string
    {
        return 'Propose a fake change.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    protected function run(Request $request, User $user): string
    {
        return '{"draft_id":"draft-1"}';
    }
}
