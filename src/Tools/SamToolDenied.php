<?php

namespace Luinuxscl\LnxSam\Tools;

use RuntimeException;

/**
 * A module refused a tool call before it could produce a result.
 *
 * A proposal that the module rejects is not a proposal: the runtime must audit
 * the refusal with its real reason instead of recording a success with no
 * payload. The module raises this exception with the message the model should
 * read and the reason that belongs in the audit trail.
 */
class SamToolDenied extends RuntimeException
{
    public const FORBIDDEN = 'forbidden';

    public const OUT_OF_SCOPE = 'out_of_scope';

    public const VALIDATION = 'validation';

    public const FAILED = 'failed';

    public function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function forbidden(string $message): self
    {
        return new self(self::FORBIDDEN, $message);
    }

    public static function outOfScope(string $message): self
    {
        return new self(self::OUT_OF_SCOPE, $message);
    }

    public static function validation(string $message): self
    {
        return new self(self::VALIDATION, $message);
    }

    public static function failed(string $message): self
    {
        return new self(self::FAILED, $message);
    }
}
