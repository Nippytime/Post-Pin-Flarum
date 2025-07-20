<?php

/*
 * This file is part of nippytime/post-pin-flarum.
 *
 * Copyright (c) 2025 Nippytime.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Nippytime\PostPinFlarum\Listener;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;

class AddPinnedPostData
{
    public function handle(Serializing $event): void
    {
        if ($event->isSerializer(DiscussionSerializer::class)) {
            /** @var Discussion $discussion */
            $discussion = $event->model;

            // Ensure we load the pinned post relationship if it's not already loaded
            if ($discussion->pinned_post_id && !$discussion->relationLoaded('pinnedPost')) {
                $discussion->load('pinnedPost');
            }
        }
    }
}