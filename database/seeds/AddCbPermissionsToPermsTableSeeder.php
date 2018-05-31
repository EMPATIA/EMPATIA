<?php

use Illuminate\Database\Seeder;

class AddCbPermissionsToPermsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perms')->insert([
            'code'  => 'participation_details',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'participation_list',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'participation_comments',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'participation_analytics',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_process',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_parameters',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_events',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_notifications',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_flags',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'security_login_levels',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'security_permissions',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_moderators',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_empaville',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_dataMigration',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_quest',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_TA',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_translations',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'advanced_schedules',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_details',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_edit',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_ta',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_create_with_user',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_review',
            'cb'  => 1,
        ]);
        DB::table('perms')->insert([
            'code'  => 'topic_create',
            'cb'  => 1,
        ]);
    }
}
