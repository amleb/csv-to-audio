#!/usr/bin/env bash

echo "Installing dependencies..."
composer install -q --no-dev --no-interaction

if [ $? -eq 0 ]; then

    echo "Creating file..."
    php create-phar.php

    if [ $? -eq 0 ]; then
        echo "Done."
        echo "Copy ./build/csv-to-audio.phar to /usr/local/bin/csv-to-audio"
        echo "(sudo cp -f ./build/csv-to-audio.phar /usr/local/bin/csv-to-audio && sudo chmod a+x /usr/local/bin/csv-to-audio)"
    fi
fi
