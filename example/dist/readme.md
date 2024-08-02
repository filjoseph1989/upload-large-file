### Step-by-Step Guide

#### Step 1: Ensure Laravel is Running

1. **Open Terminal**: Open your terminal or command prompt.
2. **Navigate to Laravel Project**: Change to your Laravel project directory.
   ```bash
   cd path/to/your/laravel/project
   ```
3. **Start Laravel Server**: Run the Laravel development server.
   ```bash
   php artisan serve --port=8000
   ```
   This should start your Laravel application at `http://localhost:8000`.

#### Step 2: Start Your Node.js Server

1. **Open a New Terminal**: Open a new terminal or command prompt window.
2. **Navigate to Node.js Project**: Change to your Node.js project directory where your `server.js` file is located.
   ```bash
   cd path/to/your/nodejs/project
   ```
3. **Install Dependencies**: If you haven't installed dependencies yet, run:
   ```bash
   pnpm install
   ```
4. **Start Node.js Server**: Start your Node.js server.
   ```bash
   pnpm run start
   ```
   This will start your server and it should be accessible at `http://localhost:3001`.

### Summary

- **Laravel Server**: Runs at `http://localhost:8000`
- **Node.js Server**: Runs at `http://localhost:3001`

With both servers running, your setup should be functional and ready for use.