<?php

namespace App\Enums;

enum ProductSort: string
{
    case Default = 'id';
    case PriceAsc = 'price_asc';
    case PriceDesc = 'price_desc';
    case NameAsc = 'name_asc';
    case NameDesc = 'name_desc';

    public static function fromQuery(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::Default;
    }

    public function label(): string
    {
        return match ($this) {
            self::PriceAsc => 'По цене ↑',
            self::PriceDesc => 'По цене ↓',
            self::NameAsc => 'По названию ↑',
            self::NameDesc => 'По названию ↓',
            self::Default => 'По умолчанию',
        };
    }

    /**
     * @return list<self>
     */
    public static function publicOptions(): array
    {
        return [
            self::PriceAsc,
            self::PriceDesc,
            self::NameAsc,
            self::NameDesc,
        ];
    }
}
