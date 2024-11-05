<?php

namespace Ruzaik11\SeederTracker\Contracts;

interface TrackableSeederInterface
{
    public function seedData();
    public function hasBeenExecuted(): bool;
    public function resetTracking(): void;
}
