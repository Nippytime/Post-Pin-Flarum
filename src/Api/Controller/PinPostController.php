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

namespace Nippytime\PostPinFlarum\Api\Controller;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\DiscussionRepository;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class PinPostController extends AbstractShowController
{
    public string $serializer = DiscussionSerializer::class;

    public function __construct(
        protected DiscussionRepository $discussions,
        protected PostRepository $posts,
        protected ValidationFactory $validation,
        protected SettingsRepositoryInterface $settings
    ) {
    }

    protected function data(ServerRequestInterface $request, Document $document): Discussion
    {
        $actor = RequestUtil::getActor($request);
        $discussionId = (int) Arr::get($request->getQueryParams(), 'id');
        $postId = (int) Arr::get($request->getParsedBody(), 'postId');

        // Validate input
        $this->validation->make([
            'discussionId' => $discussionId,
            'postId' => $postId,
        ], [
            'discussionId' => 'required|integer|min:1',
            'postId' => 'required|integer|min:1',
        ])->validate();

        $discussion = $this->discussions->findOrFail($discussionId, $actor);
        $post = $this->posts->findOrFail($postId, $actor);

        // Security checks
        $this->assertCanPinPost($actor, $discussion, $post);

        // Validate post belongs to discussion
        if ($post->discussion_id !== $discussion->id) {
            throw new \InvalidArgumentException('Post does not belong to this discussion.');
        }

        // Pin the post
        $discussion->pinned_post_id = $post->id;
        $discussion->save();

        // Load relationships for serializer
        $discussion->load(['pinnedPost']);

        return $discussion;
    }

    private function assertCanPinPost($actor, Discussion $discussion, Post $post): void
    {
        // Check basic permissions
        if (!$actor->can('pinPost', $discussion)) {
            throw new PermissionDeniedException();
        }

        // Check if user can pin their own posts only
        $allowSelfPin = (bool) $this->settings->get('nippytime-post-pin.allow_self_pin', true);
        $allowAnyPost = (bool) $this->settings->get('nippytime-post-pin.allow_any_post', true);

        if (!$allowAnyPost && !$actor->can('moderate', $discussion)) {
            if (!$allowSelfPin || $post->user_id !== $actor->id) {
                throw new PermissionDeniedException('You can only pin your own posts.');
            }
        }

        // Ensure the post is visible to the actor
        if (!$post->isVisibleTo($actor)) {
            throw new PermissionDeniedException('You cannot pin a post that is not visible to you.');
        }

        // Ensure the post is not deleted or hidden
        if ($post->is_private || $post->hidden_at !== null) {
            throw new PermissionDeniedException('You cannot pin a hidden or private post.');
        }
    }
}