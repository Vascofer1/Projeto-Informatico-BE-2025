<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('questions')->insert([
            [
                'name' => 'Gender',
                'description' => 'Indicate your gender.',
                'options' => json_encode(['Male', 'Female']),
                'category' => null,
            ],
            [
                'name' => 'Age',
                'description' => 'Indicate your age.',
                'options' => json_encode(['1-18', '19-30', '31-50', '51+']),
                'category' => null,
            ],
            [
                'name' => 'Country',
                'description' => 'Indicate the country you come from.',
                'options' => json_encode(['Portugal', 'Spain', 'France', 'Brazil', 'England', 'Other']),
                'category' => null,
            ],
            [
                'name' => 'Region',
                'description' => 'Indicate the region of Portugal you come from.',
                'options' => json_encode(['North', 'South', 'Center', 'Other']),
                'category' => null,
            ],
            [
                'name' => 'Sports Frequency',
                'description' => 'Indicate how many times a week you practice sports.',
                'options' => json_encode(['0', '1-2', '3-5', '6+']),
                'category' => 'Sports',
            ],
            [
                'name' => 'Health Problems',
                'description' => 'Indicate if you have any health problems.',
                'options' => json_encode(['Yes', 'No']),
                'category' => 'Health',
            ],
            [
                'name' => 'Fast Food',
                'description' => 'Indicate how often a week you eat fast food.',
                'options' => json_encode(['Never', '1-2 times a week', '3-5 times a week', 'Every day']),
                'category' => 'Health',
            ],
            [
                'name' => 'Nutrition',
                'description' => 'Indicate how often you eat fruits and vegetables.',
                'options' => json_encode(['Never', '1-2 times a week', '3-5 times a week', 'Every day']),
                'category' => 'Health',
            ],
            [
                'name' => 'Emerging Technologies',
                'description' => 'Indicate how familiar you are with emerging technologies such as AI or Blockchain.',
                'options' => json_encode(['Not at all', 'A little', 'Somewhat', 'Very', 'Extremely']),
                'category' => 'Technology',
            ],
            [
                'name' => 'Social Media',
                'description' => 'Indicate how often you use social media.',
                'options' => json_encode(['Never', '1-2 times a week', '3-5 times a week', 'Every day']),
                'category' => 'Technology',
            ],
        ]);
    }
}
