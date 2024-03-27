#!/usr/bin/env bash

if [ -z "$1" ]; then
    NODE_MAJOR=20
else
    NODE_MAJOR="$1"
fi

# Update package list and install required dependencies
apt-get update && apt-get install -y ca-certificates curl gnupg

# Add Node.js repository and its GPG key
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /usr/share/keyrings/nodesource.gpg
echo "deb [signed-by=/usr/share/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list

# Update package list again and install Node.js
apt-get update && apt-get install -y nodejs