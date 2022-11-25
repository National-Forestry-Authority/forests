
# You can copy this Dockerfile.example to Dockerfile to add configuration
# or packages or anything else to your webimage
ARG BASE_IMAGE
FROM $BASE_IMAGE

RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    cat /etc/apt/sources.list.d/pgdg.list && \
    curl --silent https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
    apt-get update && \
    apt-get install --no-install-recommends -y postgresql-client-12 && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN apt-get update \
    && apt-get install -y php-geos \
    && apt-get install -y php-bcmath
