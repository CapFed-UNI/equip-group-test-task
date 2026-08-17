<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'id_parent' => 0,
            'name' => fake()->unique()->words(2, true),
        ];
    }

    public function childOf(Group $parent): self
    {
        return $this->state(fn () => [
            'id_parent' => $parent->id,
        ]);
    }
}
