# Troubleshooting

If you have problems start by appending `-v` to your command to see commands that are executed by magedev.

# Errors in docker

Sometimes you may encounter problems which are hidden inside the docker service.

    [Http\Client\Common\Exception\ClientErrorException (404)]
    Not Found

You may access more information in your journal like this.

    journalctl -u docker.service

# Known Issues

## Grunt is not killed

When using `magedev grunt:watch` and exiting with `ctrl+c` the process will remain active in the container. You cannot start watch again, it will fail with:

    Fatal error: Port 35729 is already in use by another process.

As a workaround, you may use `magedev grunt:kill` in this case, to stop the process and start again.

## Reindex fails with "create-index blocked"

    Catalog Search index process error during indexation process:
    {"error":{"root_cause":[{"type":"index_create_block_exception","reason":
    "blocked by: [FORBIDDEN/10/cluster create-index blocked (api)];"}]...

Elasticsearch and OpenSearch block index creation cluster wide as soon as the
disk fills up past the high watermark of 90%, which a dev machine reaches easily.
Magedev therefore starts the search container with
`cluster.routing.allocation.disk.threshold_enabled=false`. If you still see this
error, the block is left over in the cluster state from before - release it:

    curl -XPUT localhost:9200/_cluster/settings -H 'Content-Type: application/json' \
      -d '{"persistent":{"cluster.blocks.create_index":null}}'

## Local services

This setup assumes you have no services like apache or mysql running on your host. All required services will be started inside containers and default ports will be forwarded to your host machine. Thats why your start command may fail with error like this:

    Error starting userland proxy: listen tcp 0.0.0.0:3306: bind: address already in use

Normally, magedev takes care to shut down these services if required. In case you have local services installed, you may use a bash alias in your `~/.bash_aliases` to stop them before

    alias stopall='service apache2 stop && service mysql stop'
