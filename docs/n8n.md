# n8n

[n8n](https://n8n.io) is an optional workflow automation tool that can be spun
up alongside your Magento environment. It is **disabled by default** and only
started when you activate it in your `magedev.json`.

## Enable n8n

Add this to your project's `magedev.json`:

    {
      ...
      "n8n": true
    }

Then (re)start your environment:

    magedev docker:start

n8n is now reachable at <http://localhost:5678>.

When you change the configuration of an already running project, apply it with:

    magedev docker:reinit

## Data persistence

All of n8n's moving data — workflows, credentials, the encryption key and the
sqlite database — lives in `/home/node/.n8n` inside the container. Just like the
`mysql` data directory, this is bind-mounted into your project folder:

    <project>/.n8n-data

The directory is created automatically on first start and owned by your host
user. You should add it to your project's `.gitignore`:

    /.n8n-data

Deleting `.n8n-data` resets n8n to a clean state.

## Choosing the n8n version

By default the `latest` n8n image is used. To pin a specific version, set
`n8n_version` (the value is used as the docker image tag of `n8nio/n8n`):

    {
      ...
      "n8n": true,
      "n8n_version": "1.70.0"
    }

After changing the version rebuild the container:

    magedev docker:reinit

## Changing the port

n8n listens on port `5678`, which is forwarded to the same host port by
default. To use a different host port, override it in your `docker.ports`
config:

    "docker": {
      "ports": {
        "n8n": {
          "5678": "5680"
        }
      }
    }

## Linking n8n with Magento

If you want Magento (the `main` container) to reach n8n — or n8n to reach the
project's services — add the appropriate links in your `magedev.json`:

    "docker": {
      "links": {
        "main": ["mysql", "redis", "elasticsearch", "n8n"]
      }
    }

n8n is then reachable from the `main` container under the hostname `n8n`.

## Notes

* `N8N_SECURE_COOKIE` is set to `false` so you can log in over plain http on a
  custom dev host. This is fine for local development but should never be used
  in production.
* n8n uses its own embedded sqlite database by default, so no extra database
  configuration is required.
