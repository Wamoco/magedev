# Magedev

Magedev is a shell tool for local Magento development. It helps you to get a project up and running much easier by automating cumbersome tasks. Escpecially, Magento2 is not easy to setup and install all required dependencies.

## Requirements

* Linux
* PHP 5.6 or above
* docker 1.10 or above

Magedev uses Docker to orchestrate required services in containers. Make sure you have Docker installed on your system. You may use the official install script:

    curl -sSL https://get.docker.com/ | sh

Or with wget:

    wget -qO- https://get.docker.com/ | sh

## Installation

Magedev is a command line tool. Download latest version with download script or grab it from the `releases` folder.

    curl -sSL https://raw.githubusercontent.com/Wamoco/magedev/master/download-latest.sh | sh

Make sure magedev lies in your `PATH`, move it somewhere e.g. `~/bin`:

    mv magedev ~/bin

## Updating magedev

To update your copy of magedev, you may use the `self-update` command like this:

    magedev self-update

## Usage

See [the documentation](docs/index.md).

## Magento2 Installation at warp speed

    git clone https://github.com/magento/magento2.git && cd magento2

```
cat >magedev.json <<'EOL'
{
  "magento_version": "2",
  "domain": "magento2.local"
}
EOL
```
    magedev docker:start
    magedev magento:install
    curl magento2.local


## Team

Agency for Magento and Web Development based in Bremen, Germany.

Feel free to contact us: <a href="mailto:info@wamoco.de?subject=Watchimon">info@wamoco.de</a>

[wamoco.de](https://wamoco.de)

## License

[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright 2021 © <a href="https://wamoco.de" target="_blank">Wamoco</a>.
