## Endpoint
- create users
 => http://127.0.0.1:8000/api/users
 example data :
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123"
}

- login users
 => http://127.0.0.1:8000/api/login
example data :
{
    "email": "user@example.com",
    "password": "password123"
}

-> create post
 => http://127.0.0.1:8000/api/posts
 example data :
 {
    "title": "Hello World",
    "body": "This is the content of the post.",
    "user_id": 1
}

-> get all post
 => http://127.0.0.1:8000/api/posts

-> get post by user_id
 => http://127.0.0.1:8000/api/users/{id}/posts
 example : http://127.0.0.1:8000/api/users/1/posts

 -> delete user and all post by user_id
  => http://127.0.0.1:8000/api/users/{id}
  example : http://127.0.0.1:8000/api/users/1
