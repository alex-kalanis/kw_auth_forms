# kw_auth_forms

![Build Status](https://github.com/alex-kalanis/kw_auth_forms/actions/workflows/code_checks.yml/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_forms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_forms/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_auth_forms/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_auth_forms)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_auth_forms.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_auth_forms)
[![License](https://poser.pugx.org/alex-kalanis/kw_auth_forms/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_auth_forms)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_forms/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_auth_forms/?branch=master)

Authenticate that the data in form are valid and unchanged from author.
Nice for checking when you need to pass data sensitive on changes.

Every check is done by user's certificates and with user's salt. Every
check also can say if that user can use his certificates to check the data.

## PHP Installation

```bash
composer.phar require alex-kalanis/kw_auth_forms
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Add some external packages with connection to the local or remote services.

3.) Connect the "kalanis\kw_auth_forms\AuthForm" into your app forms for your case.

4.) Just call forms and render, validity of queries will be checked on return

