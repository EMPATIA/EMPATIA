<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE PROCEDURE cb_statistics( IN new_cb_id INT(10) ) 
            BEGIN
                DECLARE total_events INT DEFAULT 0;
                DECLARE total_topics INT DEFAULT 0;
                DECLARE total_posts INT DEFAULT 0;
                
                SET total_events = (
                        SELECT COUNT(*)
                        FROM cb_votes cv
                        WHERE 
                            cv.cb_id = new_cb_id AND
                            deleted_at IS NULL
                    );

                SET total_topics = (
                        SELECT COUNT(*)
                        FROM topics t
                        WHERE 
                            t.cb_id = new_cb_id AND
                            deleted_at IS NULL
                    );

                SET total_posts = (
                        SELECT COALESCE(SUM(_count_comments),0)
                        FROM topics t
                        WHERE 
                            t.cb_id = new_cb_id AND
                            deleted_at IS NULL
                    );
                
                SET @json = CONCAT(\'{"counts": {"topics":\',total_topics,\',"posts":\',total_posts,\',"vote_events":\',total_events,\'}}\');
                                
                UPDATE cbs 
                SET _statistics = @json 
                WHERE id = new_cb_id;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS cb_statistics');
    }
}
