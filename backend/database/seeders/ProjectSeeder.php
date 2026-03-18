<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $owner = $users->first();

        $projects = [
            ['name' => 'E-Commerce Platform', 'color' => '#6366f1', 'description' => 'Full-stack e-commerce solution with payment integration.'],
            ['name' => 'Mobile App Redesign', 'color' => '#ec4899', 'description' => 'Complete UI/UX overhaul of the mobile application.'],
            ['name' => 'API Gateway Migration', 'color' => '#f59e0b', 'description' => 'Migrating legacy REST API to GraphQL with microservices.'],
            ['name' => 'Analytics Dashboard', 'color' => '#10b981', 'description' => 'Real-time analytics and reporting dashboard.'],
        ];

        $statuses = ['todo', 'in_progress', 'in_review', 'done'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($projects as $projectData) {
            $project = Project::create([
                ...$projectData,
                'owner_id' => $owner->id,
                'deadline' => now()->addMonths(rand(1, 6)),
            ]);

            // Attach members
            foreach ($users->skip(1)->take(rand(1, 3)) as $member) {
                $project->members()->attach($member->id, ['role' => 'member']);
            }

            // Create tasks
            $taskTitles = [
                'Set up project repository',
                'Design database schema',
                'Implement authentication',
                'Build REST API endpoints',
                'Create frontend components',
                'Write unit tests',
                'Configure CI/CD pipeline',
                'Deploy to staging',
                'Performance optimization',
                'Code review and QA',
            ];

            foreach ($taskTitles as $i => $title) {
                Task::create([
                    'project_id'  => $project->id,
                    'title'       => $title,
                    'description' => "Detailed description for: {$title}",
                    'status'      => $statuses[$i % count($statuses)],
                    'priority'    => $priorities[rand(0, 3)],
                    'assignee_id' => $users->random()->id,
                    'reporter_id' => $owner->id,
                    'position'    => $i + 1,
                    'due_date'    => now()->addDays(rand(1, 30)),
                ]);
            }
        }
    }
}
