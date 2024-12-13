<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Validator;

use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Model\Dataset;

/**
 * @package Bouledepate\JsonRpc\Validator
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface ValidatorInterface
{
    public function validate(Dataset $dataset): void;
}