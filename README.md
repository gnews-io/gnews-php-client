# GNews PHP Client

A PHP client for the GNews.io API, designed to be simple and easy to use.

## Installation

```bash
composer require gnews-io/gnews-php-client
```

## Usage

### Quick Examples

```php
// Using static methods
$searchResults = Gnews::search('bitcoin', [
    'apikey' => 'YOUR_API_KEY'
]);

$headlines = Gnews::headlines([
    'apikey' => 'YOUR_API_KEY', 
    'category' => 'technology'
]);

// Using instance methods
$gnews = new Gnews('YOUR_API_KEY');

$searchResults = $gnews->searchArticles('bitcoin');
$headlines = $gnews->getHeadlines(['category' => 'technology']);
```

## API Methods

### Search Articles

Search for articles with a specific query.

```php
// Static method
$results = Gnews::search('bitcoin', [
    'apikey' => 'YOUR_API_KEY',
    'lang' => 'en',
    'country' => 'us',
    'max' => 10,
    'sortby' => 'publishedAt',  // or 'relevance'
    'from' => '2025-01-01T00:00:00Z',
    'to' => '2025-01-31T23:59:59Z',
    'in' => 'title,description',  // Where to search (title,description,content)
    'nullable' => true,
    'expand' => true,
    'image' => true
]);

// Instance method
$gnews = new Gnews('YOUR_API_KEY');
$results = $gnews->searchArticles('bitcoin', [
    'lang' => 'en',
    'country' => 'us',
    // other parameters...
]);
```

### Headlines

Get top headlines, optionally filtered by category.

```php
// Static method
$headlines = Gnews::headlines([
    'apikey' => 'YOUR_API_KEY',
    'category' => 'technology',  // Optional: general, world, nation, business, technology, entertainment, sports, science, health
    'lang' => 'en',
    'country' => 'us',
    'max' => 10,
    'nullable' => true,
    'expand' => true,
    'image' => true,
    'topic' => 'artificial intelligence'  // Optional - topic to filter headlines
]);

// Instance method
$gnews = new Gnews('YOUR_API_KEY');
$headlines = $gnews->getHeadlines([
    'category' => 'business',
    // other parameters...
]);
```

## Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `apikey` | string | Your GNews API key |
| `lang` | string | Language of the articles (two-letter ISO 639-1 code) |
| `country` | string | Country of the articles (two-letter ISO 3166-1 code) |
| `max` | integer | Maximum number of articles to return (1-100) |
| `category` | string | Category of the articles (headlines only) |
| `sortby` | string | Sorting method: 'publishedAt' or 'relevance' (search only) |
| `from` | string | Start date for search (ISO 8601 format, search only) |
| `to` | string | End date for search (ISO 8601 format, search only) |
| `in` | string | Where to search: 'title', 'description', 'content' or a combination (search only) |
| `nullable` | boolean | Whether to include null values in the query params |
| `expand` | boolean | Whether to show expanded article content |
| `image` | boolean | Whether to include only articles with images |
| `topic` | string | Topic to filter headlines by (headlines only) |

## Error Handling

```php
use Gnews\GnewsPhp\Exception\GnewsException;

try {
    $results = Gnews::search('bitcoin', [
        'apikey' => 'YOUR_API_KEY'
    ]);
} catch (GnewsException $e) {
    echo "Error: " . $e->getMessage();
}
```
