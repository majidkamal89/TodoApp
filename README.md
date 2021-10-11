# TodoApp
----------


RESTful API backend for a Todo application based on PHP Lumen framework.

# Getting Started

This is an example of how you may give instructions on setting up your project locally. To get a local copy up and running follow these simple example steps.

# Prerequisites

Clone this repo and go to your project directory, open CMD terminal and run below command

* composer install

It will download all dependencies.

# Start Local Server

To start server run command

* php -S localhost:8000 -t public

Open second terminal in same directory and run this command

* php -S localhost:9001 -t public


# Note

* server localhost:8000 will use for all api response.
* server localhost:9001 will be use for only login API as it will use Guzzle-http client which doesn't work on same server.

# API List

#### Register Route
* localhost:8000/api/register (use form-data)

####Login Route
- localhost:8000/api/login (use form-data) 

It will return Bearer Token


####Todo List API's
* GET / localhost:8000/api/todo/list  - (With Bearer Token)
* PUT / localhost:8000/api/todo/store (use form-data)

Query Params are (name, description, date_time, category_id)

* PUT / localhost:8000/api/todo/update (use form-data)

Query Params are (id, name, description, date_time, category_id, status)

* DELETE / localhost:8000/api/todo/delete/{id}  - (With Bearer Token)

####Category API's
* GET / localhost:800/api/category/list
* PUT / localhost:8000/api/category/store  (use form-data)

Query Params is (name)

* PUT / localhost:8000/api/category/update  (use form-data)

Query Params are (id, name)

* DELETE / localhost:8000/api/category/delete/{id}  - (With Bearer Token)

Logout API

* POST / localhost:8000/api/logout  - (With Bearer Token)

