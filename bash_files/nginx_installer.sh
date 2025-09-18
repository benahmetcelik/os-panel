
echo "🌐 Installing Nginx..."
apt install -y nginx nginx-common

echo "🚀 Starting Nginx service..."
systemctl start nginx
systemctl enable nginx
