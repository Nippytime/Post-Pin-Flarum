<?php

/*
 * This file is part of nippytime/post-pin-flarum.
 *
 * Copyright (c) 2025 Nippytime.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Post\Post;
use Nippytime\PostPinFlarum\Api\Controller\PinPostController;
use Nippytime\PostPinFlarum\Api\Controller\UnpinPostController;
use Nippytime\PostPinFlarum\Listener\AddPinnedPostData;
use Nippytime\PostPinFlarum\Listener\SavePinnedPostToDatabase;

return [
    // Register frontend assets
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    // Register locales
    new Extend\Locales(__DIR__ . '/locale'),

    // Database migration
    (new Extend\Migration())
        ->add(__DIR__ . '/migrations/2025_01_19_000001_add_pinned_post_to_discussions.php'),

    // API routes
    (new Extend\Routes('api'))
        ->post('/discussions/{id}/pin-post', 'discussions.pinPost', PinPostController::class)
        ->delete('/discussions/{id}/unpin-post', 'discussions.unpinPost', UnpinPostController::class),

    // API serializer
    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('pinnedPostId', function (DiscussionSerializer $serializer, Discussion $discussion) {
            return $discussion->pinned_post_id;
        })
        ->hasOne('pinnedPost', PostSerializer::class, function (Discussion $discussion) {
            return $discussion->pinnedPost();
        }),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->attribute('isPinned', function (PostSerializer $serializer, Post $post) {
            $discussion = $post->discussion;
            return $discussion && $discussion->pinned_post_id === $post->id;
        }),

    // Model relationships
    (new Extend\Model(Discussion::class))
        ->belongsTo('pinnedPost', Post::class, 'pinned_post_id'),

    // Event listeners
    (new Extend\Event())
        ->listen(\Flarum\Api\Event\Serializing::class, AddPinnedPostData::class)
        ->listen(\Flarum\Discussion\Event\Saving::class, SavePinnedPostToDatabase::class),

    // Settings
    (new Extend\Settings())
        ->default('nippytime-post-pin.allow_self_pin', true)
        ->default('nippytime-post-pin.allow_any_post', true)
        ->serializeToForum('postPinSettings', [
            'nippytime-post-pin.allow_self_pin',
            'nippytime-post-pin.allow_any_post',
        ]),

    // Permissions
    (new Extend\Policy())
        ->globalPolicy(\Nippytime\PostPinFlarum\Policy\DiscussionPolicy::class),
];