<?php

namespace App\Interfaces;

interface ReviewInterface
{
    public function reviews(): mixed;

    public function numberOfReviews(): int;

    public function sumOfRatings(): int;

    public function recordAverageRating(): void;
}
