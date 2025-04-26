# Dantown 


## Prerequisites

- Laravel 12.x 
- Php >= 8.3
- A MySQL database 
- Nginx (Server)

## Architecture Layer
<p>This is MVC pattern with Service, Contract and Query Layer. The Query layer serves for encapsulation of database queries outside the service logic. This make the structure reusable and easily modifiable<p>

## Routes 
<p>API Routes are mapped in bootstrap/app.php for easy maintenance. So each routes is prefixed semantically to its used case. For laravel 10x it can be achieved in App/Providers/RouteServiceProvider</p>
<p>This assessment used Modular routing, Service layer with Contracts(Interface) binded for easy maintenance. No Respository layer was created for simplicity </p>
  
  ```bash
  GET       auth/me ..................Current Logged In user
  POST      auth/signin ............... SignIn 

  POST      /events/schedule .............. Schedule an event using the name, start_time, end_time (both are date), and max_participants(integer)
  GET       /events ......................... Fetch all Events (paginated)
  GET      /events/{eventId} ......................... Get a specific event using the ID
  POST    /events/participant/register ................. To register participant using the event_id
  ```

### Register Participant
 <p>Participants are logged in user (admin, manager or user) using the event ID</p>


## Setup
 <p>Setup your project base Url i.e {{baseUrl}} on postman. Import the API collection in collections/ directory in the project root folder into your postman. Set your authorization header to accept application/json, and Authorization as bearer token which is your auth token</p>

### Clone the Repository
```bash
git switch dev
cp .env.example .env

composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan jwt:secret
```

Signin Admin User<br>
email : admin@glimpsemedia.co<br>
password: password
<br><br> 
Manager User <br>
email : eleanor.armstrong@glimpsemedia.co <br>
password: password
<p>In the databse the roles(admin, manager, user) has been seeded</p>

###  API Public Collection

    Access the Postman collection link below: 
   https://api.postman.com/collections/39268478-e0c7afbf-ce19-4a61-8a1b-763a81d36ed7?access_key=PMAT-01JSRYK6A1N44T0HR6FY3MBGR7


### Run Tests

```bash
php artisan test
```






