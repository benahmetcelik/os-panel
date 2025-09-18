<?php



function nginxAvailableConfigPath($domain)
{
    return "/etc/nginx/sites-available/{$domain}.conf";
}

function nginxEnabledConfigPath($domain)
{
    return "/etc/nginx/sites-enabled/{$domain}.conf";
}
