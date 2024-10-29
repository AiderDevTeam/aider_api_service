<?php

namespace App\Interfaces;

interface ShareLinkInterface
{
    public function getShareLinkTitle(): ?string;

    public function getShareLinkImage(): ?string;

    public function setShareLink(): bool;

    public function getShareLinkDescription(): ?string;
}
