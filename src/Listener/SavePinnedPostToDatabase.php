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

use Flarum\Discussion\Event\Saving;
use Flarum\Post\PostRepository;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;

class SavePinnedPostToDatabase
{
    public function __construct(
        protected PostRepository $posts
    ) {
    }

    public function handle(Saving $event): void
    {
        $discussion = $event->discussion;
        $data = $event->data;
        $actor = $event->actor;

        // Check if pinned post data is being updated
        if (Arr::has($data, 'attributes.pinnedPostId')) {
            $pinnedPostId = Arr::get($data, 'attributes.pinnedPostId');

            // Validate permission to pin posts
            if (!$actor->can('pinPost', $discussion)) {
                throw new PermissionDeniedException();
            }

            // Handle unpinning (null value)
            if ($pinnedPostId === null) {
                $discussion->pinned_post_id = null;
                return;
            }

            // Validate the post exists and belongs to this discussion
            $post = $this->posts->findOrFail($pinnedPostId, $actor);
            
            if ($post->discussion_id !== $discussion->id) {
                throw new \InvalidArgumentException('Post does not belong to this discussion.');
            }

            // Additional security checks
            if (!$post->isVisibleTo($actor) || $post->is_private || $post->hidden_at !== null) {
                throw new PermissionDeniedException('Cannot pin this post.');
            }

            $discussion->pinned_post_id = $pinnedPostId;
        }
    }
}