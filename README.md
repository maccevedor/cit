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
