<?php

use Illuminate\Database\Seeder;

class AddPermissionsToPermsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perms')->insert([
            'code'  => 'participation_create',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'participation_show',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'participation_admin',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'cms_menus',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'cms_news',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'cms_pages',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'cms_sites',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'users_analytics',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'users_groups',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'users_list',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'moderation_comments',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'moderation_participation',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'moderation_users',
            'cb'  => 0,
        ]);

        DB::table('perms')->insert([
            'code'  => 'communication_email',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'communication_intMessages',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'communication_sms',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'other_questionnaire',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'other_short_links',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'other_polls',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_entity',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_gamification',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_kiosk',
            'cb'  => 0,
        ]);
        DB::table('perms')->insert([
            'code'  => 'conf_open_data',
            'cb'  => 0,
        ]);
    }
}
