# Drupal Event Management Platform

A demonstration project showcasing Drupal Software Architect skills including custom modules, theming, configuration management, and Docker setup.

## Features

- **Custom Content Type**: Event (title, date, location, description, status)
- **Custom Module**: Event Notifier (sends email notifications when events are published)
- **Custom Theme**: Simple Twig-based theming with template overrides
- **Configuration Management**: Export/import configuration
- **Docker Environment**: Local development with Composer and Drush
- **Security**: Proper access controls and input validation

## Prerequisites

- Docker and Docker Compose
- Git
- Composer (optional, included in Docker)

## Quick Start

### 1. Clone and Setup

```bash
# Clone the repository
git clone <your-repo-url>
cd cit

# Start the Docker environment
docker-compose up -d

# Install Drupal dependencies
docker-compose exec drupal composer install

# Install Drupal
docker-compose exec drupal drush site:install --db-url=mysql://drupal:drupal@db/drupal --account-name=admin --account-pass=admin -y

# Import configuration
docker-compose exec drupal drush config:import -y

# Clear cache
docker-compose exec drupal drush cr
```

### 2. Access Your Site

- **Drupal Site**: http://localhost:8080
- **Admin Login**: admin / admin
- **Database**: localhost:3306 (drupal/drupal)

### 3. Test the Features

1. **Create an Event**: Go to Content → Add content → Event
2. **Test Email Notification**: Check your email when publishing an event
3. **View Custom Theme**: The event pages use custom Twig templates

## Project Structure

```
cit/
├── docker/
│   ├── Dockerfile
│   └── docker-compose.yml
├── web/
│   ├── modules/
│   │   └── custom/
│   │       └── event_notifier/
│   │
│   ├── themes/
│   │   └── custom/
│   │       └── event_theme/
│   └── sites/
│       └── default/
│           └── files/
├── config/
│   └── sync/
├── composer.json
└── README.md
```

## Technical Highlights

### Custom Module (event_notifier)
- Implements `hook_node_insert()` and `hook_node_update()`
- Uses Drupal's MailManager service
- Proper dependency injection
- Configuration form for email settings

### Custom Theme (event_theme)
- Twig template overrides for Event content type
- Responsive design with CSS Grid
- Custom CSS variables for theming

### Configuration Management
- Event content type configuration
- Field configurations
- View modes and display settings

## Interview Talking Points

### Architecture Decisions
1. **Why Custom Module vs Contrib?**
   - Specific business logic
   - Full control over functionality
   - Easier to maintain and customize

2. **Security Considerations**
   - Input validation and sanitization
   - Access control using Drupal's permission system
   - SQL injection prevention through Drupal's database abstraction

3. **Performance Optimization**
   - Caching strategies
   - Database query optimization
   - Asset optimization

4. **Scalability**
   - Content type design for future expansion
   - Modular architecture
   - Configuration management for deployment

## Testing Scenarios

### 1. Create and Publish an Event
```bash
# Via Drush
docker-compose exec drupal drush node:create event --title="Tech Conference 2024" --field_event_date="2024-06-15" --field_location="Bogotá, Colombia"
```

### 2. Check Email Notifications
```bash
# View logs
docker-compose exec drupal drush watchdog:show --type=event_notifier
```

### 3. Configuration Export/Import
```bash
# Export current config
docker-compose exec drupal drush config:export

# Import config (useful for deployment)
docker-compose exec drupal drush config:import
```

## Development Workflow

### Adding New Features
1. Create custom module/theme
2. Develop and test locally
3. Export configuration
4. Commit code and config
5. Deploy to staging/production

### Code Quality
- Follow Drupal coding standards
- Use PHP_CodeSniffer
- Implement automated testing
- Regular code reviews

## Troubleshooting

### Common Issues

1. **Permission Denied**
   ```bash
   sudo chown -R $USER:$USER .
   ```

2. **Database Connection Issues**
   ```bash
   docker-compose restart db
   ```

3. **Cache Issues**
   ```bash
   docker-compose exec drupal drush cr
   ```

## Troubleshooting: Layout Builder Unexpected Error
If you see "The website encountered an unexpected error" when using Layout Builder, ensure the following directory exists and is writable inside the container:

```sh
# On the host (creates the directory if missing)
mkdir -p web/sites/default/files

# On the host (for local/dev only)
chmod -R 777 web/sites/default/files

# Inside the container (if needed)
docker-compose exec drupal mkdir -p /var/www/html/web/sites/default/files/$(date +%Y-%m)
docker-compose exec drupal chmod -R 777 /var/www/html/web/sites/default/files
```

This fixes errors like:
> DirectoryNotReadyException: The specified file ... could not be copied because the destination directory ... is not properly configured. This may be caused by a problem with file or directory permissions.

---

## VS Code Debugging Configuration

Add the following to `.vscode/launch.json` for Xdebug support:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug (Docker)",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www/html/web": "${workspaceFolder}/web"
      },
      "xdebugSettings": {
        "max_children": 256,
        "max_data": 10240,
        "max_depth": 5
      }
    }
  ]
}
```

---

## Docker Compose Debugging Notes

- Ensure your `docker-compose.yml` exposes port 9003 for Xdebug:

```yaml
services:
  drupal:
    # ...
    ports:
      - "8080:80"
      - "9003:9003"  # Xdebug
    # ...
```

- Xdebug should be enabled in your `php.ini`:

```ini
[xdebug]
extension=xdebug.so
xdebug.mode=debug,develop
xdebug.start_with_request=yes
xdebug.client_host=host.docker.internal  # or your host IP if on Linux
xdebug.client_port=9003
xdebug.log=/tmp/xdebug.log
```

## Next Steps for Interview Prep

1. **Practice explaining the architecture** to non-technical stakeholders
2. **Prepare for questions about**:
   - Why Drupal over other CMS?
   - How to handle high traffic?
   - Security best practices
   - Team collaboration and code reviews

3. **Be ready to discuss**:
   - Your role in this project
   - Technical decisions made
   - Challenges faced and solutions implemented

## Resources

- [Drupal 10 Documentation](https://www.drupal.org/docs/10)
- [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
- [Drupal Security](https://www.drupal.org/security)
- [Docker Documentation](https://docs.docker.com/)

---

**Good luck with your interview! This project demonstrates key Drupal Software Architect skills including custom development, theming, configuration management, and modern development practices.**

# Testing and Code Coverage for Event Notifier (Drupal)

## Test Framework

This project uses [Pest](https://pestphp.com/) for unit testing and code coverage, running inside Docker.

## Running Tests

To run all tests with code coverage (from the project root):

```
docker-compose exec drupal vendor/bin/pest --coverage
```

To run a specific test file with coverage:

```
docker-compose exec drupal vendor/bin/pest --coverage --coverage-text=coverage.txt tests/Unit/EventNotifierSettingsFormTest.pest.php
```

## Achieving High Coverage

- The `EventNotifierSettingsForm` class is covered by a dedicated test suite, achieving **97% code coverage**.
- Some edge-case validation tests may be omitted if they require deep Drupal form API integration.
- For best results, use real objects (like `FormState`) for form validation tests, or focus on logic that can be reliably unit tested.

## Troubleshooting

- If Pest does not discover tests automatically, specify the test file path directly as shown above.
- If you encounter errors with service mocks (e.g., `email.validator`, `messenger`, `config.typed`), ensure you mock these in your test container setup.
- For by-reference errors in form validation, use a variable for the form array and pass it by reference.
- If you see errors about `setErrorByName` not being called, consider using a real `FormState` object and checking errors via `$form_state->getErrors()`.

## Docker & Composer Setup

- The `settings.php` file is managed locally and mounted into the container via `docker-compose.yml`:
  ```yaml
  volumes:
    - ./web/sites/default/settings.php:/var/www/html/web/sites/default/settings.php
  ```
- After changes to `composer.json` or autoloading, run:
  ```sh
  docker-compose exec drupal composer dump-autoload
  ```
- To rebuild containers after PHP or extension changes:
  ```sh
  docker-compose down
  docker-compose build
  docker-compose up -d
  ```

## Example: Mocking Services in Tests

When unit testing Drupal forms or services, mock all required services:

```php
$container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
$container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
$container->set('config.typed', \Mockery::mock('Drupal\\Core\\Config\\TypedConfigManagerInterface'));
$container->set('messenger', \Mockery::mock('Drupal\\Core\\Messenger\\MessengerInterface'));
$container->set('email.validator', \Mockery::mock('Drupal\\Core\\Validator\\EmailValidatorInterface'));
\Drupal::setContainer($container);
```

## Summary
- Use Pest for all unit tests.
- Run tests via Docker for consistent results.
- Achieve high coverage by focusing on business logic and mocking all required services.
- For form validation, use real `FormState` objects if possible.
