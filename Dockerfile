# Use Ubuntu as the base image
FROM ubuntu:latest

# Label the image
LABEL authors="jerem"

# Update the package repository and install packages
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-mysql \
    mysql-server \
    curl \
    libcurl4-openssl-dev \
    php-curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*


# Copy your PHP application to the container
COPY . /var/www/html/

# Change ownership of the html directory to the Apache user
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Copy the entrypoint script into the container
COPY entrypoint.sh /usr/local/bin/

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Use the entrypoint script to start services
ENTRYPOINT ["entrypoint.sh"]
