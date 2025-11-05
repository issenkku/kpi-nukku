<?php

namespace Database\Factories;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndicatorFactory extends Factory
{
    protected $model = Indicator::class;

    public function definition()
    {
        return [
            'name'       => $this->faker->sentence(3),
            'code'       => strtoupper($this->faker->bothify('IND-###')),
            'max_score'  => $this->faker->numberBetween(50, 100),
            'score_acc'  => 0,
            'status'     => 1, // default active
            // Ensure required columns exist per migration
            'deadline'   => $this->faker->date(),
            // Required foreign key per migration (note: column name is 'categorie_id')
            'categorie_id' => function () {
                $standard = \App\Models\Standard::query()->firstOrCreate(['name' => 'Default']);
                return \App\Models\Category::query()->firstOrCreate([
                    'name' => 'General',
                    'standard_id' => $standard->id,
                ])->id;
            },
        ];
    }
}
