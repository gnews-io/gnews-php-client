# GNews.io PHP Client

A PHP client for the GNews.io API, designed to be simple and easy to use.

## Documentation

- [GNews.io API Documentation](https://gnews.io/docs/v4#introduction)

## Installation

```bash
composer require gnews-io/gnews-php-client
```

## Usage

### Quick Examples

```php
$gnews = new Gnews('YOUR_API_KEY');

$searchResults = $gnews->searchArticles('bitcoin');
$headlines = $gnews->getHeadlines(['category' => 'technology']);
```

## API Methods

### Search Articles

Search for articles with a specific query.

```php
$gnews = new Gnews('YOUR_API_KEY');

$results = $gnews->search('bitcoin', [
    'lang' => 'en',
    'country' => 'us',
    'max' => 10,
    'in' => 'title,description',  // Where to search (title,description,content)
    'nullable' => null, // Specify the attributes that you allow to return null values
    'sortby' => 'publishedAt',  // or 'relevance'
    'from' => '2025-01-01T00:00:00Z',
    'to' => '2025-01-31T23:59:59Z',
    'page' => 1, // Paid plan only
    'expand' => 'content', // Paid plan only : get the full content of the article
]);
```

### Headlines

Get top headlines, optionally filtered by category.

```php
$gnews = new Gnews('YOUR_API_KEY');

$headlines = $gnews->headlines([
    'category' => 'technology',  // Optional: general, world, nation, business, technology, entertainment, sports, science, health
    'lang' => 'en',
    'country' => 'us',
    'max' => 10,
    'nullable' => '', // Specify the attributes that you allow to return null values
    'from' => '2025-01-01T00:00:00Z',
    'to' => '2025-01-31T23:59:59Z',
    'q' => 'bitcoin',
    'page' => 1, // Paid plan only
    'expand' => 'content', // Paid plan only : get the full content of the article
]);

```

## Parameters

| Parameter  | Type    | Description                                                                       |
|------------|---------|-----------------------------------------------------------------------------------|
| `lang`     | string  | Language of the articles (two-letter ISO 639-1 code)                              |
| `country`  | string  | Country of the articles (two-letter ISO 3166-1 code)                              |
| `max`      | integer | Maximum number of articles to return (1-100)                                      |
| `category` | string  | Category of the articles (headlines only)                                         |
| `sortby`   | string  | Sorting method: 'publishedAt' or 'relevance' (search only)                        |
| `from`     | string  | Start date for search (ISO 8601 format, search only)                              |
| `to`       | string  | End date for search (ISO 8601 format, search only)                                |
| `in`       | string  | Where to search: 'title', 'description', 'content' or a combination (search only) |
| `nullable` | boolean | Whether to include null values in the query params                                |
| `page`     | int     | Control the pagination of the results                                             |
| `expand`   | boolean | Whether to get full article content (paid plan only)                              |

## Response Format

All API methods return promises that resolve to objects with the following structure:

```php
{
    "totalArticles": 123,
    "articles": [
        {
            "title": "Article title",
            "description": "Article description",
            "content": "Article content...",
            "url": "https://article-source.com/article",
            "image": "https://article-source.com/image.jpg",
            "publishedAt": "2025-01-01T12:00:00Z",
            "source": {
                "name": "Source Name",
                "url": "https://source.com"
            },
        }
        // ... more articles
    ]
}
```

## Error Handling

The library throws errors in the following cases:
- Missing API key during initialization
- Network errors
- API request timeouts
- API error responses