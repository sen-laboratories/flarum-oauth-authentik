# Authentik OAuth for Flarum 2.0

A first-class bridge between [Authentik](https://goauthentik.io) and Flarum 2.0.

This extension is used in production at [The Onsen](https://onsen.sen-labs.org) and is optimized for Docker environments.

## Features
- **OIDC Native**: Seamless integration with Authentik's OpenID Connect provider.
- **Internal Loopback Fix**: Dedicated field for internal backend communication, bypassing NAT/Loopback issues in Docker/Cloudflare setups.
- **Username Sanitization**: Automatically converts dots to dashes to comply with Flarum's strict username requirements.

## Configuration
1. Install via Composer (once listed) or add to your local extension path.
2. In Flarum Admin > FoF OAuth, configure the Authentik provider:
   - **Client ID / Secret**: From your Authentik application.
   - **External Base URL**: Your public Authentik URL (e.g., `https://auth.example.com`).
   - **Internal Base URL**: (Optional) Your internal Docker/IP URL to bypass external proxies during the OIDC handshake.
