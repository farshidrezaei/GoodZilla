<?php

use App\Article;
use App\Comment;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Factory(App\Article::class, 20)
            ->create([])
            ->each(function (\App\Article $article) {
                $article
                    ->comments()
                    ->saveMany(factory(Comment::class, 5)
                        ->make([
                            'commentable_id' => $article->id,
                            'commentable_type' => Article::class
                        ]));
            });
    }
}
