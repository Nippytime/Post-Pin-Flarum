import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import PostControls from 'flarum/forum/utils/PostControls';
import Button from 'flarum/common/components/Button';
import ItemList from 'flarum/common/utils/ItemList';
import PostStreamState from 'flarum/forum/states/PostStreamState';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

app.initializers.add('nippytime/post-pin-flarum', () => {
  // Extend PostControls to add pin/unpin actions
  extend(PostControls, 'moderationControls', function (items, post) {
    const discussion = post.discussion();
    
    if (!discussion || !app.session.user) {
      return;
    }

    // Check if user can pin posts in this discussion
    if (app.session.user.can('pinPost', discussion)) {
      const isPinned = discussion.pinnedPostId() === post.id();
      
      if (isPinned) {
        // Add unpin option
        items.add('unpinPost', 
          Button.component({
            icon: 'fas fa-thumbtack',
            onclick: () => this.unpinPost(post),
            className: 'Button Button--link'
          }, app.translator.trans('nippytime-post-pin.forum.post_controls.unpin_button'))
        );
      } else {
        // Add pin option
        items.add('pinPost', 
          Button.component({
            icon: 'far fa-thumbtack',
            onclick: () => this.pinPost(post),
            className: 'Button Button--link'
          }, app.translator.trans('nippytime-post-pin.forum.post_controls.pin_button'))
        );
      }
    }
  });

  // Add pin/unpin methods to PostControls
  extend(PostControls, 'pinPost', function (post) {
    const discussion = post.discussion();
    
    if (confirm(app.translator.trans('nippytime-post-pin.forum.post_controls.pin_confirmation'))) {
      return app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/discussions/' + discussion.id() + '/pin-post',
        body: { postId: post.id() },
        errorHandler: this.onerror.bind(this)
      }).then(() => {
        // Update the discussion model
        discussion.pushData({
          type: 'discussions',
          id: discussion.id(),
          attributes: {
            pinnedPostId: post.id()
          },
          relationships: {
            pinnedPost: {
              data: { type: 'posts', id: post.id() }
            }
          }
        });
        
        // Force re-render of the discussion page
        if (app.current.matches(DiscussionPage)) {
          m.redraw();
        }
      });
    }
  });

  extend(PostControls, 'unpinPost', function (post) {
    const discussion = post.discussion();
    
    if (confirm(app.translator.trans('nippytime-post-pin.forum.post_controls.unpin_confirmation'))) {
      return app.request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/discussions/' + discussion.id() + '/unpin-post',
        errorHandler: this.onerror.bind(this)
      }).then(() => {
        // Update the discussion model
        discussion.pushData({
          type: 'discussions',
          id: discussion.id(),
          attributes: {
            pinnedPostId: null
          },
          relationships: {
            pinnedPost: {
              data: null
            }
          }
        });
        
        // Force re-render of the discussion page
        if (app.current.matches(DiscussionPage)) {
          m.redraw();
        }
      });
    }
  });

  // Add visual indicator for pinned posts
  extend(CommentPost.prototype, 'elementAttrs', function (attrs) {
    const discussion = this.attrs.post.discussion();
    const isPinned = discussion && discussion.pinnedPostId() === this.attrs.post.id();
    
    if (isPinned) {
      attrs.className = (attrs.className || '') + ' Post--pinned';
    }
  });

  // Add pinned post indicator to post header
  extend(CommentPost.prototype, 'headerItems', function (items) {
    const discussion = this.attrs.post.discussion();
    const isPinned = discussion && discussion.pinnedPostId() === this.attrs.post.id();
    
    if (isPinned) {
      items.add('pinnedIndicator', 
        m('span.PinnedPost-indicator', [
          m('i.fas.fa-thumbtack'),
          ' ',
          app.translator.trans('nippytime-post-pin.forum.post.pinned_indicator')
        ]), 100
      );
    }
  });

  // Modify post stream to show pinned post first
  extend(PostStreamState.prototype, 'load', function (promise, range) {
    return promise.then((result) => {
      const discussion = this.discussion;
      const pinnedPostId = discussion.pinnedPostId();
      
      if (pinnedPostId && range && range[0] === 1) {
        // We're loading the first page, ensure pinned post is first
        const posts = app.store.all('posts')
          .filter(post => post.discussion() === discussion)
          .sort((a, b) => {
            // Pinned post always comes first
            if (a.id() === pinnedPostId) return -1;
            if (b.id() === pinnedPostId) return 1;
            // Then sort by number (position in thread)
            return a.number() - b.number();
          });
          
        // Update the visible posts array
        this.posts = posts.slice(0, this.posts.length);
      }
      
      return result;
    });
  });

  // Override post stream sorting to prioritize pinned posts
  extend(PostStreamState.prototype, 'posts', function (posts) {
    const discussion = this.discussion;
    const pinnedPostId = discussion.pinnedPostId();
    
    if (pinnedPostId && posts.length > 0) {
      // Find the pinned post
      const pinnedPost = posts.find(post => post.id() === pinnedPostId);
      
      if (pinnedPost) {
        // Remove pinned post from its current position
        const filteredPosts = posts.filter(post => post.id() !== pinnedPostId);
        // Add it to the beginning
        return [pinnedPost, ...filteredPosts];
      }
    }
    
    return posts;
  });
});