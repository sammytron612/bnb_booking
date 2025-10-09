# apartment Booking System

## Project Overview
Apartment booking system is a comprehensive booking system designed to manage reservations, customer interactions, and property details for a bed-and-breakfast business. The system is built using the Laravel framework and incorporates modern web development practices to ensure scalability, security, and user-friendliness.

## Features
- **Booking Management**: Handle reservations, cancellations, and modifications, refunds, stripe integratioon
- **Customer Communication**: Automated emails for booking confirmations, reminders, and review requests.
- **Dispute Resolution**: Tools to manage and resolve booking disputes.
- **SEO Optimization**: Enhanced visibility with SEO-friendly pages and sitemap integration.
- **Dynamic Content**: Interactive pages like the Seaglass page with historical content and visuals.
- **Validation**: Robust backend validation for same-day turnovers and double bookings.

## Technology Stack
- **Backend**: Laravel, Livewire (PHP Framework)
- **Frontend**: Blade Templates, Tailwind CSS, Javascript
- **Database**: MySQL
- **Testing**: PHPUnit, Pest
- **Build Tools**: Vite

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/sammytron612/bnb_booking.git
   ```
2. Navigate to the project directory:
   ```bash
   cd bnb_booking
   ```
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database,stripe credentials and mail configuration.
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```
7. Build frontend assets:
   ```bash
   npm run build
   ```
8. Start the development server:
   ```bash
   php artisan serve
   ```
   
   php artisan view:cache
   php artisan route:cache

## Usage
- Access the application at `http://localhost:8000`.
- Use the admin panel to manage bookings, disputes, and customer interactions.



## Contributing
1. Fork the repository.
2. Create a new branch for your feature:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments
- Laravel Framework
- Tailwind CSS
- Pest and PHPUnit for testing
- Open-source libraries and contributors
