    docker build -t bleers/deploy .

docker run --rm -it bleers/deploy

docker run --rm -it --entrypoint /bin/ash bleers/deploy

    docker push bleers/deploy

