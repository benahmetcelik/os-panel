
echo "📦 Installing Git if not present..."
if ! command -v git &> /dev/null; then
    apt install -y git
    echo "✅ Git installed."
else
    echo "✅ Git already installed."
fi
