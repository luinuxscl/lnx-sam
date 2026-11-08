<?php

namespace Luinuxscl\LnxSam\Tests\Fixtures;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Luinuxscl\LnxSam\Tools\SamReadResult;
use Luinuxscl\LnxSam\Tools\SamReadTool;

class FakeReadTool extends SamReadTool
{
    public const TOOL_NAME = 'fake_read';

    public function description(): string
    {
        return 'Read fake records.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    protected function run(Request $request, User $user): SamReadResult
    {
        return new SamReadResult(data: ['items' => [], 'total' => 0]);
    }
}
