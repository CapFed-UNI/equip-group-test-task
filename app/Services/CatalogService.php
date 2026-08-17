<?php

namespace App\Services;

use App\Models\Group;
use App\Repositories\ProductRepository;
use App\Support\CatalogQuery;

class CatalogService
{
    public function __construct(
        private readonly GroupTreeService $tree,
        private readonly ProductRepository $products,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function listing(?Group $group, CatalogQuery $query): array
    {
        $groupIds = $group === null
            ? null
            : $this->tree->descendantIdsIncludingSelf((int) $group->id);

        return [
            'products' => $this->products->paginate($groupIds, $query),
            'sidebar' => $this->tree->sidebar($group?->id),
            'breadcrumbs' => $this->tree->breadcrumbs($group),
            'currentGroup' => $group,
            'childGroups' => $group === null
                ? collect()
                : $this->tree->childrenOf((int) $group->id),
            'query' => $query,
        ];
    }
}
