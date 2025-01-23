<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🌦️ Weather App API

A modern Laravel-based Weather API that helps you track weather data and find cities. Built with love and PHP 8.2! 🚀

## 🛠️ Tech Stack

- Laravel 11.x
- PHP 8.2+
- SQLite (for simplicity!)
- OpenWeather API
- L5-Swagger for API documentation

## 🏃‍♂️ Quick Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Your favorite terminal
- OpenWeather API key (grab one at [OpenWeather](https://openweathermap.org/api))

### Installation

1. Clone and jump in:
```bash
git clone https://github.com/parseen254/weather-app-api.git
cd weather-app-api
```

2. Install the goods:
```bash
composer install
```

3. Set up your environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your OpenWeather API key:
```bash
# Add to your .env file:
OPENWEATHER_API_KEY=your_api_key_here
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

5. Create and seed the database:
```bash
touch database/database.sqlite
php artisan migrate --seed
```

6. Launch! 🚀
```bash
php artisan serve
```

## 📚 API Documentation

We've got Swagger! Once your app is running, check out:
- http://localhost:8000

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 🔄 Available Endpoints

- `GET /api/cities/search` - Find cities and their neighbors
- `GET /api/weather` - Get weather data for coordinates

## 🎨 Development

Hot reload for development:
```bash
composer dev
```

## 🤝 Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request 🎉

## 📝 License

MIT Licensed. Go wild! 🦁

---
Made with ☕ and Laravel
