<?php

namespace databaseExporter;

use Illuminate\Console\Command;

class databaseExportersCommand extends Command
{
    private $answers= [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'databaseExport {migration_name*} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * Specify a question that should be asked when the command runs.
     *
     * @param  string  $question
     * @param  string|array  $answer
     * @return $this
     */
    public function expectsQuestion($question, $answer)
    {
        $this->test->expectedQuestions[] = [$question, $answer];

        return $this;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        $migration_name = $this->argument('migration_name');
        $controller = new databaseExporterController($migration_name);
        $controller->export();
    }
    public function reversionQuestion(){
        return $this->ask('What is your password?');


    }
}
