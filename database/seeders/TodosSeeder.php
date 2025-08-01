<?php

namespace Database\Seeders;

use App\Models\Todo;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TodosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 500; ++$i) {
            $todo = new Todo();
            $todo->user_id = 1;
            $todo->titolo = $faker->sentence(3);
            $todo->descrizione = $faker->paragraph();
            $todo->data_inserimento = $faker->dateTimeBetween('-1 month', '-1 day');
            $todo->data_scadenza = $faker->dateTimeBetween('now', '+3 month');
            $todo->save();
        }
    }
}
