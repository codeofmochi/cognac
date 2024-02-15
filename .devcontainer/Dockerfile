FROM mcr.microsoft.com/devcontainers/php:1-8.2-bookworm

# Install MariaDB client
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y mariadb-client \ 
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install php-mysql driver
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN sudo apt install symfony-cli

# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

# Run following commands as unprivileged user
USER vscode
# cd to home directory
WORKDIR /home/vscode

# Add git bash completions
RUN grep -qxF 'source /usr/share/bash-completion/completions/git' .bashrc || echo 'source /usr/share/bash-completion/completions/git' >> .bashrc
# Prevent "dubious ownership" git error message
RUN git config --global --add safe.directory /workspace