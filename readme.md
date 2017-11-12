Leftaro, a micro Web Framework
=============================

PSR-7, PSR-11 and PSR-15 compliant micro framework for PHP 7.

Get ready
--------------

- `composer install``
- Copy the folder config/base into config/local
- Update the settings
- Run the local server for development with `php bin/server.php``
- Try to run the example static routes defined in `config/local/routes.php`
  - http://0.0.0.0/html
  - http://0.0.0.0/html/10
  - http://0.0.0.0/text
  - http://0.0.0.0/json
- Try to run the smart-discover endpoints like:
  - http://0.0.0.0:8000/smart/10/discover/100/resource/my-resource-id

Current status
--------------

- [x] Add PSR-15 middleware approach
- [x] Add PSR-7 Http library
- [x] Add PSR-11 Container
- [x] Add configuration loader class
- [x] Add built-in server for development mode
- [x] Add FastRoute for fixed routes by file
- [x] Add automatic route resolver by URI/Controller
- [ ] Add utility commands to
  - [ ] Migrate database
  - [ ] Create an empty project

