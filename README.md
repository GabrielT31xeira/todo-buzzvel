# Run the project
 - Run ``docker compose up --build``
 - Enter laravel container ``docker exec -it <id-container> /bin/bash`` and run ``php artisan migrate`` and ``php artisan db:seed --class=UserSeeder``.
 - Acess routes

## Login
``http://localhost:8000/api/login``

``` 
{
    "email": "buzzvel@gmail.com",
    "password": "password"
}
```

# Protected routes
Use ``Bearer <token>``
## Logout - post
``http://localhost:8000/api/logout``

## List tasks - get
``http://localhost:8000/api/tasks``

## Create a new task - post
``http://localhost:8000/api/tasks``
- Validators ``title: required|unique``, ``description: required``, ``pdf:max 20000 caracters``
```
{
    "title":"Example 1",
    "description":"description 1",
    "pdf":["base64 enconde"]
}
```
## Get specific task - get
``http://localhost:8000/api/tasks/1``

## Update task
``http://localhost:8000/api/tasks/1``
- Validators ``title: required|unique``, ``description: required``, ``pdf:max 20000 caracters``
```{
    "title":"Update 1",
    "description":"Update 1",
    "pdf":["base64 enconde"]
}
```

## Delete a task

