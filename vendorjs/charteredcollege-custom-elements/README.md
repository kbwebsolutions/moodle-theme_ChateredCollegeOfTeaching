# charteredcollege Custom Elements

This project holds charteredcollege custom elements which are used by the charteredcollege theme.

## Adding a new element

```bash
ng g component <component name> --inline-style --inline-template
```

The generated element will be found in:
`theme/charteredcollege/vendorjs/charteredcollege-custom-elements/src/app/<component name>`

## Building the library for use with the charteredcollege theme

```bash
npm run test-and-build
```

This will generate:

* `theme/charteredcollege/vendorjs/charteredcollege-custom-elements/charteredcollege-ce.js`.
* `theme/charteredcollege/vendorjs/charteredcollege-custom-elements/charteredcollege-ce-es5.js`.
