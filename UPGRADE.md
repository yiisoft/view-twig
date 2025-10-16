# Upgrading Instructions for Yii View Twig

This file contains the upgrade notes. These notes highlight changes that could break your
application when you upgrade the package from one version to another.

> **Important!** The following upgrading instructions are cumulative. That is, if you want
> to upgrade from version A to version C and there is version B between A and C, you need
> to following the instructions for both A and B.

## Upgrade from 2.x

- Update usages of `ViewRenderer` to `TwigTemplateRenderer`.
- `YiiTwigExtension` was removed. Consider using third party extensions instead or creating your own.
