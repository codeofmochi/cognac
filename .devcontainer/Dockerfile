FROM mcr.microsoft.com/devcontainers/php:1-8.2-bookworm

# Add Node apt repository
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -

# Add symfony CLI repository
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash

# Add httpie apt repository
RUN curl -SsL https://packages.httpie.io/deb/KEY.gpg | gpg --dearmor -o /usr/share/keyrings/httpie.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/httpie.gpg] https://packages.httpie.io/deb ./" | tee /etc/apt/sources.list.d/httpie.list > /dev/null

# Add gh apt repository
RUN mkdir -p -m 755 /etc/apt/keyrings \
    && wget -qO- https://cli.github.com/packages/githubcli-archive-keyring.gpg | tee /etc/apt/keyrings/githubcli-archive-keyring.gpg > /dev/null \
    && chmod go+r /etc/apt/keyrings/githubcli-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | tee /etc/apt/sources.list.d/github-cli.list > /dev/null

# Install additional OS packages
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install \
    curl \
    fish \
    gh \
    httpie \
    mariadb-client \
    nodejs \
    libxslt1-dev \
    symfony-cli \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql xsl

# Set fish as the default shell for the devcontainer user
RUN echo "/usr/bin/fish" >> /etc/shells \
    && chsh -s /usr/bin/fish vscode

# Install starship shell prompt
RUN curl -sS https://starship.rs/install.sh | sh -s -- --yes

# Run following commands as unprivileged user
USER vscode
# cd to home directory
WORKDIR /home/vscode

# Install composer
RUN EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')" \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")" \
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ] \
    then \
    >&2 echo 'ERROR: Invalid installer checksum' \
    rm composer-setup.php \
    exit 1 \
    fi \
    php composer-setup.php --quiet \
    RESULT=$? \
    rm composer-setup.php \
    exit $RESULT

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

# Add git bash completions
RUN grep -qxF 'source /usr/share/bash-completion/completions/git' .bashrc || echo 'source /usr/share/bash-completion/completions/git' >> .bashrc
# Prevent "dubious ownership" git error message
RUN git config --global --add safe.directory /workspace

# Set starship at fish init
RUN mkdir -p /home/vscode/.config/fish \
    && echo 'starship init fish | source' >> /home/vscode/.config/fish/config.fish