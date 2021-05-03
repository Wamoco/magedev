# Installation

## Docker Installation guide

### Ubuntu 20.04

    curl -sSL https://get.docker.com/ | sh

After the script completes, *DO NOT* use rootless mode. Follow these [instruction](https://docs.docker.com/go/daemon-access/) instead to to run Docker daemon as a fully privileged service. Otherwise permission mess.

## Installation

For using the latest dev version in this repository, first clone it and create a symlink for the executable `bin/magedev`:

    git clone https://github.com/bka/php-cli-magedev.git && cd php-cli-magedev && git checkout develop
    composer install --no-dev
    ln -s $(pwd)/bin/magedev ~/bin/magedev
    magedev
