# Post Pin for Flarum

[![Latest Stable Version](https://poser.pugx.org/nippytime/post-pin-flarum/v/stable)](https://packagist.org/packages/nippytime/post-pin-flarum)
[![Total Downloads](https://poser.pugx.org/nippytime/post-pin-flarum/downloads)](https://packagist.org/packages/nippytime/post-pin-flarum)
[![License](https://poser.pugx.org/nippytime/post-pin-flarum/license)](https://packagist.org/packages/nippytime/post-pin-flarum)

A Flarum extension that allows pinning a single post to the top of any discussion thread. Perfect for creating summary posts, important announcements, or highlighting key information within discussions.

## Features

- 📌 **Pin any post** to the top of a discussion thread
- 🔄 **Replace pinned posts** - only one post can be pinned per discussion
- 🛡️ **Granular permissions** - control who can pin posts
- 🎨 **Visual indicators** - pinned posts are clearly marked with orange/black styling
- ⚙️ **Configurable settings** - admin controls for self-pinning and any-post pinning
- 📱 **Mobile responsive** - works seamlessly on all devices
- 🌙 **Dark mode support** - respects your forum's theme with enhanced orange accents
- ♿ **Accessibility focused** - follows WCAG guidelines with proper focus states

## Installation

Use Composer to install the extension:

```bash
composer require nippytime/post-pin-flarum
```

Then enable the extension in your Flarum admin panel.

## Requirements

- Flarum 1.8.0 or higher
- PHP 8.0 or higher

## Usage

### For Users

1. **Pin a post**: Navigate to any post in a discussion and click the pin icon (📌) in the post controls dropdown
2. **Unpin a post**: Click the unpin icon on a currently pinned post
3. **View pinned posts**: Pinned posts automatically appear at the top of discussions with bright orange visual indicators

### For Administrators

Navigate to the extension settings in your admin panel to configure:

- **Allow users to pin their own posts**: Let users pin their own posts in discussions
- **Allow pinning any post**: Let users pin any post in a discussion (not just their own)

### Permissions

The extension adds a new permission: **"Pin posts to top of discussions"**

This permission can be assigned to different user groups:
- **Members**: Basic pinning capabilities based on settings
- **Moderators**: Can always pin any post in any discussion
- **Administrators**: Full control over all pinning functionality

## Visual Design

This extension features a distinctive **black and orange theme**:
- Pinned posts have bold orange borders and subtle background tints
- Orange accent colors highlight important elements
- Enhanced styling for dark mode with complementary orange shades
- Smooth animations and hover effects (respects reduced motion preferences)

## Technical Details

### Database Changes

The extension adds a single column to the `discussions` table:
- `pinned_post_id` (nullable integer) - Foreign key reference to the pinned post

### API Endpoints

- `POST /api/discussions/{id}/pin-post` - Pin a post to a discussion
- `DELETE /api/discussions/{id}/unpin-post` - Unpin the current pinned post

### Security Features

- **Permission validation** - All actions are protected by proper permission checks
- **Input validation** - All user inputs are validated and sanitized
- **SQL injection protection** - Uses Eloquent ORM and prepared statements
- **XSS prevention** - Proper output escaping in templates
- **CSRF protection** - Protected by Flarum's built-in CSRF middleware

## Development

### Setup Development Environment

```bash
git clone https://github.com/Nippytime/Post-Pin-Flarum.git
cd Post-Pin-Flarum
composer install
npm install
```

### Build Assets

```bash
npm run dev    # Development build with watch
npm run build  # Production build
```

### Run Tests

```bash
composer test           # Run PHPUnit tests
composer test:setup     # Setup test database
composer analyse        # Run PHPStan static analysis
composer cs             # Fix code style
```

### File Structure

```
├── extend.php                    # Extension bootstrap
├── composer.json                 # Package definition
├── js/
│   ├── src/
│   │   ├── admin/               # Admin panel interface
│   │   └── forum/               # Forum interface
│   └── dist/                    # Compiled assets
├── less/
│   └── forum.less               # Orange/black theme styling
├── locale/
│   └── en.yml                   # Translations
├── migrations/                   # Database migrations
├── src/
│   ├── Api/Controller/          # API controllers
│   ├── Listener/                # Event listeners
│   └── Policy/                  # Permission policies
└── tests/                       # Test suites
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Code Style

This project follows [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards for PHP and [Flarum's JavaScript guidelines](https://docs.flarum.org/extend/frontend/#javascript-code-style).

### Testing

Please ensure all tests pass before submitting a pull request:

```bash
composer test
composer analyse
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a full list of changes.

## Security

If you discover any security vulnerabilities, please send an email to [feralnub@gmail.com](mailto:feralnub@gmail.com) instead of using the issue tracker.

## License

This extension is open source software licensed under the [MIT License](LICENSE).

## Support

- 💬 [Community Discussion](https://discuss.flarum.org/t/post-pin-extension)
- 🐛 [Issue Tracker](https://github.com/Nippytime/Post-Pin-Flarum/issues)
- 📧 [Email Support](mailto:feralnub@gmail.com)

## Credits

- Built with ❤️ by [Nippytime](https://github.com/Nippytime)
- Inspired by the need for better discussion organization in Flarum communities
- Thanks to the Flarum development team for creating an excellent extension framework

---

**Made with Flarum Extension Best Practices** ✨