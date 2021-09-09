<p align="center">
    <h1 align="center">
        DbIpClient
    </h1>
</p>
<br>

Modern client library for the db-ip.com API services


## Getting Started

First of all, you need to define an add the API client in your project.
`composer require scullwm/dbip-client`



```php
    $dbipClient = new Client('my_secret_token');
    $ipDetails = $dbipClient->getIpDetails('8.8.8.8');
    $ipDetails->isRisky(); // false

    $apiStatus = $dbipClient->getApiStatus();
    echo $apiStatus->getQueriesLeft(); // 9996
```

### Running the Test Suite

Once you have all dependencies installed via `composer install`, you can run the test suite with:

```bash
./vendor/bin/phpunit tests/
```