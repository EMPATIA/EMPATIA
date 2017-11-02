<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE PROCEDURE count_comments( IN new_topic_id INT(10) ) 
                BEGIN  
                    DECLARE post_count INT;
                    
                    SELECT COUNT(*)-1
                    AS total
                    FROM posts
                    WHERE 
                        `topic_id` = new_topic_id AND
                        `enabled` = 1 AND
                        `active` = 1 AND
                        `deleted_at` IS NULL 
                    INTO @count;
                    
                    IF @count > 0 THEN SET post_count = @count;
                    ELSE SET post_count = 0;
                    END IF;
                    
                    UPDATE topics
                    SET _count_comments = post_count
                    WHERE topics.id = new_topic_id;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS count_comments');
    }
}
