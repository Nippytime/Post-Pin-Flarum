# Contributing to Post Pin for Flarum

Thank you for your interest in contributing to Post Pin! This document provides guidelines for contributing to this Flarum extension.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a new branch for your feature or bug fix
4. Make your changes
5. Test your changes thoroughly
6. Submit a pull request

## Development Setup

```bash
# Clone the repository
git clone https://github.com/Nippytime/Post-Pin-Flarum.git
cd Post-Pin-Flarum

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Build assets
npm run build
```

## Code Style

### PHP
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use strict typing where possible
- Write comprehensive PHPDoc comments
- Use meaningful variable and method names

### JavaScript
- Follow [Flarum's JavaScript guidelines](https://docs.flarum.org/extend/frontend/#javascript-code-style)
- Use ES6+ features
- Write clear, readable code
- Use Prettier for formatting

### CSS/LESS
- Follow BEM methodology where applicable
- Use semantic class names
- Consider accessibility in styling choices
- Maintain the orange/black theme consistency

## Testing

Before submitting a pull request:

```bash
# Run PHP tests
composer test

# Run static analysis
composer analyse

# Fix code style
composer cs

# Lint JavaScript
npm run lint

# Build assets
npm run build
```

## Pull Request Guidelines

1. **Create a descriptive title** - Summarize what your PR does
2. **Provide context** - Explain why this change is needed
3. **Test thoroughly** - Make sure all tests pass
4. **Keep it focused** - One feature or fix per PR
5. **Update documentation** - If you change functionality, update docs

## Bug Reports

When reporting bugs, please include:

1. **Flarum version** and **PHP version**
2. **Extension version**
3. **Steps to reproduce** the issue
4. **Expected behavior**
5. **Actual behavior**
6. **Screenshots** (if relevant)
7. **Browser and OS** (for frontend issues)

## Feature Requests

Before requesting a feature:

1. **Check existing issues** - Someone might have already suggested it
2. **Provide use cases** - Explain why this feature would be useful
3. **Consider alternatives** - Are there other ways to achieve the same goal?
4. **Think about scope** - Keep requests focused and specific

## Code Review Process

1. All submissions require review before merging
2. Reviews focus on code quality, security, and adherence to standards
3. Constructive feedback is always welcome
4. Be patient - reviews take time to ensure quality

## Security

If you discover a security vulnerability, please email [feralnub@gmail.com](mailto:feralnub@gmail.com) instead of using the issue tracker.

## Questions?

Feel free to:
- Open an issue for questions
- Start a discussion on the Flarum community forum
- Reach out via email

Thanks for contributing! 🎉