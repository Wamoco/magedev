# MariaDB

By default magedev runs the database service with mysql. MariaDB can be used
instead by switching the `db_engine` in your project's `magedev.json`:

    {
      ...
      "db_engine": "mariadb"
    }

For a new project that is all there is to it - `magedev docker:start` builds the
mariadb container. A project that already runs keeps its existing database
container, see [switching an existing project](#switching-an-existing-project).

The container is still named `mysql`, so the hostname magento and all other
services connect to does not change. Nothing about the magento configuration
(`--db-host=mysql`), `magedev docker:cli:mysql` or the port forwarding for
`3306` has to be touched.

## Choosing the version

Any tag of the official [mariadb image](https://hub.docker.com/_/mariadb) can be
selected with `mariadb_version`:

    {
      ...
      "db_engine": "mariadb",
      "mariadb_version": "11.4"
    }

When `mariadb_version` is omitted, `10.11` is used. Pick the version your magento
release supports - Adobe lists MariaDB `10.11` and `11.8` for 2.4.7-p10, and
`11.4` respectively `11.4`/`11.8` for 2.4.8-p4 and p5. Older releases like `10.4`
and `10.6` still work with magedev but have dropped out of that matrix, so check
[Adobes system requirements](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/system-requirements)
for your version.

After changing the version rebuild the container:

    magedev docker:reinit

## Data directory

MariaDB refuses to start on a data directory that was created by mysql (and the
other way around), so each engine keeps its data separate. With
`"db_engine": "mariadb"` the data lives in:

    <project>/mariadb

Add it to your project's `.gitignore`:

    /mariadb

Your old mysql data is untouched in `<project>/mysql`, so switching back is just
another `db_engine` change.

Slow queries (longer than one second) are logged to `<project>/mariadb/slow.log`.

## Switching an existing project

`magedev docker:start` only builds containers that do not exist yet, so an
already running project keeps its old database container after changing
`db_engine`. Recreate just the database container:

    magedev docker:stop
    docker rm magedev-<project>-mysql
    magedev docker:start

`magedev docker:reinit` works too, but destroys and rebuilds the images of all
containers - including the main one, which takes a while.

The new engine starts with an empty database, so import your dump again:

    magedev db:import

MariaDB cannot take over a mysql data directory: the redo log format and the
`mysql` system schema differ, and there is no supported in place upgrade path
from mysql to mariadb. A logical dump is the only way over.

## Server configuration

The server settings are shared by all mariadb versions and live in
`var/Docker/mysql/mariadb.cnf`. Like every other service config file it can be
replaced per project (see [configuration](configuration.md)):

    .magedev/var/Docker/mysql/mariadb.cnf

If you only want to change the settings of one version, use a version specific
file - it wins over the shared one:

    .magedev/var/Docker/mysql/mariadb-11.4.cnf

## Notes

* The official mariadb image understands the same `MYSQL_*` environment
  variables as the mysql image, so user, password and database stay `magento`.
* The `migration` container (used for magento 1 to 2 migrations) is not affected
  by `db_engine` and keeps running mysql.
