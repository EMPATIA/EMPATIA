<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER count_comments_insert AFTER INSERT ON `posts` FOR EACH ROW
            BEGIN
                DECLARE new_cb_id INT DEFAULT 0;
            
                CALL count_comments(NEW.topic_id);
            
                SET new_cb_id = (SELECT cb_id
                                FROM topics t
                                WHERE t.id = NEW.topic_id);
                
                CALL cb_statistics(new_cb_id);
            END
        ');

        DB::unprepared('
        CREATE TRIGGER count_comments_update AFTER UPDATE ON `posts` FOR EACH ROW
            BEGIN
                DECLARE new_cb_id INT DEFAULT 0;
            
                CALL count_comments(NEW.topic_id);
            
                SET new_cb_id = (SELECT cb_id
                                FROM topics t
                                WHERE t.id = NEW.topic_id);
                
                CALL cb_statistics(new_cb_id);
            END
        ');

        DB::unprepared('
        CREATE TRIGGER topics_cb_statistics_insert AFTER INSERT ON `topics` FOR EACH ROW
            BEGIN    
                CALL cb_statistics(NEW.cb_id);
            END
        ');

        DB::unprepared('
        CREATE TRIGGER topics_cb_statistics_update AFTER UPDATE ON `topics` FOR EACH ROW
            BEGIN    
                CALL cb_statistics(NEW.cb_id);
            END
        ');

        DB::unprepared('
        CREATE TRIGGER vote_events_cb_statistics_insert AFTER INSERT ON `cb_votes` FOR EACH ROW
            BEGIN    
                CALL cb_statistics(NEW.cb_id);
            END
        ');

        DB::unprepared('
        CREATE TRIGGER vote_events_cb_statistics_update AFTER UPDATE ON `cb_votes` FOR EACH ROW
            BEGIN    
                CALL cb_statistics(NEW.cb_id);
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `count_comments_insert`');
        DB::unprepared('DROP TRIGGER `count_comments_update`');
        DB::unprepared('DROP TRIGGER `topics_cb_statistics_insert`');
        DB::unprepared('DROP TRIGGER `topics_cb_statistics_update`');
        DB::unprepared('DROP TRIGGER `vote_events_cb_statistics_insert`');
        DB::unprepared('DROP TRIGGER `vote_events_cb_statistics_update`');

    }
}
