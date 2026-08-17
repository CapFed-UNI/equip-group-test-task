<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Product;
use Illuminate\Support\Collection;

class GroupTreeService
{
    /** @var Collection<int, Group>|null */
    private ?Collection $groups = null;

    /** @var array<int, int>|null */
    private ?array $productCounts = null;

    /**
     * @return Collection<int, Group>
     */
    public function allGroups(): Collection
    {
        return $this->groups ??= Group::query()->orderBy('id')->get()->keyBy('id');
    }

    /**
     * @return list<int>
     */
    public function descendantIdsIncludingSelf(int $groupId): array
    {
        $childrenMap = $this->childrenMap();
        $ids = [$groupId];
        $queue = [$groupId];

        while ($queue !== []) {
            $current = array_shift($queue);
            foreach ($childrenMap[$current] ?? [] as $childId) {
                $ids[] = $childId;
                $queue[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @return list<int>
     */
    public function ancestorIds(int $groupId): array
    {
        $groups = $this->allGroups();
        $ids = [];
        $current = $groups->get($groupId);

        while ($current && (int) $current->id_parent !== 0) {
            $ids[] = (int) $current->id_parent;
            $current = $groups->get($current->id_parent);
        }

        return $ids;
    }

    /**
     * Количество товаров в группе, включая все подгруппы.
     *
     * @return array<int, int>
     */
    public function productCounts(): array
    {
        if ($this->productCounts !== null) {
            return $this->productCounts;
        }

        $direct = Product::query()
            ->selectRaw('id_group, COUNT(*) as aggregate')
            ->groupBy('id_group')
            ->pluck('aggregate', 'id_group')
            ->map(fn ($count) => (int) $count)
            ->all();

        $childrenMap = $this->childrenMap();
        $memo = [];

        $walk = function (int $id) use (&$walk, &$memo, $direct, $childrenMap): int {
            if (array_key_exists($id, $memo)) {
                return $memo[$id];
            }

            $total = $direct[$id] ?? 0;

            foreach ($childrenMap[$id] ?? [] as $childId) {
                $total += $walk($childId);
            }

            return $memo[$id] = $total;
        };

        foreach ($this->allGroups() as $group) {
            $walk((int) $group->id);
        }

        return $this->productCounts = $memo;
    }

    public function productCount(int $groupId): int
    {
        return $this->productCounts()[$groupId] ?? 0;
    }

    /**
     * Дерево сайдбара: на главной только корни; в группе раскрыт путь до текущей.
     *
     * @return list<array{group: Group, count: int, active: bool, on_path: bool, children: array}>
     */
    public function sidebar(?int $activeGroupId): array
    {
        $path = $activeGroupId === null
            ? []
            : array_merge($this->ancestorIds($activeGroupId), [$activeGroupId]);

        return $this->buildNodes(0, $path, $activeGroupId);
    }

    /**
     * @return list<array{title: string, url: string|null}>
     */
    public function breadcrumbs(?Group $group): array
    {
        $crumbs = [
            ['title' => 'Главная', 'url' => route('catalog.index')],
        ];

        if ($group === null) {
            return $crumbs;
        }

        $chain = [];
        $groups = $this->allGroups();
        $current = $groups->get($group->id) ?? $group;

        while ($current) {
            array_unshift($chain, $current);
            $parentId = (int) $current->id_parent;
            $current = $parentId === 0 ? null : $groups->get($parentId);
        }

        foreach ($chain as $item) {
            $crumbs[] = [
                'title' => $item->name,
                'url' => route('catalog.group', $item),
            ];
        }

        return $crumbs;
    }

    /**
     * @return Collection<int, Group>
     */
    public function childrenOf(int $parentId): Collection
    {
        return $this->allGroups()->where('id_parent', $parentId)->values();
    }

    /**
     * @param  list<int>  $path
     * @return list<array{group: Group, count: int, active: bool, on_path: bool, children: array}>
     */
    private function buildNodes(int $parentId, array $path, ?int $activeGroupId): array
    {
        $nodes = [];

        foreach ($this->childrenOf($parentId) as $group) {
            $isActive = $activeGroupId !== null && (int) $group->id === $activeGroupId;
            $onPath = in_array((int) $group->id, $path, true);
            $expand = $onPath;

            $nodes[] = [
                'group' => $group,
                'count' => $this->productCount((int) $group->id),
                'active' => $isActive,
                'on_path' => $onPath && ! $isActive,
                'children' => $expand
                    ? $this->buildNodes((int) $group->id, $path, $activeGroupId)
                    : [],
            ];
        }

        return $nodes;
    }

    /**
     * @return array<int, list<int>>
     */
    private function childrenMap(): array
    {
        $map = [];

        foreach ($this->allGroups() as $group) {
            $map[(int) $group->id_parent][] = (int) $group->id;
        }

        return $map;
    }
}
