# AIDAR API SERVICES
This project contains backend APIs written in PHP that powers the AIDAR mobile app.

The services are contained in docker containers hence to run an instance of the project, you are expected to 
have docker and docker-compose installed on you device.

Visit the official Docker website for instructions on how to install docker and docker-compose.

Now clone the project: 

`git clone git@github.com:ZuberiPay/aidar-services.git`

`cd aidar-services`

Make of copy of the `.env.example` file and name if `.env`.

The `.env` file contains environment variables that are required to properly config the various services especially the ports and DB settings per service.
You are advised to update it according to the settings or ports available on your machine.

Kindly note that every service has it own `.env.example` and `.env`. They are not the same as the one in the project root.
That said, you will have to make sure you mirror settings that are in the root `.env` with the appropriate service.

For instance, after setting the DB credentials for auth service in the root `.env` file, you have to update the DB credentials in the `auth/.env`, since
 the variables in the root `.env` is to configure docker contain at build time and not to updated the actual services and their environment variables.

After all params have been set, run `docker-compose up --build`. This command will build all containers using the set fo commands found in the Dockerfile in the root of 
the each. 

## Sending Requests

To send request to any of the services, all you need to do is to send the request to the proxy server (already configured).

Assuming you are running the app with the default settings in the .env.example, then you will be sending all http request to `http://localhost:4000` then you append the service name eg. auth, vendor etc as show below,  to send a login request to the auth service, the url will look like this: 

`http://localhost:4000/auth/api/login`

To fetch products from the vendor service will require a url like:

`http://localhost:4000/vendor/api/products`

Closely observing the examples above, you notice that the pattern for the urls is: [base/hostname][service][endpoint]

*DO NOT TOUCH OR UPDATE THIS FILE*

To run any terminal command against any service, run

`docker-compose exec [service name] [command]`

So given I want to run migrations in the auth service, my command will be

`docker-compose exec auth php artisan migrate`

Another example will be if I want to tinker in the vendor service, then my command will look like this

`docker-compose exec vendor php artisan tinker`

Basically, the last parts of the commands are the usual artisan or terminal commands that you are familiar with. The beginning of the command
just instructs docker-compose to run the command again a specific service.

To stop containers from running, just run: `docker-compose down`. This will stop all containers and make available the resources they use eg, ports.
