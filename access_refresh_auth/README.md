Sure — keep it simple and focused on what we actually implemented.

````markdown
# Access & Refresh Token Authentication

A simple PHP authentication project demonstrating JWT-based authentication using access tokens and refresh tokens.

## Features

- User registration
- Password hashing
- User login
- JWT Access Token
- JWT Refresh Token
- HTTP-only cookies
- Refresh token stored as a hash in MySQL
- Access token expiration
- Refresh token expiration
- Refresh token rotation
- Refresh token revocation
- Logout
- CSRF token generation and verification
- Protected API using authentication middleware

## Token Details

- Access Token: JWT, expires in 15 minutes
- Refresh Token: JWT, expires in 2 days
- Both tokens are stored in HTTP-only cookies
- Refresh token hash is stored in the database

## Technologies

- PHP
- MySQL
- JWT
- Firebase PHP-JWT
- Composer
- Postman

## Project Structure

```text
access_refresh_auth/
│
├── auth/
│   ├── register.php
│   ├── login.php
│   ├── refresh.php
│   ├── logout.php
│   └── csrf.php
│
├── api/
│   ├── profile.php
│   └── update-profile.php
│
├── middleware/
│   ├── AuthMiddleware.php
│   └── CsrfMiddleware.php
│
├── config/
│   ├── database.php
│   └── jwt.php
│
├── database/
│   └── database.sql
│
├── .env
├── .env.example
├── .gitignore
├── composer.json
└── composer.lock
````

## Authentication Flow

```text
Register
   ↓
Login
   ↓
Generate Access JWT + Refresh JWT
   ↓
Store tokens in HTTP-only cookies
   ↓
Store refresh token hash in database
   ↓
Access protected API
   ↓
Access token expires
   ↓
Use refresh token
   ↓
Generate new Access + Refresh tokens
   ↓
Revoke old refresh token
```

## Setup

1. Clone the repository.
2. Create the MySQL database using `database/database.sql`.
3. Create a `.env` file.
4. Add your JWT secret:

```text
JWT_SECRET=your_secret_key
```

5. Install dependencies:

```bash
composer install
```

6. Start Apache and MySQL using WAMP.
7. Test the APIs using Postman.

> **Note:** Never commit the `.env` file because it contains the JWT secret.

```


```
