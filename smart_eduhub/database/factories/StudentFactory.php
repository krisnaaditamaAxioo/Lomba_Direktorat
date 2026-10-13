<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'id_siswa'       => 'SIS' . fake()->unique()->numerify('####'),
            'username'       => fake()->unique()->userName(),
            'pin'            => '123456',
            'status'         => 0,           // 0 = aktif
            'nama_lengkap'   => fake()->name(),
            'angkatan_id'    => null,
            'foto'           => null,
            'nisn'           => fake()->unique()->numerify('##########'),
            'no_nik'         => fake()->unique()->numerify('################'),
            'gender'         => fake()->randomElement([0, 1]),
            'mother_name'    => fake()->name('female'),
            'religion'       => 'Islam',
            'height'         => fake()->numberBetween(150, 180),
            'weight'         => fake()->numberBetween(45, 75),
            'place_of_birth' => fake()->city(),
            'dateofbirth'    => fake()->date('Y-m-d', '-16 years'),
            'address'        => fake()->address(),
            'nis_siswa'      => fake()->unique()->numerify('####'),
            'role'           => 'student',
            'email'          => fake()->unique()->safeEmail(),
            'phone'          => fake()->phoneNumber(),
            'parent_phone'   => fake()->phoneNumber(),
        ];
    }

    /** Siswa nonaktif */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 1]);
    }

    /** Siswa dengan PIN tertentu */
    public function withPin(string $pin): static
    {
        return $this->state(fn (array $attributes) => ['pin' => $pin]);
    }
}
