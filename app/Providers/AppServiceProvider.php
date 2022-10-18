<?php

namespace App\Providers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Topic;
use App\Observers\LikeObserver;
use App\Observers\PostObserver;
use App\Observers\TopicObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Topic::observe(TopicObserver::class);
        Post::observe(PostObserver::class);
        Like::observe(LikeObserver::class);
    }
}
