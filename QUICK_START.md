# 🚀 Quick Start Guide

## Prerequisites
- Docker and Docker Compose installed
- Git
- Basic command line knowledge

## ⚡ 5-Minute Setup

### 1. Clone and Navigate
```bash
cd cit
```

### 2. Run Setup Script
```bash
./setup.sh
```

### 3. Access Your Site
- **Drupal Site**: http://localhost:8080
- **Admin Login**: admin / admin
- **Database**: http://localhost:8081 (phpMyAdmin)

## 🧪 Test the Features

### 1. Create an Event
1. Go to http://localhost:8080/node/add/event
2. Fill in the form:
   - Title: "Tech Conference 2024"
   - Event Date: 2024-06-15 09:00
   - Location: "Bogotá, Colombia"
   - Body: "Join us for an exciting tech conference..."
3. Save and publish

### 2. Test Email Notifications
1. Go to http://localhost:8080/admin/config/system/event-notifier
2. Set your email address
3. Create another event to trigger notification

### 3. Explore Custom Theme
- View the event page to see custom styling
- Notice the responsive design
- Check the custom Twig template

## 🔧 Useful Commands

```bash
# View logs
docker-compose exec drupal drush watchdog:show

# Clear cache
docker-compose exec drupal drush cr

# Export configuration
docker-compose exec drupal drush config:export

# Create test event via Drush
docker-compose exec drupal drush node:create event --title="Test Event" --field_event_date="2024-07-01T10:00:00" --field_location="Test Location"

# Stop containers
docker-compose down
```

## 📁 Project Structure

```
cit/
├── docker/                    # Docker configuration
├── web/                       # Drupal web root
│   ├── modules/custom/        # Custom modules
│   └── themes/custom/         # Custom themes
├── config/sync/               # Configuration files
├── composer.json              # Dependencies
├── docker-compose.yml         # Container orchestration
├── setup.sh                   # Automated setup
└── README.md                  # Detailed documentation
```

## 🎯 Interview Prep Checklist

- [ ] **Technical Skills**: Understand the custom module code
- [ ] **Architecture**: Be able to explain design decisions
- [ ] **Security**: Know the security measures implemented
- [ ] **Performance**: Understand caching and optimization
- [ ] **Deployment**: Explain Docker and configuration management
- [ ] **Leadership**: Prepare to discuss team management and mentoring

## 🚨 Troubleshooting

### Common Issues

**Docker not running:**
```bash
sudo systemctl start docker
```

**Permission denied:**
```bash
sudo chown -R $USER:$USER .
```

**Database connection error:**
```bash
docker-compose restart db
```

**Cache issues:**
```bash
docker-compose exec drupal drush cr
```

## 📚 Next Steps

1. **Read the full documentation**: `README.md`
2. **Study the interview guide**: `INTERVIEW_PREP.md`
3. **Practice explaining the architecture**
4. **Prepare for technical questions**
5. **Test all features thoroughly**

---

**Good luck with your interview! 🎉**
