## 🚀 How to Run the Project

### 1. Clone the repository
```bash
git clone https://github.com/472-68-AgileDevOps/p2-final-project-sprint-procrastinator.git
cd p2-final-project-sprint-procrastinator
```

### 2. Create environment file
```bash
cp .env.example .env
```

### 3. Install PHP dependencies using Composer via Docker
```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/app" \
  -w /app \
  composer:latest \
  install --ignore-platform-reqs
```

### 4. Start Docker containers
```bash
./vendor/bin/sail up -d
```
(or if you have alias)
```bash
sail up -d
```

### 5. Generate application key
```bash
sail artisan key:generate
```

### 6. Run migrations and seed database
```bash
sail artisan migrate:fresh --seed
```

### 7. Install frontend dependencies
```bash
sail yarn install
```

### 8. Run frontend development server
```bash
sail yarn dev
```

### 9. Open the application in your browser
```bash
http://localhost
```

[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/c3PHjGNX)
