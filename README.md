# [API Mock Server](https://github.com/albertleng/AethonAPIMockServer)

API Mock Server used to test fictional endpoints `/go`, `/job`, `/destination`, `/status`, and `/pause` by returning responses with randomized data.

# Requirements:
[php 7++](www.php.net)

[Slim Microframework 2](http://www.slimframework.com/docs/v2/)

[Composer](https://getcomposer.org/)

## Installation

Install Composer. Refer to [documentation](https://getcomposer.org/).


Install Slim Microframework 2 via Composer
```bash
composer require slim/slim:~2.0
```

## Usage

```bash
git clone https://github.com/albertleng/AethonAPIMockServer.git <folder-name>
```

```
 php -S 0.0.0.0:8080 -t <folder-name> <folder-name>/index.php
```

Start making REST requests via <ip-address>:8080/ from another machine in the same network.<endpoint>
