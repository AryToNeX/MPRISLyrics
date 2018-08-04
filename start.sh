#!/bin/bash

DIR="$(cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
cd "$DIR"
PHP_BINARY=$(type -p php)

if [ -f ./MPRISLyrics.phar ]; then
	MPRISLYRICS_FILE="./MPRISLyrics.phar"
elif [ -f ./src/AryToNeX/MPRISLyrics/Loader.php ]; then
    MPRISLYRICS_FILE="./src/AryToNeX/MPRISLyrics/Loader.php"
else
	echo "Couldn't find a valid MPRISLyrics file!"
	exit 1
fi

exec "$PHP_BINARY" "$MPRISLYRICS_FILE" $@