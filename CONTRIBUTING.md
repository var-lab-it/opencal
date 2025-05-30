# Contributing to OpenCal

Thank you for your interest in contributing to OpenCal! 🎉  
Your contributions help make this project better for everyone.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [How to Contribute](#how-to-contribute)
- [Development Guidelines](#development-guidelines)
- [Commit Message Convention](#commit-message-convention)
- [License](#license)

---

## Code of Conduct

This project adheres to a [Code of Conduct](https://www.contributor-covenant.org/version/2/1/code_of_conduct/).  
By participating, you are expected to uphold this code. Please report any unacceptable behavior
to [contact email or GitHub issues].

---

## Getting Started

See [Getting Started (For Developers)](docs/dev_setup.md).

---

## How to Contribute

You can contribute in many ways:

- Reporting bugs or vulnerabilities
- Fixing issues (check the [issue tracker](https://github.com/var-lab-it/opencal/issues))
- Improving documentation
- Adding new features
- Improving tests or test coverage
- Reviewing and commenting on open pull requests

---

## Development Guidelines

- Fork this repository
- Follow the coding style used throughout the project
- Make sure all tests pass before submitting a pull request
- Add or update tests when introducing changes
- Update documentation if applicable
- Keep your pull request focused and avoid unrelated changes

To run tests:

```bash
make api.sh
# and then
composer run tests
```

To run phpstan:

```bash
make api.sh
# and then
composer run phpstan
```

To run PHP linter:

```bash
make api.sh
# and then
composer run lint:php
composer run lint:php:fix
```

## License

By contributing, you agree that your contributions will be licensed under the [GNU AGPLv3 License](./LICENSE).

Thank you for your contribution!
