# Configuration

| Variable                  | Description                                     | Default Value                           |
|---------------------------|-------------------------------------------------|-----------------------------------------|
| `LOCALE`                  | The locale.                                     | `en_GB`                                 |
| `EMAIL_SENDER_ADDRESS`    | The e-mail address from which e-mails are sent. | `mail@example.tld` (please change this! |
| `EMAIL_SENDER_NAME`       | The name of the sender of e-mails               | `OpenCal`                               |
| `MAILER_DSN`              | Mailer configuration                            | `smtp://mailer:1025` (MailPit)          |
| `MESSENGER_TRANSPORT_DSN` | Messenger transport configuration               | `doctrine://default?auto_setup=0`       |
| `FRONTEND_DOMAIN`         | Domain of the frontend.                         | `localhost`                             |
| `USE_SSL`                 | If true, https will be used.                    | `true`                                  |
