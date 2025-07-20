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
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UnpinPostController extends AbstractShowController
{
    public string $serializer = DiscussionSerializer::class;

    public function __construct(
        protected DiscussionRepository $discussions,
        protected ValidationFactory $validation
    ) {
    }

    protected function data(ServerRequestInterface $request, Document $document): Discussion
    {
        $actor = RequestUtil::getActor($request);
        $discussionId = (int) Arr::get($request->getQueryParams(), 'id');

        // Validate input
        $this->validation->make([
            'discussionId' => $discussionId,
        ], [
            'discussionId' => 'required|integer|min:1',
        ])->validate();

        $discussion = $this->discussions->findOrFail($discussionId, $actor);

        // Security checks
        $this->assertCanUnpinPost($actor, $discussion);

        // Unpin the post
        $discussion->pinned_post_id = null;
        $discussion->save();

        // Load relationships for serializer
        $discussion->load(['pinnedPost']);

        return $discussion;
    }

    private function assertCanUnpinPost($actor, Discussion $discussion): void
    {
        // Check basic permissions
        if (!$actor->can('pinPost', $discussion)) {
            throw new PermissionDeniedException();
        }

        // Check if there's actually a pinned post to unpin
        if ($discussion->pinned_post_id === null) {
            throw new \InvalidArgumentException('No post is currently pinned in this discussion.');
        }
    }
}