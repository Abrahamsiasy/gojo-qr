# QR Gen - Free QR Code Generator

A modern, free online QR code generator built with Laravel. Create beautiful, customizable QR codes with logos, colors, borders, and scan text. Perfect for businesses and individuals in Ethiopia and worldwide.

![QR Gen](https://img.shields.io/badge/QR-Gen-22d3ee?style=for-the-badge&logo=qrcode)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

## ✨ Features

- 🎨 **Multiple QR Code Types**: URL, Text, Email, SMS, Phone, WiFi, vCard
- 🖼️ **Logo Support**: Upload and embed your logo in QR codes
- 🎨 **Custom Colors**: Choose from presets or custom colors for QR code and background
- 🔲 **Borders & Frames**: Square, rounded, or circular borders with adjustable width and gap
- 📝 **Scan Text**: Add "Scan Me" text with multiple styles (Plain, Speech, Banner, Badge)
- 📏 **Size Control**: Adjustable QR code size (200px - 1000px)
- 🎯 **Error Correction**: Choose error correction level (L, M, Q, H)
- 🚀 **Real-time Preview**: See your QR code update as you customize
- 💾 **No Storage**: QR codes are generated on-the-fly, not saved to disk
- 🌍 **SEO Optimized**: Built-in SEO for better search engine visibility
- 🇪🇹 **Ethiopia Focused**: Optimized for Ethiopian users and businesses

## 🚀 Quick Start

### Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- GD Library or Imagick extension (for image processing)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd qrcodegenerator
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` in your browser.

## 📦 Production Deployment

### Server Requirements

- PHP 8.2 or higher
- GD Library or Imagick extension
- Composer
- Node.js (for building assets)
- Web server (Apache/Nginx)

### Deployment Steps

1. **Set environment to production**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize Laravel**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Build production assets**
   ```bash
   npm run build
   ```

4. **Set up scheduled tasks** (for automatic cleanup)
   
   Add to your crontab:
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

5. **Set proper permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## 🔧 Configuration

### Rate Limiting

The application includes rate limiting to prevent abuse:
- **60 requests per minute** per IP address for QR generation
- Adjustable in `routes/web.php`

### Temporary File Cleanup

Temporary logo files are automatically cleaned up:
- Cleanup runs hourly via scheduled task
- Files older than 1 hour are deleted
- Manual cleanup: `php artisan qr:cleanup-temp --hours=1`

### Storage

- **QR codes are NOT saved** - Generated in memory and streamed directly
- **Temporary logo files** - Stored in `storage/app/temp` and cleaned automatically
- **No database required** - Stateless application

## 📖 Usage

### Generating QR Codes

1. Select QR code type (URL, Text, Email, etc.)
2. Enter your content
3. Customize appearance:
   - Choose colors (presets or custom)
   - Upload logo (optional)
   - Add border with gap
   - Add "Scan Me" text
4. Adjust size and error correction
5. Preview updates in real-time
6. Download or copy to clipboard

### QR Code Types

- **URL**: Direct links to websites
- **Text**: Plain text messages
- **Email**: Pre-filled email with subject and body
- **SMS**: Pre-filled SMS with phone number and message
- **Phone**: Direct phone call
- **WiFi**: WiFi network credentials
- **vCard**: Contact information (Name|Phone|Email|Organization)

## 🏗️ Architecture

### How It Works

1. **Request Handling**: User submits form via AJAX
2. **Validation**: Server validates all inputs
3. **QR Generation**: QR code generated in memory using SimpleSoftwareIO/simple-qrcode
4. **Logo Processing**: If logo uploaded, optimized and merged (temporary file created)
5. **Image Enhancement**: Border and scan text added using GD library
6. **Response**: Binary image data streamed directly to browser
7. **Cleanup**: Temporary files deleted immediately

### Key Components

- **QrCodeController**: Main controller handling QR generation
- **LogoOptimizerService**: Handles logo optimization and cleanup
- **CleanupTempFiles Command**: Scheduled task for cleaning old temp files
- **Rate Limiting**: Prevents server overload

## 🔒 Security Features

- CSRF protection on all forms
- Input validation and sanitization
- File upload restrictions (image types, max 2MB)
- Rate limiting to prevent abuse
- No persistent storage of user data

## 📊 Performance

- **Memory Usage**: ~5-10MB per request
- **Processing Time**: < 1 second per QR code
- **No Database**: Stateless, no database queries
- **Caching**: Browser caching enabled (1 hour)
- **Rate Limiting**: 60 requests/minute per IP

## 🛠️ Development

### Available Commands

```bash
# Clean up temporary files
php artisan qr:cleanup-temp --hours=1

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run development server with hot reload
composer dev
```

### Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── CleanupTempFiles.php    # Cleanup command
├── Http/
│   └── Controllers/
│       └── QrCodeController.php    # Main QR generation logic
├── Providers/
│   └── AppServiceProvider.php      # Scheduled tasks
└── Services/
    └── LogoOptimizerService.php    # Logo optimization

resources/
└── views/
    └── index.blade.php             # Main frontend

routes/
└── web.php                         # Application routes
```

## 🌐 SEO Features

- Optimized meta tags for search engines
- Open Graph tags for social sharing
- Twitter Card support
- Structured data (JSON-LD)
- Geographic targeting (Ethiopia)
- Canonical URLs

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For issues, questions, or contributions, please open an issue on the repository.

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- QR Code generation by [SimpleSoftwareIO/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- Image processing by [Intervention Image](https://image.intervention.io/)
- Styled with [Tailwind CSS](https://tailwindcss.com)

---

**Made with ❤️ for Ethiopia and the world**
