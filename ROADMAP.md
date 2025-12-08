# Roadmap

Links:
- Issues: https://github.com/Kehet/imagick-layout-engine/issues
- Discussions: https://github.com/Kehet/imagick-layout-engine/discussions
- Docs: https://kehet.github.io/imagick-layout-engine-docs

## Vision

Provide a small, predictable, and composable layout system for images, inspired by flexbox suitable for labels, stickers, and status screens.

### Layout features
- `GridContainer`
- `gap`
- `justify-content`, `align-content`
- `align-items`, `align-self`
- `justify-items`, `justify-self`
- `width`, `max-width`, `min-width`
- `height`, `max-height`, `min-height`

### Text features
- Text multiline wrapping improvements: word/char boundaries, soft hyphen support
- line height, letter spacing, paragraph spacing
- Ellipsize (end/middle) with max lines
- Font fallback chain, emoji support
- Caching text measurements
- Rich text features: per span font, size, weight, color, underline/strike (markdown parsing?)

### API
- Better `DrawableInterface`
- Performance optimizations
- Templates as json / yaml
- CLI tool to render templates
- Laravel bridge (config, facade)

### Project management
- MIT license?
- Name change?

## Non‑goals (for now)
- Full CSS rendering / browser engine parity
- Perfect cross‑platform pixel parity
- Animated output
