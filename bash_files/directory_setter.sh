
if [ ! -d "/var/www/panel" ]; then
    echo "Warning: /var/www/panel directory does not exist."
    mkdir -p /var/www/panel
    echo "/var/www/panel directory created."
else
    echo "⚠️  /var/www/panel directory exists."
    echo "📁 Contents:"
    ls -la /var/www/panel/ 2>/dev/null || echo "   (Directory is empty or inaccessible)"
    echo ""

    echo "🗑️  Removing existing files..."
    rm -rf /var/www/panel/*
    rm -rf /var/www/panel/.[^.]* 2>/dev/null || true
    echo "✅ Files removed successfully."
fi


