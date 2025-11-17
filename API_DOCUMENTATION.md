# ShareUp API Documentation

## Установка и настройка

### 1. Установка зависимостей
```bash
cd backend
composer install
```

### 2. Настройка .env
```bash
cp .env.example .env
php artisan key:generate
```

Настройте базу данных в .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shareup
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Миграции
```bash
php artisan migrate
```

### 4. Storage link
```bash
php artisan storage:link
```

### 5. Запуск сервера
```bash
php artisan serve
```

## API Endpoints

### Authentication

#### POST /api/auth/register
Регистрация нового пользователя

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "user": {...},
  "token": "1|xxxxx..."
}
```

#### POST /api/auth/login
Вход в систему

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "user": {...},
  "token": "1|xxxxx..."
}
```

#### POST /api/auth/logout
Выход из системы (требуется авторизация)

#### GET /api/auth/me
Получить текущего пользователя (требуется авторизация)

---

### Profile

#### GET /api/profile/{id}
Получить профиль пользователя

#### PUT /api/profile/update
Обновить профиль (требуется авторизация)

**Body:**
```json
{
  "name": "John Doe",
  "bio": "Hello world!"
}
```

#### POST /api/profile/avatar
Загрузить аватар (требуется авторизация)

**Body:** (multipart/form-data)
```
avatar: file
```

---

### Posts

#### GET /api/posts
Получить ленту постов (пагинация)

**Query params:**
- page: номер страницы

#### POST /api/posts
Создать пост (требуется авторизация)

**Body:** (multipart/form-data)
```
content: текст поста
image: файл изображения (опционально)
```

#### GET /api/posts/{id}
Получить конкретный пост

#### DELETE /api/posts/{id}
Удалить пост (требуется авторизация, владелец или админ)

#### GET /api/posts/user/{userId}
Получить посты пользователя

---

### Likes

#### POST /api/likes/{postId}
Поставить/убрать лайк (toggle)

**Response:**
```json
{
  "liked": true,
  "likes_count": 5
}
```

---

### Comments

#### GET /api/comments/post/{postId}
Получить комментарии к посту

#### POST /api/comments/post/{postId}
Создать комментарий

**Body:**
```json
{
  "content": "Great post!"
}
```

#### DELETE /api/comments/{id}
Удалить комментарий (владелец или админ)

---

### Points

#### GET /api/points/balance
Получить баланс поинтов

**Response:**
```json
{
  "points": 150,
  "user": {...}
}
```

#### GET /api/points/transactions
Получить историю транзакций

---

### Admin (требуется роль админа)

#### GET /api/admin/dashboard
Статистика платформы

#### GET /api/admin/users
Список всех пользователей

#### POST /api/admin/users/{userId}/ban
Заблокировать пользователя

#### POST /api/admin/users/{userId}/unban
Разблокировать пользователя

#### DELETE /api/admin/users/{userId}
Удалить пользователя

#### POST /api/admin/users/{userId}/points/add
Добавить поинты

**Body:**
```json
{
  "amount": 100,
  "description": "Bonus points"
}
```

#### POST /api/admin/users/{userId}/points/deduct
Списать поинты

**Body:**
```json
{
  "amount": 50,
  "description": "Penalty"
}
```

#### GET /api/admin/posts
Список всех постов

#### DELETE /api/admin/posts/{postId}
Удалить пост

#### GET /api/admin/logs
Логи транзакций поинтов

---

## Система поинтов

- Создание поста: +10 поинтов
- Получение лайка: +2 поинта
- Создание комментария: +5 поинтов
- Админ может вручную добавлять/списывать поинты

---

## Headers для авторизации

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

