<?php

namespace Ruzaik\SeederTracker\Contracts;

interface TrackableSeederInterface
{
    public function seedData();
    public function hasBeenExecuted(): bool;
    public function resetTracking(): void;
}
