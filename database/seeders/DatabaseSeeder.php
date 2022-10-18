<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {   

        Section::insert([
            ['subject' => 'politics'],
            ['subject' => 'media'],
            ['subject' => 'news'],
            ['subject' => 'sports']
        ]);
    }
}
