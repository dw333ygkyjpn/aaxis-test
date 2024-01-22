<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\ModelFactory;

final class UserFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'password' => self::faker()->text(),
            'roles' => [],
            'username' => self::faker()->text(180),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
