<?php

namespace App\Traits;

trait WithBlamestamps
{
    /**
     * Initialize the trait for an instance.
     *
     * @return void
     */
    public function initializeWithBlamestamps(): void
    {
        $this->schema = array_merge($this->schema ?? [], [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ]);
    }
}
