# Wubbleyou\Wubblestan

A simple set of prebuilt rules for PHPStan/Larastan.

### Setup

Simply install this as you would any other composer package:
`composer require --dev "wubbleyou/wubblestan:dev"`

##### Note: this isn't actually published to composer yet

For now check the [Composer Docs](https://getcomposer.org/doc/05-repositories.md#using-private-repositories) for installing from this git repo instead.

#### Neon Config

Add the class definition to your services, this should be in a `phpstan.neon` file in your project root. See [Larastan - Getting Started](https://github.com/larastan/larastan?tab=readme-ov-file#-getting-started-in-3-steps) if you're confused.

```
services:
    - class: Wubbleyou\Wubblestan\Rules\RULE_CLASS
      tags:
        - phpstan.rules.rule
```

#### Whitelisting

To do (sorry)

### AuthorisationInController

This rule scans all controller methods and fails whenever one is detected without any authorisation - essentially checks if `$this->authorize(...)` is present.

### StandardRequestInController

This rule scans all controller methods to make sure the standard Laravel request class is not being used. We should _always_ be creating a dedicated request class extending `FormRequest`.
Any method using `Illuminate\Http\Request` **will fail**.

### ModelsInController

This rule scans all controller methods to make sure you're not using/accessing models directly within a controller. For an example the following is forbidden:

-   `$user = User::find(2);`
-   `User::create([...])`

### BusinessLogicInController

This rule scans all controller methods to make sure no business logic is present - we should be using services instead. Currently it checks for usage of:

-   `if`
-   `for`
-   `foreach`
-   `while`
-   `switch`

### MaxLengthController

This rule scans all controller methods to make sure none of them exceed a limit of 20 lines in length.
