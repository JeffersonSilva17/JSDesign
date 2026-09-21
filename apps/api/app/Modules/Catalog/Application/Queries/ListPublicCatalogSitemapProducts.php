<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class ListPublicCatalogSitemapProducts
{
    public function __construct(private PublicCatalogQuery $query) {}

    public function execute(int $page): PublicCatalogSitemapPage
    {
        if ($page < 1 || $page > 10000) {
            throw new \OverflowException('Invalid sitemap page');
        }

        return $this->query->sitemapPage($page);
    }

    /** @return list<array{slug: string, label: string}> */
    public function categories(): array
    {
        $categories = $this->query->sitemapCategories();
        if (count($categories) > 4998) {
            throw new \OverflowException('Invalid sitemap categories');
        }

        $seen = [];
        foreach ($categories as $category) {
            $slug = $category['slug'] ?? null;
            if (! is_string($slug) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) !== 1 || isset($seen[$slug])) {
                throw new \OverflowException('Invalid sitemap category');
            }
            $seen[$slug] = true;
        }

        return $categories;
    }
}
