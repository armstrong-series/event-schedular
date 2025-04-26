<?php

namespace App\Contracts;
use App\Models\User;

interface AuthInterface
{
    public function signin(array $credentials): array;
    public function me(): User;
    public function logout(): bool;

}
