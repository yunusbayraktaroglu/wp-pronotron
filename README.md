# WP Pronotron WordPress Plugin

WP Pronotron is a modular WordPress plugin that I have written. It includes features which I have integrated into almost every website that I build.

[![Build WP Pronotron Plugin](https://github.com/yunusbayraktaroglu/wp-pronotron/actions/workflows/ci.yml/badge.svg)](https://github.com/yunusbayraktaroglu/wp-pronotron/actions/workflows/ci.yml)

### Modules

#### Art direction images module
<i>Why? Having multiple orientations in your image pipeline has benefits for design and page load times.</i>
- Create custom image sizes based on ratios via admin UI (landscape and portrait ratios).
- Crop images with defined image aspect ratios.
- Auto-generates `<picture>...sources</picture>` media items with applied media orientation

![Art direction images](https://github.com/yunusbayraktaroglu/wp-pronotron/blob/main/manual/.art-direction-images-low.jpg)

#### Auto metafields module
<i>Why? Every business has different types of posts, and each post type needs different meta.</i>
- Create metafields (date,image,string,number,...) for defined post types with WordPress filters

#### Image carousels module
<i>Why? WordPress lacks simple carousel functionality.</i>
- Adds functionality to default gallery blocks to have a data attribute for display on the front-end.
