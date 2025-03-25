# ZHC Communication Tool

A web application for managing and streamlining communication for ZHC Hockey Club. This tool allows club members to submit news articles and administrators to manage their publication across multiple platforms.

## Features

- User authentication and role-based access control
- News article creation with rich text editing
- Image upload support (up to 5 images per article)
- Publication workflow management
- Multi-platform publishing (Website, Digital Agenda, Mobile App)
- Status tracking and processing history

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer for PHP dependency management

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/chosee/roger_tips_zhc_communication.git
   cd roger_tips_zhc_communication
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and configure it:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your database and other configuration settings.

4. Set up the database:
   ```bash
   mysql -u your_username -p < database/schema.sql
   ```

5. Configure your web server:
   - Point the document root to the `src/public` directory
   - Ensure Apache mod_rewrite is enabled
   - Make sure the `uploads` directory is writable:
     ```bash
     chmod -R 755 src/public/uploads
     ```

6. Default admin credentials:
   - Username: admin
   - Password: admin123
   (Change these immediately after first login)

## Directory Structure

```
src/
├── config/         # Configuration files
├── controllers/    # Application controllers
├── models/         # Data models
├── views/          # View templates
├── includes/       # Helper functions and utilities
└── public/         # Publicly accessible files
    ├── css/        # Stylesheets
    ├── js/         # JavaScript files
    └── uploads/    # User uploaded files
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details. 