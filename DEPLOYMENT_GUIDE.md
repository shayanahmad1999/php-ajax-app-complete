# Complete Deployment Guide

This guide will walk you through deploying your PHP AJAX PostgreSQL application to GitHub and Azure step by step.

## Part 1: GitHub Upload

### Step 1: Create a GitHub Repository

1. Go to [GitHub.com](https://github.com) and sign in to your account
2. Click the "+" icon in the top right corner and select "New repository"
3. Name your repository (e.g., "php-ajax-user-management")
4. Add a description: "Modern PHP application with AJAX and PostgreSQL"
5. Choose "Public" or "Private" based on your preference
6. **Do NOT** initialize with README, .gitignore, or license (we already have these)
7. Click "Create repository"

### Step 2: Upload Your Code to GitHub

#### Option A: Using Git Command Line

1. **Initialize Git in your project folder:**
   ```bash
   cd php-ajax-app
   git init
   ```

2. **Add all files to Git:**
   ```bash
   git add .
   ```

3. **Make your first commit:**
   ```bash
   git commit -m "Initial commit: PHP AJAX PostgreSQL User Management System"
   ```

4. **Add your GitHub repository as remote:**
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME.git
   ```

5. **Push to GitHub:**
   ```bash
   git push -u origin main
   ```

#### Option B: Using GitHub Desktop

1. Download and install [GitHub Desktop](https://desktop.github.com/)
2. Open GitHub Desktop and sign in
3. Click "Add an Existing Repository from your Hard Drive"
4. Select your `php-ajax-app` folder
5. Click "Publish repository" and select your GitHub account
6. Enter repository name and description
7. Click "Publish Repository"

#### Option C: Using GitHub Web Interface

1. In your GitHub repository, click "uploading an existing file"
2. Drag and drop all files from your `php-ajax-app` folder
3. Write a commit message: "Initial commit"
4. Click "Commit changes"

## Part 2: Azure Deployment

### Prerequisites

Before deploying to Azure, ensure you have:
- An Azure account ([create free account](https://azure.microsoft.com/free/))
- Azure CLI installed ([download here](https://docs.microsoft.com/en-us/cli/azure/install-azure-cli))

### Step 1: Login to Azure

```bash
az login
```

This will open a browser window for authentication.

### Step 2: Create Resource Group

```bash
az group create --name php-ajax-app-rg --location "East US"
```

### Step 3: Create App Service Plan

```bash
az appservice plan create \
  --name php-ajax-app-plan \
  --resource-group php-ajax-app-rg \
  --sku B1 \
  --is-linux
```

### Step 4: Create Web App

```bash
az webapp create \
  --resource-group php-ajax-app-rg \
  --plan php-ajax-app-plan \
  --name YOUR-UNIQUE-APP-NAME \
  --runtime "PHP|8.1"
```

**Note:** Replace `YOUR-UNIQUE-APP-NAME` with a unique name (e.g., `php-ajax-app-yourname-2024`)

### Step 5: Create PostgreSQL Database

```bash
# Create PostgreSQL server
az postgres server create \
  --resource-group php-ajax-app-rg \
  --name YOUR-UNIQUE-DB-SERVER \
  --location "East US" \
  --admin-user dbadmin \
  --admin-password "YourSecurePassword123!" \
  --sku-name GP_Gen5_2

# Create database
az postgres db create \
  --resource-group php-ajax-app-rg \
  --server-name YOUR-UNIQUE-DB-SERVER \
  --name php_ajax_app
```

**Note:** Replace `YOUR-UNIQUE-DB-SERVER` with a unique name and choose a secure password.

### Step 6: Configure Firewall Rules

```bash
# Allow Azure services
az postgres server firewall-rule create \
  --resource-group php-ajax-app-rg \
  --server YOUR-UNIQUE-DB-SERVER \
  --name AllowAzureServices \
  --start-ip-address 0.0.0.0 \
  --end-ip-address 0.0.0.0

# Allow your IP (optional, for management)
az postgres server firewall-rule create \
  --resource-group php-ajax-app-rg \
  --server YOUR-UNIQUE-DB-SERVER \
  --name AllowMyIP \
  --start-ip-address YOUR-IP \
  --end-ip-address YOUR-IP
```

### Step 7: Set Environment Variables

```bash
az webapp config appsettings set \
  --resource-group php-ajax-app-rg \
  --name YOUR-UNIQUE-APP-NAME \
  --settings \
    DB_HOST="YOUR-UNIQUE-DB-SERVER.postgres.database.azure.com" \
    DB_NAME="php_ajax_app" \
    DB_USER="dbadmin@YOUR-UNIQUE-DB-SERVER" \
    DB_PASSWORD="YourSecurePassword123!" \
    DB_PORT="5432" \
    APP_ENV="production" \
    APP_DEBUG="false"
```

### Step 8: Deploy from GitHub

#### Option A: Using Azure CLI

```bash
az webapp deployment source config \
  --resource-group php-ajax-app-rg \
  --name YOUR-UNIQUE-APP-NAME \
  --repo-url https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME \
  --branch main \
  --manual-integration
```

#### Option B: Using Azure Portal

1. Go to [Azure Portal](https://portal.azure.com)
2. Navigate to your App Service
3. Go to "Deployment Center" in the left menu
4. Select "GitHub" as source
5. Authorize Azure to access your GitHub
6. Select your repository and branch
7. Click "Save"

### Step 9: Set Up Database Schema

You need to run the database migration on your Azure PostgreSQL:

1. **Connect to your Azure PostgreSQL:**
   ```bash
   psql "host=YOUR-UNIQUE-DB-SERVER.postgres.database.azure.com port=5432 dbname=php_ajax_app user=dbadmin@YOUR-UNIQUE-DB-SERVER password=YourSecurePassword123! sslmode=require"
   ```

2. **Run the schema creation:**
   ```sql
   -- Copy and paste the contents of sql/create_tables.sql
   CREATE TABLE IF NOT EXISTS users (
       id SERIAL PRIMARY KEY,
       name VARCHAR(100) NOT NULL,
       email VARCHAR(150) UNIQUE NOT NULL,
       phone VARCHAR(20),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   
   -- Create indexes
   CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
   CREATE INDEX IF NOT EXISTS idx_users_name ON users(name);
   
   -- Insert sample data
   INSERT INTO users (name, email, phone) VALUES 
   ('John Doe', 'john.doe@example.com', '+1-555-0123'),
   ('Jane Smith', 'jane.smith@example.com', '+1-555-0124'),
   ('Mike Johnson', 'mike.johnson@example.com', '+1-555-0125')
   ON CONFLICT (email) DO NOTHING;
   ```

### Step 10: Test Your Deployment

1. Visit your app URL: `https://YOUR-UNIQUE-APP-NAME.azurewebsites.net`
2. Test all functionality:
   - View existing users
   - Add a new user
   - Edit a user
   - Delete a user
   - Search functionality

## Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Issues

**Problem:** "Connection failed" errors

**Solution:**
- Verify firewall rules allow Azure services
- Check environment variables are set correctly
- Ensure PostgreSQL server is running

#### 2. 500 Internal Server Error

**Problem:** Application shows 500 error

**Solution:**
- Check application logs in Azure Portal
- Verify all files were deployed correctly
- Ensure composer dependencies are installed

#### 3. CSS/JS Not Loading

**Problem:** Styling or JavaScript not working

**Solution:**
- Check file paths in HTML
- Verify assets are in the correct directory
- Check web.config rewrite rules

#### 4. AJAX Requests Failing

**Problem:** API calls return errors

**Solution:**
- Check API endpoint URLs
- Verify CORS headers
- Test API endpoints directly

### Getting Help

1. **Azure Portal Logs:**
   - Go to your App Service
   - Navigate to "Log stream" or "Logs"
   - Check for error messages

2. **Database Logs:**
   - Go to your PostgreSQL server
   - Check "Server logs" section

3. **GitHub Issues:**
   - Create an issue in your repository
   - Include error messages and steps to reproduce

## Security Considerations

### Production Checklist

- [ ] Use strong database passwords
- [ ] Enable SSL for database connections
- [ ] Set `APP_DEBUG=false` in production
- [ ] Regularly update dependencies
- [ ] Monitor application logs
- [ ] Set up backup for database
- [ ] Configure custom domain with SSL certificate

### Environment Variables Security

Never commit sensitive information to Git:
- Database passwords
- API keys
- Secret tokens

Always use Azure App Settings for sensitive configuration.

## Monitoring and Maintenance

### Set Up Monitoring

1. **Application Insights:**
   ```bash
   az monitor app-insights component create \
     --app YOUR-UNIQUE-APP-NAME-insights \
     --location "East US" \
     --resource-group php-ajax-app-rg
   ```

2. **Database Monitoring:**
   - Enable query performance insights
   - Set up alerts for high CPU/memory usage

### Regular Maintenance

- Update PHP dependencies monthly
- Monitor database performance
- Review application logs weekly
- Test backup and restore procedures

## Cost Optimization

### Azure Cost Management

1. **Use appropriate service tiers:**
   - Start with Basic tier for development
   - Scale up only when needed

2. **Monitor usage:**
   - Set up billing alerts
   - Review cost analysis monthly

3. **Optimize database:**
   - Choose right compute size
   - Use connection pooling
   - Archive old data

## Next Steps

After successful deployment:

1. **Custom Domain:** Set up a custom domain name
2. **SSL Certificate:** Enable HTTPS with free SSL
3. **CDN:** Add Azure CDN for better performance
4. **Backup:** Set up automated backups
5. **Monitoring:** Configure alerts and monitoring
6. **CI/CD:** Set up automated deployment pipeline

Congratulations! Your PHP AJAX PostgreSQL application is now live on Azure! 🎉

