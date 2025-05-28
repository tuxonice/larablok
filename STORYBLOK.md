# Storyblok Configuration for LaraBlok

This document explains how to set up your Storyblok space to work with the LaraBlok application.

## Prerequisites

1. A Storyblok account (you can sign up at [storyblok.com](https://www.storyblok.com/))
2. A Storyblok space for your blog content

## Content Structure

LaraBlok expects a specific content structure in Storyblok to function properly:

### Content Types

#### Article

Create an "Article" content type in Storyblok with the following fields:

| Field Name      | Field Type      | Description                                  |
|-----------------|-----------------|----------------------------------------------|
| `title`         | Text            | The title of the article                     |
| `teaser`        | Text            | A short summary of the article               |
| `content`       | Rich Text       | The main content of the article              |
| `featured_image`| Asset           | The featured image for the article           |
| `categories`    | Multi-Options   | Categories or tags for the article           |
| `author`        | Text            | The author of the article                    |

### Field Configuration

1. **Categories Field**:
   - Create a Multi-Options field
   - Add your desired categories (e.g., Technology, Travel, Food, etc.)
   - Make sure the field is named `categories`

2. **Featured Image**:
   - Create an Asset field named `featured_image`
   - Allow only images

## API Setup

1. Go to your Storyblok space settings
2. Navigate to the "API Keys" section
3. Create a new API key with "Preview" access if you want to see draft content, or "Public" for published content only
4. Copy the API key

## LaraBlok Configuration

1. Open your `.env` file
2. Set the following variables:
   ```
   STORYBLOK_API_KEY=your_api_key_here
   STORYBLOK_VERSION=published  # or 'draft' for preview mode
   STORYBLOK_CACHE_DURATION=3600  # Cache duration in seconds
   ```

## Warming the Cache

LaraBlok includes a command to warm the cache with Storyblok content:

```bash
# Clear and warm the cache
php artisan storyblok:warm-cache --clear

# Only warm the cache (without clearing)
php artisan storyblok:warm-cache
```

This is useful for production environments to ensure fast page loads.

## Content Management

1. Create articles in your Storyblok space
2. Publish them when ready
3. The LaraBlok application will automatically fetch and display your content

## Webhooks (Optional)

For automatic cache invalidation, you can set up a webhook in Storyblok:

1. Go to your Storyblok space settings
2. Navigate to the "Webhooks" section
3. Create a new webhook pointing to your application's cache clear endpoint
4. Configure it to trigger on "Story Published" and "Story Unpublished" events

## Troubleshooting

If your content isn't appearing:

1. Check that your API key is correct
2. Ensure your content follows the expected structure
3. Verify that the articles are published (if using 'published' version)
4. Try clearing and warming the cache
5. Check the Laravel logs for any errors

For more information, refer to the [Storyblok API documentation](https://www.storyblok.com/docs/api/content-delivery).
