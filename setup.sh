#!/bin/bash

# Drupal Event Management Platform Setup Script
# This script automates the setup process for the interview project

set -e

echo "🚀 Setting up Drupal Event Management Platform..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker and try again."
    exit 1
fi

# Create necessary directories
echo "📁 Creating project directories..."
mkdir -p web/sites/default/files
mkdir -p config/sync

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod 755 web/sites/default/files

# Start Docker containers
echo "🐳 Starting Docker containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install Drupal dependencies
echo "📦 Installing Drupal dependencies..."
docker-compose exec drupal composer install --no-interaction

# Check if Drupal is already installed
if docker-compose exec drupal drush status --format=json | grep -q '"bootstrap": "Successful"'; then
    echo "✅ Drupal is already installed."
else
    echo "🔧 Installing Drupal..."
    docker-compose exec drupal drush site:install standard \
        --db-url=mysql://drupal:drupal@db/drupal \
        --account-name=admin \
        --account-pass=admin \
        --site-name="Event Management Platform" \
        --site-mail=admin@example.com \
        -y
fi

# Enable custom modules and themes
echo "🔧 Enabling custom modules and themes..."
docker-compose exec drupal drush en event_notifier -y
docker-compose exec drupal drush theme:enable event_theme -y
docker-compose exec drupal drush config:set system.theme default event_theme -y

# Import configuration
echo "📥 Importing configuration..."
docker-compose exec drupal drush config:import -y

# Clear cache
echo "🧹 Clearing cache..."
docker-compose exec drupal drush cr

# Create sample event
echo "📝 Creating sample event..."
docker-compose exec drupal drush node:create event \
    --title="Tech Conference 2024" \
    --field_event_date="2024-06-15T09:00:00" \
    --field_location="Bogotá, Colombia" \
    --body="Join us for an exciting tech conference featuring the latest innovations in software development and architecture." \
    -y

echo "✅ Setup complete!"
echo ""
echo "🌐 Access your site at: http://localhost:8080"
echo "👤 Admin login: admin / admin"
echo "📊 Database: http://localhost:8081 (phpMyAdmin)"
echo ""
echo "📋 Next steps:"
echo "1. Visit http://localhost:8080/admin/config/system/event-notifier"
echo "2. Configure email notifications"
echo "3. Create more events to test the system"
echo "4. Explore the custom theme and module functionality"
echo ""
echo "🔧 Useful commands:"
echo "- View logs: docker-compose exec drupal drush watchdog:show"
echo "- Clear cache: docker-compose exec drupal drush cr"
echo "- Export config: docker-compose exec drupal drush config:export"
echo "- Stop containers: docker-compose down"
