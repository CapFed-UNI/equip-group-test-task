<?php

namespace App\Support;

use App\Enums\ProductSort;
use Illuminate\Http\Request;

final class CatalogQuery
{
    public function __construct(
        public readonly ProductSort $sort,
        public readonly int $perPage,
        public readonly int $page,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $options = config('catalog.per_page_options');
        $default = (int) config('catalog.per_page_default');
        $perPage = (int) $request->query('per_page', $default);

        if (! in_array($perPage, $options, true)) {
            $perPage = $default;
        }

        return new self(
            sort: ProductSort::fromQuery($request->query('sort')),
            perPage: $perPage,
            page: max(1, (int) $request->query('page', 1)),
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function toQuery(array $overrides = []): array
    {
        $query = [
            'sort' => $this->sort->value,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];

        foreach ($overrides as $key => $value) {
            if ($value === null) {
                unset($query[$key]);
            } else {
                $query[$key] = $value;
            }
        }

        if (($query['sort'] ?? null) === ProductSort::Default->value) {
            unset($query['sort']);
        }

        if (($query['per_page'] ?? null) === (int) config('catalog.per_page_default')) {
            unset($query['per_page']);
        }

        if (($query['page'] ?? 1) === 1) {
            unset($query['page']);
        }

        return $query;
    }
}
