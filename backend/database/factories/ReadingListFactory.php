<?php

namespace Database\Factories;

use App\Models\ReadingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingListFactory extends Factory
{
    protected $model = ReadingList::class;

    public function definition(): array
    {
        $listNames = ['Đang đọc', 'Muốn đọc', 'Đã đọc', 'Yêu thích', 'Khoa học viễn tưởng'];
        $visibility = ['public', 'private'];

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->randomElement($listNames) . ' - ' . $this->faker->unique()->word(),
            'description' => $this->faker->boolean(70) ? $this->faker->sentence(mt_rand(5, 15)) : null,
            'visibility' => $this->faker->randomElement($visibility),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}