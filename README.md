# YouTube Course Scraper

A Laravel 12 web application that leverages **OpenAI** and the **YouTube Data API v3** to intelligently discover and collect educational playlists from YouTube based on user-defined categories.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Requirements](#requirements)
- [Installation](#installation)
- [API Keys Configuration](#api-keys-configuration)
- [Running the Project](#running-the-project)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Routes](#routes)
- [License](#license)

---

## Overview

This application automates the process of discovering educational YouTube playlists:

1. **Input** — The user enters learning categories (one per line).
2. **AI Generation** — Each category is sent to OpenAI to generate 10 targeted search queries.
3. **YouTube Search** — Each query is used to search YouTube for matching playlists.
4. **Deduplication** — Results are stored in MySQL with automatic duplicate prevention via unique `playlist_id`.
5. **Display** — A filterable, paginated card-based UI presents all collected playlists.

---

## Tech Stack

| Layer       | Technology              |
|-------------|-------------------------|
| Framework   | Laravel 12              |
| Language    | PHP 8.2                 |
| Database    | MySQL                   |
| Frontend    | Bootstrap 5, Blade      |
| AI Provider | OpenAI API (GPT-3.5)    |
| Video API   | YouTube Data API v3     |

---

## Architecture

The application follows the **Repository Design Pattern** with a dedicated service layer:

```
Request → FormRequest → Controller → Service → Repository → Model → Database
```

| Layer              | Responsibility                              |
|--------------------|---------------------------------------------|
| **FormRequest**    | Input validation (thin, reusable)           |
| **Controller**     | HTTP handling only — no business logic       |
| **Service**        | Business logic and external API integration |
| **Repository**     | Data access abstraction via interfaces      |
| **Model**          | Eloquent ORM representation                 |

---

## Requirements

- PHP >= 8.2
- Composer >= 2.x
- MySQL >= 5.7
- Git

---

## Installation

```bash
# 1. Clone the repository
git clone <repository-url> e-learning-app
cd e-learning-app

# 2. Install PHP dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS e_learning_app;"

# 5. Run migrations
php artisan migrate
```

---

## API Keys Configuration

The application requires two API keys. Add them to your `.env` file:

```env
OPENAI_API_KEY=your-openai-key-here
YOUTUBE_API_KEY=your-youtube-key-here
```

### OpenAI API Key

1. Visit [platform.openai.com/api-keys](https://platform.openai.com/api-keys)
2. Sign in or create an account
3. Generate a new secret key
4. Copy the key into `OPENAI_API_KEY` in `.env`

> If no key is provided, the app falls back to predefined search queries — it will still function, just without AI-generated titles.

### YouTube Data API v3 Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create or select a project
3. Enable **YouTube Data API v3** under *APIs & Services → Library*
4. Create an API key under *APIs & Services → Credentials*
5. Copy the key into `YOUTUBE_API_KEY` in `.env`

> Restrict the key to **YouTube Data API v3** only for security best practices.

---

## Running the Project

```bash
php artisan serve
```

Open your browser at **http://localhost:8000**

---

## Usage

1. Navigate to the **Home** page (`/`)
2. Enter your learning categories in the textarea — one category per line
3. Click **Start Fetching**
4. Wait for the process to complete (AI generation + YouTube search)
5. You will be redirected to the **Results** page (`/results`)
6. Use the category filter tabs to browse playlists by topic
7. Click any card to open the playlist directly on YouTube

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── CategoryController.php           # Thin controller
│   └── Requests/
│       └── FetchCategoriesRequest.php       # Custom form validation
├── Models/
│   └── Playlist.php                         # Eloquent model
├── Repositories/
│   ├── Interfaces/
│   │   └── PlaylistRepositoryInterface.php  # Repository contract
│   └── PlaylistRepository.php               # Eloquent implementation
├── Providers/
│   └── RepositoryServiceProvider.php        # Interface bindings
└── Services/
    ├── AIService.php                        # OpenAI integration
    └── YouTubeService.php                   # YouTube API integration
```

---

## Routes

| Method | URI       | Controller Action              | Description                  |
|--------|-----------|--------------------------------|------------------------------|
| GET    | `/`       | `CategoryController@index`     | Home page with input form    |
| POST   | `/fetch`  | `CategoryController@fetch`     | Process categories           |
| GET    | `/results`| `CategoryController@results`   | Paginated results with filter|

---

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
