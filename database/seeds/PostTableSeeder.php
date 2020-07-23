<?php

use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $post = new \App\Post([
            'title' => 'Learning Laravel',
            'content' => 'This blog will get you right in the track with laravel'
        ]);
        $post->save();

        $post = new \App\Post([
            'title' => 'Learning Javascript',
            'content' => 'Some Other content'
        ]);
        $post->save();
    }
}
