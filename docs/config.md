# Configuration

Both backend and frontend Docker containers can be customized using environment variables.

## Frontend Environment Variables

| Variable            | Description                                           | Default Value           |
|---------------------|-------------------------------------------------------|-------------------------|
| `VITE_APP_LANGUAGE` | Defines the default language/locale for the frontend. | `en_GB`                 |
| `VITE_API_HOST`     | Specifies the backend API's base URL.                 | `http://localhost:8080` |
| `VITE_LOGO_URL`     | URL to your logo.                                     | <empty>                 |

## Backend/API Environment Variables

| Variable                  | Description                                     | Default Value                           |
|---------------------------|-------------------------------------------------|-----------------------------------------|
| `LOCALE`                  | The locale.                                     | `en_GB`                                 |
| `EMAIL_SENDER_ADDRESS`    | The e-mail address from which e-mails are sent. | `mail@example.tld` (please change this! |
| `EMAIL_SENDER_NAME`       | The name of the sender of e-mails               | `OpenCal`                               |
| `MAILER_DSN`              | Mailer configuration                            | `smtp://mailer:1025` (MailPit)          |
| `MESSENGER_TRANSPORT_DSN` | Messenger transport configuration               | `doctrine://default?auto_setup=0`       |
| `FRONTEND_DOMAIN`         | Domain of the frontend.                         | `localhost`                             |
| `USE_SSL`                 | If true, https will be used.                    | `true`                                  |
