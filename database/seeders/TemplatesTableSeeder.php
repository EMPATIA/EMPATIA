<?php

namespace Database\Seeders;

use App\Models\Backend\Notifications\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TemplatesTableSeeder extends Seeder
{
    protected array $templates = [
        [
            'code'        => 'account_created',
            'channel'     => 'email',
            'subject'     => [
                'pt' => 'Criação de conta',
                'en' => 'Account creation'
            ],
            'content'     => [
                'pt' => 'Caro(a) #name#, Obrigado pelo seu registo.',
                'en' => 'Dear #name#, Thank you for your registration.'
            ]
        ],
        [
            'code'        => 'set_password',
            'channel'     => 'email',
            'subject'     => [
                'pt' => 'Definir palavra-passe',
                'en' => 'Set password'
            ],
            'content'     => [
                'pt' => 'Caro(a) #name#, Para definir a sua palavra-passe acesse o link: #password_set_link#.',
                'en' => 'Dear #name#, To set your password access this link: #password_set_link#'
            ]
        ],
        [
            'code'        => 'reset_password',
            'channel'     => 'email',
            'subject'     => [
                'pt' => 'Redefinir palavra-passe',
                'en' => 'Reset password'
            ],
            'content'     => [
                'pt' => 'Caro(a) #name#, Para redefinir a sua palavra-passe acesse o link: #password_reset_link#.',
                'en' => 'Dear #name#, To reset your password access this link: #password_reset_link#.'
            ]
        ],
        [
            'code'        => 'votes_submitted',
            'channel'     => 'email',
            'subject'     => [
                'pt' => 'Votos submetidos',
                'en' => 'Votes submitted'
            ],
            'content'     => [
                'pt' => 'Caro(a) #name#, Os seus votos foram submetidos com sucesso.',
                'en' => 'Dear #name#, Your votes were successfully submitted.'
            ]
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            if (Schema::hasTable('templates')) {
                Model::unguard();
                
                $command = $command ?: $this->command;
                if ($options['clear'] ?? []) {
                    if ($command->confirm('Are you sure that you want to delete all templates\'s table data?', false)) {
                        $command->info("Deleting templates table data...");
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::table('templates')->truncate();
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    }
                }
                
                $command->info("Seeding templates table...");
                foreach ($this->templates as $template) {
                    Template::create([
                        'code' => $template['code'],
                        'channel' => $template['channel'],
                        'subject' => $template['subject'],
                        'content' => $template['content'],
                        'version' => 1,
                        'versions' => [] //TODO: Trait Versionable
                    ]);
                }
                $command->comment("Templates table seeding completed successfully!");
            } else {
                $command->error("There isn't any templates table");
                return null;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding templates table!");
            logError('Templates seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }
    }
}
