SUDOERS_FILE="/etc/sudoers.d/os-panel"


echo "🔎 Checking sudoers config..."

# Eğer dosya yoksa oluştur
if [ ! -f "$SUDOERS_FILE" ]; then
    echo "⚠️  $SUDOERS_FILE does not exist."
    echo "📝 Creating sudoers file..."

    cat <<EOL | sudo tee $SUDOERS_FILE > /dev/null
www-data ALL=(ALL) NOPASSWD: /bin/cp, /bin/ln, /usr/sbin/nginx, /bin/systemctl
EOL

    echo "✅ Sudoers file created at $SUDOERS_FILE"
else
    echo "⚠️  $SUDOERS_FILE already exists."
    echo "📄 Contents:"
    sudo cat $SUDOERS_FILE
    echo ""

    echo "🗑️  Removing old sudoers file..."
    sudo rm -f $SUDOERS_FILE
    echo "📝 Writing new sudoers file..."

    cat <<EOL | sudo tee $SUDOERS_FILE > /dev/null
www-data ALL=(ALL) NOPASSWD: /bin/cp, /bin/ln, /usr/sbin/nginx, /usr/bin/systemctl /usr/bin/certbot
EOL

    echo "✅ Sudoers file updated successfully."
fi


echo "🔍 Validating sudoers syntax..."
sudo visudo -c -f $SUDOERS_FILE

