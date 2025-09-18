# shellcheck disable=SC2034
export PHP_VERSION="8.1"
# shellcheck disable=SC2155
export SERVER_IP=$(hostname -I | awk '{print $1}')
