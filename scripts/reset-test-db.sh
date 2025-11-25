#!/bin/bash
rm var/data_test.db
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test --no-interaction
echo "SQLite test database reset ✔"