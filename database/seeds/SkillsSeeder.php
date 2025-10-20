<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SkillsSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            // Nature Conservation Skills
            ['Wildlife Monitoring', 'Tracking and observing wildlife populations.'],
            ['Ecological Research', 'Studying ecosystems and biodiversity.'],
            ['Habitat Restoration', 'Rehabilitating natural environments.'],
            ['Invasive Species Management', 'Controlling non-native species.'],
            ['Biodiversity Assessment', 'Evaluating the variety of life in ecosystems.'],
            ['Field Data Collection', 'Gathering environmental data in the field.'],
            ['Species Identification', 'Recognizing and classifying species.'],
            ['GIS Mapping', 'Using Geographic Information Systems for mapping.'],
            ['Environmental Education', 'Teaching environmental awareness.'],
            ['Sustainable Resource Management', 'Managing resources responsibly.'],
            ['Anti-poaching Operations', 'Preventing illegal wildlife hunting.'],
            ['Wildlife Rehabilitation', 'Caring for and releasing injured wildlife.'],
            ['Conservation Policy Advocacy', 'Promoting conservation-friendly policies.'],
            ['Environmental Impact Assessment', 'Evaluating projects’ effects on nature.'],

            // General Company Skills
            ['Communication', 'Effectively conveying ideas and information.'],
            ['Teamwork', 'Collaborating with others to achieve goals.'],
            ['Problem Solving', 'Finding solutions to challenges.'],
            ['Leadership', 'Guiding and motivating a team.'],
            ['Time Management', 'Prioritizing tasks efficiently.'],
            ['Adaptability', 'Adjusting to change and new situations.'],
            ['Critical Thinking', 'Analyzing situations logically.'],
            ['Project Management', 'Planning and executing projects.'],
            ['Technical Writing', 'Documenting technical information clearly.'],
            ['Conflict Resolution', 'Mediating and resolving disagreements.'],
            ['Customer Service', 'Assisting and supporting clients.'],
            ['Data Analysis', 'Interpreting data to inform decisions.'],
            ['Report Writing', 'Creating formal written reports.'],
            ['Basic Computer Skills', 'Using standard software and tools.'],
        ];

        foreach ($skills as [$name, $description]) {
            DB::table('skills')->updateOrInsert(
                ['slug' => Str::slug($name)], // Unique condition
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'updated_at' => now(),
                    'created_at' => now(), // Optional: update `created_at` only if new
                ]
            );
        }
    }
}