<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mss\Models\Article;

class SetArticleNumbersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articlenumbers:set {--database=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set new Article Numbers for all articles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force') || $this->confirm('This will reset all article numbers - are you sure?')) {

            $databaseConnection = $this->option('database') ?? DB::getDefaultConnection();
            DB::setDefaultConnection($databaseConnection);

            $bar = $this->output->createProgressBar(Article::count());
            Article::query()->update(['internal_article_number' => null]);
            Article::all()->each(function ($article) use ($bar) {
                /* @var $article \Mss\Models\Article */
                $article->setNewArticleNumber();
                $bar->advance();
            });
            $bar->finish();
            $this->info(' done');
        }
    }
}
