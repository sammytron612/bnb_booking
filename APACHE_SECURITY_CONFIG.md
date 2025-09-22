# Apache Virtual Host Security Configuration
# For Seaham Coastal Retreats Laravel Application
# Created: September 22, 2025

## 🔒 SECURE APACHE VIRTUAL HOST CONFIGURATION

### 1. Main Secure Virtual Host (HTTPS)

```apache
<VirtualHost *:443>
    ServerName seahamcoastalretreats.com
    ServerAlias www.seahamcoastalretreats.com
    DocumentRoot /var/www/bnb/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/private.key
    SSLCertificateChainFile /path/to/ssl/chain.pem
    
    # Security Headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://js.stripe.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https://api.stripe.com"
    
    # Hide Apache Version
    ServerTokens Prod
    ServerSignature Off
    
    # Directory Security
    <Directory /var/www/bnb/public>
        Options -Indexes -Includes -ExecCGI
        AllowOverride All
        Require all granted
        
        # Prevent access to sensitive files
        <FilesMatch "^\.">
            Require all denied
        </FilesMatch>
        
        <FilesMatch "\.(log|sql|conf|ini|bak)$">
            Require all denied
        </FilesMatch>
    </Directory>
    
    # Block access to Laravel directories
    <Directory /var/www/bnb/storage>
        Require all denied
    </Directory>
    
    <Directory /var/www/bnb/bootstrap/cache>
        Require all denied
    </Directory>
    
    # PHP Security
    php_admin_value expose_php Off
    php_admin_value display_errors Off
    php_admin_value log_errors On
    php_admin_value error_log /var/log/apache2/php_errors.log
    
    # Rate Limiting (if mod_evasive is available)
    DOSHashTableSize 4096
    DOSPageCount 3
    DOSSiteCount 50
    DOSPageInterval 1
    DOSSiteInterval 1
    DOSBlockingPeriod 600
    
    # Log Configuration
    ErrorLog ${APACHE_LOG_DIR}/bnb_error.log
    CustomLog ${APACHE_LOG_DIR}/bnb_access.log combined
    LogLevel warn
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName seahamcoastalretreats.com
    ServerAlias www.seahamcoastalretreats.com
    Redirect permanent / https://seahamcoastalretreats.com/
</VirtualHost>
```

### 2. Required Apache Modules

```bash
# Enable essential security modules
sudo a2enmod ssl
sudo a2enmod headers
sudo a2enmod rewrite
sudo a2enmod security2  # ModSecurity
sudo a2enmod evasive    # DDoS protection
sudo systemctl restart apache2
```

### 3. Additional Security Configuration

Create `/etc/apache2/conf-available/security.conf`:

```apache
# Hide Apache version and OS
ServerTokens Prod
ServerSignature Off

# Disable unnecessary HTTP methods
<Location />
    <LimitExcept GET POST HEAD PUT DELETE OPTIONS>
        Require all denied
    </LimitExcept>
</Location>

# Prevent access to .htaccess files
<FilesMatch "^\.ht">
    Require all denied
</FilesMatch>

# Prevent access to version control
<DirectoryMatch "/\.git">
    Require all denied
</DirectoryMatch>

# Timeout settings
Timeout 60
KeepAliveTimeout 5

# Limit request size (adjust for your needs)
LimitRequestBody 10485760  # 10MB
```

Enable the configuration:
```bash
sudo a2enconf security
sudo systemctl reload apache2
```

### 4. Laravel-Specific Security

Add these to your virtual host:

```apache
# Block access to Laravel sensitive files
<LocationMatch "/(\.env|\.git|composer\.(json|lock)|package\.json|artisan)">
    Require all denied
</LocationMatch>

# Block access to storage except public files
<LocationMatch "^/storage/(?!app/public/)">
    Require all denied
</LocationMatch>

# Secure session files
<Directory /var/www/bnb/storage/framework/sessions>
    Require all denied
</Directory>

# Secure log files
<Directory /var/www/bnb/storage/logs>
    Require all denied
</Directory>

# Secure config files
<Directory /var/www/bnb/config>
    Require all denied
</Directory>

# Secure vendor directory
<Directory /var/www/bnb/vendor>
    Require all denied
</Directory>
```

### 5. SSL/TLS Hardening

```apache
# SSL Protocol and Cipher Configuration
SSLProtocol -all +TLSv1.2 +TLSv1.3
SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384
SSLHonorCipherOrder off
SSLSessionTickets off

# OCSP Stapling
SSLUseStapling on
SSLStaplingCache "shmcb:logs/stapling-cache(150000)"

# Disable weak protocols
SSLCompression off
```

### 6. Payment-Specific Security (Stripe Integration)

```apache
# Secure webhook endpoint
<Location "/stripe/webhook">
    # Allow only POST
    <LimitExcept POST>
        Require all denied
    </LimitExcept>
    
    # Optional: Restrict to Stripe IPs (uncomment if needed)
    # Require ip 54.187.174.169
    # Require ip 54.187.205.235
    # Require ip 54.187.216.72
    # Require ip 54.241.31.99
    # Require ip 54.241.31.102
    # Require ip 54.241.34.107
</Location>

# Secure payment pages
<LocationMatch "^/payment/(success|cancel|checkout)">
    # Add extra security headers for payment pages
    Header always set Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate"
    Header always set Pragma "no-cache"
    Header always set Expires "0"
    
    # Additional security for payment pages
    Header always set X-Robots-Tag "noindex, nofollow"
</LocationMatch>

# Secure admin areas
<LocationMatch "^/admin">
    # Enhanced security headers for admin
    Header always set X-Frame-Options "DENY"
    Header always set Cache-Control "no-store, no-cache, must-revalidate"
</LocationMatch>
```

### 7. File Permissions Setup

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/html/eileen_bnb

# Set secure permissions
sudo find /var/www/html/eileen_bnb -type f -exec chmod 644 {} \;
sudo find /var/www/html/eileen_bnb -type d -exec chmod 755 {} \;

# Laravel specific permissions
sudo chmod -R 775 /var/www/html/eileen_bnb/storage
sudo chmod -R 775 /var/www/html/eileen_bnb/bootstrap/cache
sudo chmod 600 /var/www/html/eileen_bnb/.env

# Secure sensitive files
sudo chmod 600 /var/www/html/eileen_bnb/config/database.php
sudo chmod 600 /var/www/html/eileen_bnb/services.php
```

### 8. Firewall Configuration (UFW)

```bash
# Reset firewall
sudo ufw --force reset

# Default policies
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow essential services
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS

# Rate limiting for SSH
sudo ufw limit 22/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status verbose
```

### 9. Enhanced Logging Configuration

```apache
# Custom log format with more security details
LogFormat "%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\" %D %{X-Forwarded-For}i" security_combined

# Enhanced logging for different areas
<LocationMatch "^/(admin|payment)">
    CustomLog ${APACHE_LOG_DIR}/bnb_security.log security_combined
</LocationMatch>

<LocationMatch "^/api">
    CustomLog ${APACHE_LOG_DIR}/bnb_api.log security_combined
</LocationMatch>

# Log failed requests
CustomLog ${APACHE_LOG_DIR}/bnb_errors.log "%t %h \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" expr=resp!=200
```

### 10. ModSecurity Configuration (Optional)

Create `/etc/apache2/mods-available/security2.conf`:

```apache
<IfModule mod_security2.c>
    # Basic ModSecurity configuration
    SecRuleEngine On
    SecRequestBodyAccess On
    SecResponseBodyAccess On
    SecRequestBodyLimit 13107200
    SecRequestBodyNoFilesLimit 131072
    SecRequestBodyInMemoryLimit 131072
    SecRequestBodyLimitAction Reject
    SecRule REQUEST_HEADERS:Content-Type "text/xml" \
         "id:'200000',phase:1,t:none,t:lowercase,pass,nolog,ctl:requestBodyProcessor=XML"
    SecRule REQUEST_HEADERS:Content-Type "application/xml" \
         "id:'200001',phase:1,t:none,t:lowercase,pass,nolog,ctl:requestBodyProcessor=XML"
    SecRule REQUEST_HEADERS:Content-Type "text/json" \
         "id:'200002',phase:1,t:none,t:lowercase,pass,nolog,ctl:requestBodyProcessor=JSON"
    SecRule REQUEST_HEADERS:Content-Type "application/json" \
         "id:'200003',phase:1,t:none,t:lowercase,pass,nolog,ctl:requestBodyProcessor=JSON"
    SecRequestBodyJsonDepthLimit 512
    SecRequestBodyNoFilesLimit 131072
    SecRequestBodyInMemoryLimit 131072
    SecDataDir /tmp/
    SecUploadDir /tmp/
</IfModule>
```

### 11. Regular Security Maintenance

Create `/usr/local/bin/security-update.sh`:

```bash
#!/bin/bash
# Automated security updates for Laravel B&B app

# Update system packages
apt update && apt upgrade -y

# Update composer dependencies (if needed)
cd /var/www/bnb
sudo -u www-data composer update --no-dev --optimize-autoloader

# Clear and rebuild caches
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Reload Apache
systemctl reload apache2

# Log the update
echo "$(date): Security update completed" >> /var/log/security-updates.log
```

Make it executable:
```bash
sudo chmod +x /usr/local/bin/security-update.sh
```

Add to crontab for weekly execution:
```bash
# Run security updates every Sunday at 2 AM
0 2 * * 0 /usr/local/bin/security-update.sh
```

### 12. SSL Certificate Setup (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Obtain SSL certificate
sudo certbot --apache -d seahamcoastalretreats.com -d www.seahamcoastalretreats.com

# Test automatic renewal
sudo certbot renew --dry-run

# Set up automatic renewal
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

## 🎯 Security Checklist

### Pre-Deployment:
- [ ] SSL certificate installed and configured
- [ ] All security modules enabled
- [ ] File permissions set correctly
- [ ] Firewall configured and enabled
- [ ] Security headers configured
- [ ] Sensitive directories blocked
- [ ] Laravel .env file secured
- [ ] Database access restricted
- [ ] Log monitoring set up

### Post-Deployment:
- [ ] SSL Labs test (Grade A+ target)
- [ ] Security headers test
- [ ] Vulnerability scan completed
- [ ] Backup strategy implemented
- [ ] Monitoring alerts configured
- [ ] Incident response plan ready

## 🚨 Important Notes

⚠️ **Before implementing:**
1. **Backup your current configuration**
2. **Test on staging environment first**
3. **Update domain names** in configuration
4. **Adjust file paths** for your server setup
5. **Configure proper SSL certificates**

🔒 **Security Benefits:**
- SSL/TLS encryption for all traffic
- Security headers prevent XSS, clickjacking
- File access restrictions protect sensitive Laravel files
- Rate limiting prevents abuse and DDoS
- Payment-specific protections for Stripe integration
- Comprehensive logging for security monitoring
- ModSecurity protection against common attacks

This configuration provides enterprise-level security for your Seaham Coastal Retreats Laravel application while maintaining compatibility with Stripe payments and all existing functionality.

## 📞 Contact Information for Production Setup

When implementing this on your production server:
- **App Name**: Seaham Coastal Retreats
- **Owner Email**: kevin.wilson@kevinlwilson.co.uk
- **Owner Phone**: +44 07912345678
- **Current Domain**: Update seahamcoastalretreats.com to your actual domain

Remember to update all domain references and file paths to match your production server configuration.
