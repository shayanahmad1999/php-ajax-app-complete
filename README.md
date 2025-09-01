# PHP AJAX PostgreSQL User Management System

A modern, responsive web application built with PHP, AJAX, and PostgreSQL featuring a beautiful user interface and complete CRUD functionality.

## Features

- 🎨 **Beautiful UI**: Modern gradient design with smooth animations and responsive layout
- 🔄 **AJAX Functionality**: Real-time user interactions without page refreshes
- 🗄️ **PostgreSQL Database**: Robust database with proper indexing and relationships
- 🔍 **Search Functionality**: Real-time user search with debounced input
- 📱 **Responsive Design**: Works perfectly on desktop and mobile devices
- 🔒 **Environment Configuration**: Secure configuration using .env files
- ☁️ **Cloud Ready**: Configured for Azure deployment

## Technology Stack

- **Backend**: PHP 8.1+ with PDO
- **Database**: PostgreSQL 14+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with CSS Grid and Flexbox
- **Icons**: Font Awesome 6
- **Dependencies**: Composer for PHP package management

## Project Structure

```
php-ajax-app/
├── config/
│   └── database.php          # Database configuration class
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css     # Main stylesheet
│   │   └── js/
│   │       └── app.js        # AJAX functionality
│   ├── index.php             # Main application page
│   └── api.php               # REST API endpoints
├── src/
│   └── User.php              # User model class
├── sql/
│   └── create_tables.sql     # Database schema
├── .env                      # Environment configuration
├── composer.json             # PHP dependencies
├── web.config               # Azure configuration
├── deploy.cmd               # Azure deployment script
└── README.md                # This file
```

## Local Development Setup

### Prerequisites

- PHP 8.1 or higher
- PostgreSQL 14 or higher
- Composer
- Web server (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd php-ajax-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   ```

4. **Set up PostgreSQL database**
   ```bash
   # Create database
   createdb php_ajax_app
   
   # Run migrations
   psql -d php_ajax_app -f sql/create_tables.sql
   ```

5. **Start the development server**
   ```bash
   php -S localhost:8000 -t public
   ```

6. **Access the application**
   Open your browser and navigate to `http://localhost:8000`

## Environment Configuration

The application uses environment variables for configuration. Update the `.env` file with your settings:

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=5432
DB_NAME=php_ajax_app
DB_USER=postgres
DB_PASSWORD=your_password_here

# Application Configuration
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Azure Configuration (for deployment)
AZURE_DB_HOST=your-azure-db-host.postgres.database.azure.com
AZURE_DB_NAME=php_ajax_app
AZURE_DB_USER=your_azure_user@your-server
AZURE_DB_PASSWORD=your_azure_password
```

## Database Schema

The application uses a simple but effective database schema:

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## API Endpoints

The application provides a RESTful API:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api.php` | Get all users |
| GET | `/api.php?action=search&q=term` | Search users |
| GET | `/api.php?action=get&id=1` | Get single user |
| POST | `/api.php?action=create` | Create new user |
| PUT | `/api.php?action=update` | Update existing user |
| DELETE | `/api.php?action=delete&id=1` | Delete user |

## Azure Deployment

### Prerequisites

- Azure account with active subscription
- Azure CLI installed
- Git repository (GitHub, Azure DevOps, etc.)

### Deployment Steps

1. **Create Azure App Service**
   ```bash
   # Create resource group
   az group create --name myResourceGroup --location "East US"
   
   # Create App Service plan
   az appservice plan create --name myAppServicePlan --resource-group myResourceGroup --sku B1 --is-linux
   
   # Create web app
   az webapp create --resource-group myResourceGroup --plan myAppServicePlan --name myUniqueAppName --runtime "PHP|8.1"
   ```

2. **Create Azure Database for PostgreSQL**
   ```bash
   # Create PostgreSQL server
   az postgres server create --resource-group myResourceGroup --name mypostgresqlserver --location "East US" --admin-user myadmin --admin-password myPassword123! --sku-name GP_Gen5_2
   
   # Create database
   az postgres db create --resource-group myResourceGroup --server-name mypostgresqlserver --name php_ajax_app
   ```

3. **Configure App Settings**
   ```bash
   # Set environment variables
   az webapp config appsettings set --resource-group myResourceGroup --name myUniqueAppName --settings \
     DB_HOST="mypostgresqlserver.postgres.database.azure.com" \
     DB_NAME="php_ajax_app" \
     DB_USER="myadmin@mypostgresqlserver" \
     DB_PASSWORD="myPassword123!" \
     APP_ENV="production"
   ```

4. **Deploy from Git**
   ```bash
   # Configure deployment source
   az webapp deployment source config --resource-group myResourceGroup --name myUniqueAppName --repo-url https://github.com/yourusername/php-ajax-app --branch main --manual-integration
   ```

### GitHub Deployment

1. **Push to GitHub**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/yourusername/php-ajax-app.git
   git push -u origin main
   ```

2. **Configure GitHub Actions** (Optional)
   Create `.github/workflows/azure-deploy.yml` for automated deployment.

## Features Overview

### User Interface
- **Modern Design**: Gradient backgrounds, card-based layout, smooth animations
- **Responsive Layout**: CSS Grid and Flexbox for perfect mobile experience
- **Interactive Elements**: Hover effects, loading states, notifications
- **Accessibility**: Proper ARIA labels, keyboard navigation support

### Functionality
- **Create Users**: Add new users with validation
- **Read Users**: View all users with pagination support
- **Update Users**: Edit existing user information
- **Delete Users**: Remove users with confirmation
- **Search**: Real-time search with debounced input
- **Notifications**: Success/error messages with auto-dismiss

### Security
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Protection**: Prepared statements with PDO
- **XSS Prevention**: HTML escaping for all outputs
- **CSRF Protection**: Token-based protection (can be added)

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@yourcompany.com or create an issue in the GitHub repository.

## Changelog

### Version 1.0.0
- Initial release
- Complete CRUD functionality
- Beautiful responsive UI
- Azure deployment ready
- PostgreSQL integration
- AJAX-powered interactions

