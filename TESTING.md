# Post Pin Extension Testing Checklist

## Prerequisites
- [ ] Flarum installed and running
- [ ] Extension installed and enabled
- [ ] Admin account created
- [ ] Test discussions with multiple posts

## Admin Panel Tests
- [ ] Extension appears in Extensions list
- [ ] Settings page loads without errors
- [ ] "Allow users to pin their own posts" toggle works
- [ ] "Allow pinning any post" toggle works
- [ ] Permission "Pin posts to top of discussions" appears
- [ ] Permission can be assigned to different groups

## Frontend Tests
### As Admin
- [ ] Pin icon appears in post controls dropdown
- [ ] Can pin any post in any discussion
- [ ] Pinned post moves to top of discussion
- [ ] Pinned post shows orange styling and indicator
- [ ] Can unpin posts
- [ ] Only one post can be pinned per discussion
- [ ] Pin/unpin shows confirmation dialogs

### As Regular User
- [ ] Pin icon appears (based on permissions)
- [ ] Can pin own posts (if setting enabled)
- [ ] Cannot pin others' posts (if setting disabled)
- [ ] Proper error messages for denied actions

## Visual Tests
- [ ] Pinned posts have orange border/background
- [ ] "PINNED" indicator shows in post header
- [ ] Pin icon in post controls is correct color
- [ ] Styling works in both light and dark modes
- [ ] Mobile responsive design works
- [ ] Accessibility features work (keyboard navigation)

## Database Tests
- [ ] `discussions` table has `pinned_post_id` column
- [ ] Foreign key constraint works properly
- [ ] Database migration runs without errors
- [ ] Pinned post IDs are stored correctly

## API Tests
- [ ] POST `/api/discussions/{id}/pin-post` works
- [ ] DELETE `/api/discussions/{id}/unpin-post` works
- [ ] Proper error responses for invalid requests
- [ ] Permission checks work via API

## Edge Cases
- [ ] Cannot pin deleted/hidden posts
- [ ] Cannot pin posts from different discussions
- [ ] Pinning works in locked discussions (for moderators)
- [ ] Extension uninstalls cleanly
- [ ] Works with other popular extensions

## Performance
- [ ] Page load times are not significantly affected
- [ ] No JavaScript errors in browser console
- [ ] No PHP errors in Flarum logs

## Cross-Browser Testing
- [ ] Chrome
- [ ] Firefox  
- [ ] Edge
- [ ] Safari (if available)
- [ ] Mobile browsers

---

## Test Data Setup

### Create Test Discussions
1. Create discussion: "Test Discussion 1" with 5+ posts
2. Create discussion: "Test Discussion 2" with 10+ posts
3. Create discussion: "Locked Discussion" and lock it
4. Create posts from different users

### Test User Accounts
- Admin account (full permissions)
- Moderator account (moderate permissions)
- Regular user account (basic permissions)
- Guest access (no login)

---

## Common Issues & Solutions

### Pin Icon Not Showing
- Check permissions are set correctly
- Verify user can reply to discussion
- Check JavaScript console for errors
- Clear Flarum cache

### Styling Not Applied
- Ensure CSS files are published
- Check LESS compilation
- Verify extension assets are built
- Clear browser cache

### Database Errors
- Check migration ran successfully
- Verify database user has proper permissions
- Check foreign key constraints

### Permission Denied
- Verify discussion is not locked (unless moderator)
- Check user has pinPost permission
- Verify post is visible to user
- Check post is not hidden/deleted