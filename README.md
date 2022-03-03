# kw_auth_forms

Authenticate that the data in form are valid and unchanged from author. Nice for checking
when you need pass sensitive data.

Every check is done by user's certificates and with user's salt.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/kw_auth_forms": "1.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Add some external packages with connection to the local or remote services.

3.) Connect the "kalanis\kw_auth_forms\AuthForm" into your app forms for your case.

4.) Just call forms and render, validity of queries will be checked on return

