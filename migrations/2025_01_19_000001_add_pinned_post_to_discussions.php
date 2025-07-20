<?php

/*
 * This file is part of nippytime/post-pin-flarum.
 *
 * Copyright (c) 2025 Nippytime.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->unsignedInteger('pinned_post_id')->nullable()->index();
            
            $table->foreign('pinned_post_id')
                  ->references('id')
                  ->on('posts')
                  ->onDelete('set null');
        });
    },
    
    'down' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->dropForeign(['pinned_post_id']);
            $table->dropColumn('pinned_post_id');
        });
    },
];