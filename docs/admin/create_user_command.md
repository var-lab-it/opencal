# Create a User via Command Line

You can create a new user using the CLI command below.

# Command Syntax

```
Usage:
  opencal:user:create <email> <given_name> <family_name> [<password>]
```

Example:

```bash
make backend.sh
php bin/console opencal:user:create example@email-domain.com Example User # without password
or
php bin/console opencal:user:create example@email-domain.com Example User 1s3cur3! # with password
```

### Parameters
- `<email>` – The user’s email address (used for login).
- `<given_name>` – The user’s first name.
- `<family_name>` – The user’s last name.
- `[<password>]` – (Optional) The user’s password.

### ⚠️ Important Notes
- If no password is provided, the user will be required to reset his password before the first login.
- The email address must be unique.
