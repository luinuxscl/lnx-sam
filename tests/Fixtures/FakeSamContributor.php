<?php

namespace Luinuxscl\LnxSam\Tests\Fixtures;

use App\Models\User;
use Illuminate\Http\Request;
use Luinuxscl\LnxSam\SamContext;
use Luinuxscl\LnxSam\SamContributor;
use Luinuxscl\LnxSam\SamProposalPresenter;

/**
 * Contributor with no manifest: the state of a module before migrating.
 */
class FakeSamContributor implements SamContributor
{
    public function source(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Fake module';
    }

    public function knowledge(User $actor): ?string
    {
        return 'Dominio falso.';
    }

    public function readTools(User $actor): array
    {
        return [];
    }

    public function proposeTools(User $actor): array
    {
        return [];
    }

    public function context(Request $request): ?SamContext
    {
        return null;
    }

    public function rowContext(User $actor, string $resource, string $id, array $filters = []): ?SamContext
    {
        return null;
    }

    public function signals(User $actor, SamContext $context): array
    {
        return [];
    }

    public function suggestions(User $actor, ?SamContext $context): array
    {
        return [];
    }

    public function proposals(): ?SamProposalPresenter
    {
        return null;
    }
}
