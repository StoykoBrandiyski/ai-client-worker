<?php

namespace App\Providers;

use App\Repositories\Contracts\EngineModelRepositoryInterface;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\ProcessLogRepositoryInterface;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\EngineModelRepository;
use App\Repositories\EngineRepository;
use App\Repositories\GroupRepository;
use App\Repositories\ProcessLogRepository;
use App\Repositories\ProcessRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(EngineRepositoryInterface::class, EngineRepository::class);
        $this->app->bind(EngineModelRepositoryInterface::class, EngineModelRepository::class);
        $this->app->bind(ProcessRepositoryInterface::class, ProcessRepository::class);
        $this->app->bind(ProcessLogRepositoryInterface::class, ProcessLogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
