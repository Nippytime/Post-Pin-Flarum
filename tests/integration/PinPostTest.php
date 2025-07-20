<?php

/*
 * This file is part of nippytime/post-pin-flarum.
 *
 * Copyright (c) 2025 Nippytime.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nippytime\PostPinFlarum\Tests\Integration;

use Flarum\Discussion\Discussion;
use Flarum\Group\Group;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

class PinPostTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('nippytime-post-pin-flarum');

        $this->prepareDatabase([
            'discussions' => [
                ['id' => 1, 'title' => 'Test Discussion', 'created_at' => '2023-01-01 00:00:00', 'user_id' => 1, 'first_post_id' => 1, 'comment_count' => 2, 'pinned_post_id' => null],
            ],
            'posts' => [
                ['id' => 1, 'discussion_id' => 1, 'created_at' => '2023-01-01 00:00:00', 'user_id' => 1, 'type' => 'comment', 'content' => '<t><p>Original post</p></t>'],
                ['id' => 2, 'discussion_id' => 1, 'created_at' => '2023-01-01 01:00:00', 'user_id' => 2, 'type' => 'comment', 'content' => '<t><p>Reply post</p></t>'],
            ],
            'users' => [
                $this->normalUser(),
            ],
            'groups' => [
                $this->memberGroup(),
                $this->moderatorGroup(),
            ],
            'group_user' => [
                ['user_id' => 2, 'group_id' => Group::MEMBER_ID],
            ],
        ]);
    }

    /** @test */
    public function admin_can_pin_any_post(): void
    {
        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'authenticatedAs' => 1,
                'json' => [
                    'postId' => 2,
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $discussion = Discussion::find(1);
        $this->assertEquals(2, $discussion->pinned_post_id);
    }

    /** @test */
    public function user_can_pin_own_post_when_allowed(): void
    {
        $this->setting('nippytime-post-pin.allow_self_pin', true);

        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'authenticatedAs' => 2,
                'json' => [
                    'postId' => 2, // User 2's own post
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $discussion = Discussion::find(1);
        $this->assertEquals(2, $discussion->pinned_post_id);
    }

    /** @test */
    public function user_cannot_pin_others_post_without_permission(): void
    {
        $this->setting('nippytime-post-pin.allow_self_pin', true);
        $this->setting('nippytime-post-pin.allow_any_post', false);

        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'authenticatedAs' => 2,
                'json' => [
                    'postId' => 1, // User 1's post
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function admin_can_unpin_post(): void
    {
        // First pin a post
        $discussion = Discussion::find(1);
        $discussion->pinned_post_id = 2;
        $discussion->save();

        $response = $this->send(
            $this->request('DELETE', '/api/discussions/1/unpin-post', [
                'authenticatedAs' => 1,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $discussion->refresh();
        $this->assertNull($discussion->pinned_post_id);
    }

    /** @test */
    public function cannot_pin_nonexistent_post(): void
    {
        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'authenticatedAs' => 1,
                'json' => [
                    'postId' => 999, // Non-existent post
                ],
            ])
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function cannot_pin_post_from_different_discussion(): void
    {
        // Create another discussion with a post
        $this->prepareDatabase([
            'discussions' => [
                ['id' => 2, 'title' => 'Other Discussion', 'created_at' => '2023-01-01 00:00:00', 'user_id' => 1, 'first_post_id' => 3, 'comment_count' => 1],
            ],
            'posts' => [
                ['id' => 3, 'discussion_id' => 2, 'created_at' => '2023-01-01 00:00:00', 'user_id' => 1, 'type' => 'comment', 'content' => '<t><p>Other post</p></t>'],
            ],
        ]);

        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'authenticatedAs' => 1,
                'json' => [
                    'postId' => 3, // Post from discussion 2
                ],
            ])
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    /** @test */
    public function guest_cannot_pin_posts(): void
    {
        $response = $this->send(
            $this->request('POST', '/api/discussions/1/pin-post', [
                'json' => [
                    'postId' => 2,
                ],
            ])
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function serializer_includes_pinned_post_data(): void
    {
        // Pin a post
        $discussion = Discussion::find(1);
        $discussion->pinned_post_id = 2;
        $discussion->save();

        $response = $this->send(
            $this->request('GET', '/api/discussions/1', [
                'authenticatedAs' => 1,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals(2, $data['data']['attributes']['pinnedPostId']);
        $this->assertArrayHasKey('pinnedPost', $data['data']['relationships']);
    }
}