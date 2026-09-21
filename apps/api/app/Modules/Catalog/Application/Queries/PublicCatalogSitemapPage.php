<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogSitemapPage
{
    /** @param list<string> $slugs */
    public function __construct(public array $slugs, public int $currentPage, public int $total)
    {
        if ($this->currentPage < 1 || $this->currentPage > 10000 || $this->total < 0 || $this->total > 5000000 || count($this->slugs) > 500) {
            throw new \OverflowException('Invalid sitemap page');
        }

        $seen = [];
        foreach ($this->slugs as $slug) {
            if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) !== 1 || isset($seen[$slug])) {
                throw new \OverflowException('Invalid sitemap slug');
            }
            $seen[$slug] = true;
        }
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / 500));
    }
}
