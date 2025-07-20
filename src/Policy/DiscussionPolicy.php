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

namespace Nippytime\PostPinFlarum\Policy;

use Flarum\Discussion\Discussion;
use Flarum\User\User;

class DiscussionPolicy
{
    /**
     * Determine if the user can pin posts in a discussion.
     */
    public function pinPost(User $actor, Discussion $discussion): bool
    {
        // Must be able to see the discussion
        if (!$discussion->isVisibleTo($actor)) {
            return false;
        }

        // Guests cannot pin posts
        if ($actor->isGuest()) {
            return false;
        }

        // Discussion must not be locked (unless user can moderate)
        if ($discussion->is_locked && !$actor->can('moderate', $discussion)) {
            return false;
        }

        // Check if user can moderate discussions (moderators/admins can always pin)
        if ($actor->can('moderate', $discussion)) {
            return true;
        }

        // Check if user is the discussion starter
        if ($discussion->user_id === $actor->id) {
            return true;
        }

        // Check if user can reply to the discussion (basic participation requirement)
        if ($actor->can('reply', $discussion)) {
            return true;
        }

        return false;
    }
}