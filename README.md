# Commission Calculator

## Requirements
* PHP >= 8.2
* Composer
* Api Key for BIN Checker
``https://apilayer.com/marketplace/bincheck-api``
* Api Key for Exchange Rates
``https://www.exchangerate-api.com/``

## How to Install
* Run in command ``composer install``
* Copy *.env.dist* as *.env.local* or *env.prod*
* Write both API keys for services

## Usage
``bin/console app:calculate input.txt``
App will load input.txt file and calculate commission

## Testing
* Copy *.env.dist* as *env.test*
* Write both API keys for services
* Run in command ``php bin/phpunit``